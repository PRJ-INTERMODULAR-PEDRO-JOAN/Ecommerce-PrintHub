 // Script per a interacció bàsica, com per exemple mostrar un missatge quan s'hi fa clic en el botó de veure més
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', () => {
        alert('Estàs veient més informació del producte!');
    });
});

