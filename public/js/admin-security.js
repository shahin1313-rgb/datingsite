document.addEventListener('submit', (event) => {
    if (event.target.matches('.sensitive-action-form')) {
        return;
    }

    const message = event.target.dataset.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});