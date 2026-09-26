document.addEventListener('DOMContentLoaded', () => {
    if (!window.jQuery || !jQuery.fn.select2) return;

    const enhanceSelects = (root) => {
        const selects = root.matches?.('select') ? [root] : root.querySelectorAll?.('select') || [];
        selects.forEach((select) => {
            if (select.classList.contains('select2-hidden-accessible') || select.closest('.select2-container')) return;
            const label = select.labels?.[0]?.textContent?.trim().replace(/\s*\*$/, '') || select.name.replaceAll('_', ' ');
            const $select = jQuery(select);
            $select.select2({
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownParent: $select.closest('.modal, .offcanvas').length ? $select.closest('.modal, .offcanvas') : jQuery(document.body),
                language: { noResults: () => 'No matches found', searching: () => 'Searching...' }
            });
            $select.next('.select2-container').find('.select2-selection').attr('aria-label', label);
            select.addEventListener('invalid', () => {
                $select.next('.select2-container').addClass('is-invalid');
                $select.select2('open');
            });
            $select.on('change', () => $select.next('.select2-container').removeClass('is-invalid'));
        });
    };

    const content = document.querySelector('.content-page');
    if (!content) return;
    enhanceSelects(content);
    new MutationObserver((mutations) => {
        mutations.forEach(({ addedNodes }) => addedNodes.forEach((node) => {
            if (node.nodeType === 1 && !node.closest('.select2-container')) enhanceSelects(node);
        }));
    }).observe(content, { childList: true, subtree: true });
});
