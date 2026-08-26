import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function hideGlobalLoader() {
    const loader = document.getElementById('globalLoading');

    if (!loader) {
        return;
    }

    loader.classList.add('loader-hidden');

    window.setTimeout(() => {
        loader.style.display = 'none';
    }, 500);
}

window.addEventListener('load', hideGlobalLoader);
window.setTimeout(hideGlobalLoader, 3000);

document.addEventListener('click', (event) => {
    const historyButton = event.target.closest(
        '[data-history-back]'
    );

    if (historyButton) {
        window.history.back();
        return;
    }

    const sidebarButton = event.target.closest(
        '[data-toggle-sidebar]'
    );

    if (sidebarButton) {
        const sidebar =
            document.getElementById('mySidebar');

        const overlay =
            document.getElementById('myOverlay');

        sidebar?.classList.toggle('sidebar-open');
        overlay?.classList.toggle('hidden');
    }
});

document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});