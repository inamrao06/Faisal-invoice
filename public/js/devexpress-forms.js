(function () {
    function enhanceForm(form) {
        if (form.dataset.dxFormReady === '1' || form.dataset.dxSkip === '1') {
            return;
        }

        var controls = form.querySelectorAll('.form-control, .form-select, .form-check-input');
        if (!controls.length) {
            return;
        }

        form.dataset.dxFormReady = '1';
        form.classList.add('app-dx-form');

        controls.forEach(function (control) {
            control.classList.add('app-dx-editor');

            var field = control.closest('.mb-3, .mb-2, .col-md-6, .col-md-4, .col-md-3, .col-lg-6, .col-lg-4, .col-lg-3, .form-group, .dx-field, .vi-card .vi-body > div, .form-grid > div');
            if (field && !field.classList.contains('form-check') && !field.classList.contains('input-group')) {
                field.classList.add('app-dx-field');
            }
        });

        form.querySelectorAll('label').forEach(function (label) {
            label.classList.add('app-dx-label');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(enhanceForm);
    });
})();
