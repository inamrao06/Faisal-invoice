(function () {
    function textFrom(element) {
        return (element.innerText || element.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function makeField(index) {
        return 'col_' + index;
    }

    function buildColumns(table) {
        return Array.from(table.querySelectorAll('thead th')).map(function (header, index) {
            var alignRight = header.classList.contains('text-end') || header.classList.contains('text-right');
            return {
                caption: textFrom(header) || ' ',
                dataField: makeField(index),
                alignment: alignRight ? 'right' : 'left',
                allowFiltering: true,
                allowHeaderFiltering: true,
                allowSorting: true,
                cellTemplate: function (container, options) {
                    window.jQuery(container).html(options.data['_html_' + makeField(index)] || '');
                }
            };
        });
    }

    function buildRows(table, columnCount) {
        return Array.from(table.querySelectorAll('tbody tr')).reduce(function (rows, row, rowIndex) {
            var cells = Array.from(row.children);
            if (cells.length !== columnCount || cells.some(function (cell) { return cell.hasAttribute('colspan'); })) {
                return rows;
            }

            var item = { id: rowIndex };
            cells.forEach(function (cell, cellIndex) {
                var field = makeField(cellIndex);
                item[field] = textFrom(cell);
                item['_html_' + field] = cell.innerHTML;
            });
            rows.push(item);
            return rows;
        }, []);
    }

    function enhance(table) {
        if (!window.jQuery || !window.DevExpress || table.dataset.dxReady === '1' || table.dataset.dxSkip === '1') {
            return;
        }

        if (table.closest('form') || table.classList.contains('invoice-table') || table.classList.contains('items-table')) {
            return;
        }

        var columns = buildColumns(table);
        if (!columns.length) {
            return;
        }

        var rows = buildRows(table, columns.length);
        if (!rows.length) {
            return;
        }

        table.dataset.dxReady = '1';
        var grid = document.createElement('div');
        grid.className = 'app-dx-grid';
        table.parentNode.insertBefore(grid, table);
        table.style.display = 'none';

        window.jQuery(grid).dxDataGrid({
            dataSource: rows,
            keyExpr: 'id',
            columns: columns,
            columnAutoWidth: true,
            allowColumnResizing: true,
            columnResizingMode: 'widget',
            showBorders: false,
            showColumnLines: false,
            rowAlternationEnabled: false,
            hoverStateEnabled: true,
            filterRow: {
                visible: true,
                applyFilter: 'auto'
            },
            headerFilter: {
                visible: true
            },
            searchPanel: {
                visible: true,
                width: 260,
                placeholder: 'Search'
            },
            sorting: {
                mode: 'multiple'
            },
            paging: {
                pageSize: Number(table.dataset.dxPageSize || 15)
            },
            pager: {
                visible: true,
                showInfo: true,
                showNavigationButtons: true,
                showPageSizeSelector: true,
                allowedPageSizes: [10, 15, 25, 50, 100]
            },
            columnChooser: {
                enabled: true,
                mode: 'select'
            },
            noDataText: 'No records found'
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('table[data-dx-grid], .table-responsive > table.table').forEach(enhance);
    });
})();
