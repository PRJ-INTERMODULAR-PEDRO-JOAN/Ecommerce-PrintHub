// Selección de elementos
const barraLateral = document.querySelector(".barra-lateral");
const botonAlternar = document.querySelector(".alternar-menu");

// Toggle del menú lateral (abrir/cerrar con el mismo botón)
if (botonAlternar && barraLateral) {
  botonAlternar.addEventListener("click", () => {
    barraLateral.classList.toggle("activa");
  });
}

// Dropdown del menú lateral
const botonDesplegable = document.querySelector(".desplegable"); // Selecciona el <li>

if (botonDesplegable) {
  botonDesplegable.addEventListener("click", (e) => {
    
    // Solo previene la navegación si se hace clic en el enlace (<a>)
    if (e.target.tagName === 'A') {
        e.preventDefault(); 
    }

    // Busca el contenido desplegable DENTRO del <li>
    const contenidoDesplegable = botonDesplegable.querySelector(".contenido-desplegable"); 
    
    if (contenidoDesplegable) {
      contenidoDesplegable.classList.toggle("mostrar");
    }
  });
}

// Mensaje botones productos
document.querySelectorAll('.boton').forEach(button => {
  button.addEventListener('click', () => {
    alert('Estàs veient més informació del producte!');
  });
});