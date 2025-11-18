const form = document.getElementById("registerForm");
const formMessage = document.getElementById("formMessage");

if (form) {

    const nom = form.nom;
    const cognoms = form.cognoms;
    const email = form.email;
    const username = form.username;
    const password = form.password;

    const validateForm = () => {
        const errors = [];

        if (nom.value.trim().length < 2)
            errors.push("El nombre debe tener al menos 2 caracteres.");

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value.trim()))
        errors.push("Correo electrónico no válido.");

        if (!username.readOnly && username.value.trim().length < 3)
            errors.push("El nombre de usuario debe tener al menos 3 caracteres.");

        if (!username.readOnly && password.value.trim().length < 6)
            errors.push("La contraseña debe tener al menos 6 caracteres.");

        if (username.readOnly && password.value.trim() !== "" && password.value.trim().length < 6)
            errors.push("La nueva contraseña debe tener al menos 6 caracteres.");

        return errors;
    };

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const errors = validateForm();

        if (errors.length > 0) {
            formMessage.innerHTML = "<div class='error'>" + errors.join("<br>") + "</div>";
            return;
        }

        formMessage.innerHTML = "<div class='success'>Validación correcta. Enviando...</div>";
        form.submit();
    });

}

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

// --- SECCIÓN DEL CARRUSEL ELIMINADA ---