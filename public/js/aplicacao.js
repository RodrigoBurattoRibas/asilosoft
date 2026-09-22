document.querySelectorAll('[data-confirmacao]').forEach((formulario) => {
    formulario.addEventListener('submit', (evento) => {
        if (!window.confirm(formulario.dataset.confirmacao)) {
            evento.preventDefault();
        }
    });
});
