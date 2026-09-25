document.querySelectorAll('[data-confirmacao]').forEach((formulario) => {
    formulario.addEventListener('submit', (evento) => {
        if (!window.confirm(formulario.dataset.confirmacao)) {
            evento.preventDefault();
        }
    });
});

document.querySelectorAll('[data-aviso]').forEach((aviso) => {
    let tempo;
    const fechar = () => {
        window.clearTimeout(tempo);
        aviso.remove();
    };

    aviso.querySelector('[data-fechar-aviso]')?.addEventListener('click', fechar);
    tempo = window.setTimeout(fechar, 5000);
});
