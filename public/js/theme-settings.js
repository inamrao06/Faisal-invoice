document.addEventListener('DOMContentLoaded', () => {
    const preview = document.getElementById('themePreview');
    if (!preview) return;
    const accent = document.getElementById('accentColor');
    const sidebar = document.getElementById('sidebarColor');
    const header = document.getElementById('headerColor');
    const isDark = (color) => {
        const rgb = [1, 3, 5].map((index) => parseInt(color.slice(index, index + 2), 16));
        return (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000 < 150;
    };
    const render = () => {
        preview.style.setProperty('--preview-accent', accent.value);
        preview.style.setProperty('--preview-sidebar', sidebar.value);
        preview.style.setProperty('--preview-header', header.value);
        preview.style.setProperty('--preview-sidebar-text', isDark(sidebar.value) ? '#fff' : '#26313d');
        preview.style.setProperty('--preview-header-text', isDark(header.value) ? '#fff' : '#26313d');
        preview.classList.toggle('is-dark', document.querySelector('input[name="theme_mode"]:checked')?.value === 'dark');
        document.querySelectorAll('.theme-swatches').forEach((group) => {
            const value = document.getElementById(group.dataset.colorTarget).value.toLowerCase();
            group.querySelectorAll('button').forEach((button) => button.setAttribute('aria-pressed', button.dataset.color === value ? 'true' : 'false'));
        });
    };
    document.querySelectorAll('.theme-swatches button').forEach((button) => button.addEventListener('click', () => {
        const input = document.getElementById(button.parentElement.dataset.colorTarget);
        input.value = button.dataset.color;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }));
    document.querySelectorAll('.theme-settings input').forEach((input) => input.addEventListener('input', render));
    document.querySelector('.theme-settings').addEventListener('submit', () => sessionStorage.removeItem('__THEME_CONFIG__'));
    render();
});
