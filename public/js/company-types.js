document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('.company-types-table');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('[data-company-type-row]'));
    const search = document.getElementById('company-type-search');
    const status = document.getElementById('company-type-status');
    const count = document.getElementById('company-types-count');
    const noMatch = document.getElementById('company-types-no-match');

    const update = () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        rows.forEach((row) => {
            const checked = row.querySelector('input[type="checkbox"]').checked;
            const name = row.querySelector('input[type="text"], input:not([type])').value;
            const code = row.cells[0].textContent;
            row.querySelector('.company-types-status').textContent = checked ? 'Active' : 'Inactive';
            const matches = (code + ' ' + name).toLocaleLowerCase().includes(query)
                && (status.value === 'all' || (status.value === 'active') === checked);
            row.hidden = !matches;
            if (matches) visible++;
        });
        noMatch.hidden = visible !== 0 || rows.length === 0;
        count.textContent = visible + ' of ' + rows.length + ' company types';
    };

    search.addEventListener('input', update);
    status.addEventListener('change', update);
    table.addEventListener('input', update);
    table.addEventListener('change', update);
    update();
});
