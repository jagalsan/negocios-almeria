import './cookie-consent';

document.addEventListener('click', (event) => {
    const copy = event.target.closest('[data-copy]');
    if (copy) {
        navigator.clipboard.writeText(copy.dataset.copy);
        copy.textContent = 'Código copiado';
    }
});
