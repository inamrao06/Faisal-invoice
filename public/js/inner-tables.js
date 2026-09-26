document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.content-page div.panel').forEach((panel) => {
        const table = panel.querySelector('table.table');
        if (!table) return;
        if (table.parentElement === panel) {
            const scroll = document.createElement('div');
            scroll.className = 'table-responsive';
            table.before(scroll);
            scroll.append(table);
        }
        if (panel.querySelector('.panel-toolbar')) {
            const count = table.tBodies[0]?.querySelectorAll('tr:not(:has([colspan]))').length || 0;
            const footer = document.createElement('div');
            footer.className = 'data-table-footer';
            const label = document.createElement('span');
            label.textContent = 'Showing ' + count + ' row' + (count === 1 ? '' : 's') + ' on this page';
            footer.append(label);
            const pagination = Array.from(panel.children).find((child) => child.classList.contains('p-3') && child.querySelector('.pagination'));
            if (pagination) {
                pagination.classList.remove('p-3');
                footer.append(pagination);
            }
            panel.append(footer);
            return;
        }
        const rows = Array.from(table.tBodies[0]?.rows || []).filter((row) => !row.querySelector('[colspan]'));
        const header = panel.previousElementSibling;
        const toolbar = document.createElement('div');
        toolbar.className = 'data-table-toolbar';
        const search = document.createElement('input');
        search.type = 'search';
        search.className = 'form-control data-table-search';
        search.placeholder = 'Search this page';
        search.setAttribute('aria-label', 'Search table rows on this page');
        const searchWrap = document.createElement('label');
        searchWrap.className = 'data-table-search-wrap';
        const searchIcon = document.createElement('i');
        searchIcon.className = 'ti ti-search';
        searchWrap.append(searchIcon, search);
        toolbar.append(searchWrap);
        const actions = document.createElement('div');
        actions.className = 'data-table-actions';
        const statusIndex = Array.from(table.tHead?.rows[0]?.cells || []).findIndex((cell) => cell.textContent.trim().toLowerCase() === 'status');
        let statusFilter;
        if (statusIndex !== -1 && rows.length) {
            statusFilter = document.createElement('select');
            statusFilter.className = 'form-select data-table-status';
            statusFilter.setAttribute('aria-label', 'Filter table status on this page');
            statusFilter.add(new Option('All statuses', ''));
            [...new Set(rows.map((row) => row.cells[statusIndex]?.textContent.trim()).filter(Boolean))].sort()
                .forEach((status) => statusFilter.add(new Option(status, status)));
            actions.append(statusFilter);
        }
        if (header?.matches('.ci-header, .page-actions, .ui-header')) {
            const create = header.querySelector('a.btn-primary');
            if (create) actions.append(create);
        }
        toolbar.append(actions);
        panel.prepend(toolbar);
        const footer = document.createElement('div');
        footer.className = 'data-table-footer';
        const count = document.createElement('span');
        count.className = 'data-table-count';
        footer.append(count);
        const pagination = Array.from(panel.children).find((child) => child.classList.contains('p-3') && child.querySelector('.pagination'));
        if (pagination) {
            pagination.classList.remove('p-3');
            footer.append(pagination);
        }
        panel.append(footer);
        const empty = document.createElement('tr');
        empty.className = 'data-table-no-results';
        empty.hidden = true;
        const emptyCell = document.createElement('td');
        emptyCell.colSpan = table.tHead?.rows[0]?.cells.length || 1;
        emptyCell.textContent = 'No matching rows on this page.';
        empty.append(emptyCell);
        table.tBodies[0]?.append(empty);
        const update = () => {
            const query = search.value.trim().toLocaleLowerCase();
            const status = statusFilter?.value || '';
            let visible = 0;
            rows.forEach((row) => {
                const matches = row.textContent.toLocaleLowerCase().includes(query)
                    && (!status || row.cells[statusIndex]?.textContent.trim() === status);
                row.hidden = !matches;
                if (matches) visible++;
            });
            empty.hidden = visible !== 0 || rows.length === 0;
            count.textContent = query || status
                ? 'Showing ' + visible + ' of ' + rows.length + ' rows on this page'
                : 'Showing ' + rows.length + ' row' + (rows.length === 1 ? '' : 's') + ' on this page';
        };
        search.addEventListener('input', update);
        statusFilter?.addEventListener('change', update);
        update();
    });
});
