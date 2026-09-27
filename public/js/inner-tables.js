document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.content-page div.card, .content-page div.panel').forEach((box) => {
        const table = box.querySelector('table.table');
        if (!table || table.closest('.card, .panel') !== box) return;

        if (table.parentElement === box) {
            const scroll = document.createElement('div');
            scroll.className = 'table-responsive';
            table.before(scroll);
            scroll.append(table);
        }

        const rows = Array.from(table.tBodies[0]?.rows || []).filter((row) => !row.querySelector('[colspan]'));
        const header = box.querySelector(':scope > .card-header, :scope > .panel-toolbar');
        const pageHead = box.previousElementSibling;

        let tools = null;
        if (header) {
            tools = header.querySelector('[data-panel-tools]');
            if (!tools) {
                tools = document.createElement('div');
                tools.className = 'd-flex align-items-center gap-2 flex-wrap';
                tools.dataset.panelTools = '';
                header.prepend(tools);
                header.classList.add('justify-content-between');
            }
        } else if (box.classList.contains('panel')) {
            tools = document.createElement('div');
            tools.className = 'data-table-toolbar';
            box.prepend(tools);
        }

        const actions = document.createElement('div');
        actions.className = 'd-flex align-items-center gap-1 flex-wrap';

        let search = header?.querySelector('[data-panel-search]');
        if (search) {
            search.classList.add('data-table-search');
        } else if (tools && !box.hasAttribute('data-panel-no-search')) {
            const wrap = document.createElement(tools.classList.contains('data-table-toolbar') ? 'label' : 'div');
            wrap.className = tools.classList.contains('data-table-toolbar') ? 'data-table-search-wrap' : 'app-search';
            search = document.createElement('input');
            search.type = 'search';
            search.className = 'form-control data-table-search';
            search.placeholder = 'Search...';
            search.setAttribute('aria-label', 'Search rows on this page');
            const icon = document.createElement('i');
            icon.className = tools.classList.contains('data-table-toolbar') ? 'ti ti-search' : 'ti ti-search app-search-icon text-muted';
            wrap.append(icon, search);
            tools.append(wrap);
        }

        const statusIndex = Array.from(table.tHead?.rows[0]?.cells || [])
            .findIndex((cell) => cell.textContent.trim().toLowerCase() === 'status');

        let statusFilter = header?.querySelector('[data-panel-status]');
        if (!statusFilter && statusIndex !== -1 && rows.length && tools) {
            statusFilter = document.createElement('select');
            statusFilter.className = 'form-select form-control data-table-status my-1 my-md-0';
            statusFilter.dataset.panelStatus = '';
            statusFilter.setAttribute('aria-label', 'Filter by status');
            statusFilter.add(new Option('All', ''));
            [...new Set(rows.map((row) => row.cells[statusIndex]?.textContent.trim()).filter(Boolean))].sort()
                .forEach((status) => statusFilter.add(new Option(status, status)));
            actions.append(statusFilter);
        }

        if (!header && pageHead?.matches('.ci-header, .page-actions, .ui-header')) {
            const create = pageHead.querySelector('a.btn-primary');
            if (create) actions.append(create);
        }

        if (tools && actions.childElementCount) tools.append(actions);

        let footer = box.querySelector(':scope > .card-footer, :scope > .data-table-footer');
        const pagination = Array.from(box.children)
            .find((child) => child.classList.contains('p-3') && child.querySelector('.pagination'));
        if (!footer && (pagination || tools)) {
            footer = document.createElement('div');
            footer.className = box.classList.contains('panel')
                ? 'data-table-footer'
                : 'card-footer border-top py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap';
            box.append(footer);
        }
        if (footer && pagination) {
            pagination.classList.remove('p-3');
            footer.append(pagination);
        }

        let counter = footer?.querySelector('[data-panel-count]') || footer?.querySelector('.data-table-count');
        if (counter) counter.classList.add('data-table-count');
        if (footer && !counter) {
            counter = document.createElement('span');
            counter.className = 'data-table-count text-muted fs-12';
            footer.prepend(counter);
        }

        const empty = document.createElement('tr');
        empty.className = 'data-table-no-results';
        empty.hidden = true;
        const emptyCell = document.createElement('td');
        emptyCell.colSpan = table.tHead?.rows[0]?.cells.length || 1;
        emptyCell.textContent = 'No matching rows on this page.';
        empty.append(emptyCell);
        table.tBodies[0]?.append(empty);

        const update = () => {
            const query = (search?.value || '').trim().toLocaleLowerCase();
            const status = statusFilter?.value || '';
            let visible = 0;
            rows.forEach((row) => {
                const matches = row.textContent.toLocaleLowerCase().includes(query)
                    && (!status || row.cells[statusIndex]?.textContent.trim() === status);
                row.hidden = !matches;
                if (matches) visible++;
            });
            empty.hidden = visible !== 0 || rows.length === 0;
            if (counter) {
                counter.textContent = query || status
                    ? 'Showing ' + visible + ' of ' + rows.length + ' rows on this page'
                    : 'Showing ' + rows.length + ' row' + (rows.length === 1 ? '' : 's') + ' on this page';
            }
        };

        search?.addEventListener('input', update);
        statusFilter?.addEventListener('change', update);
        update();
    });
});
