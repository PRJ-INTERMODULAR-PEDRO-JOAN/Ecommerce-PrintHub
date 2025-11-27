document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loginForm");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const formError = document.getElementById("formError");

    form.addEventListener("submit", function(e) {
        let messages = [];

        // Limpiar errores previos
        formError.textContent = "";
        formError.style.display = "none";

        // Validar username
        if (!username.checkValidity()) {
            messages.push("El nombre de usuario es obligatorio.");
        }

        // Validar password
        if (!password.checkValidity()) {
            messages.push("La contraseña es obligatoria.");
        }

        // Validar reCAPTCHA
        const captchaResponse = grecaptcha.getResponse();
        if (!captchaResponse) {
            messages.push("Debes completar el reCAPTCHA.");
        }

        // Si hay errores, prevenir envío y mostrarlos
        if (messages.length > 0) {
            e.preventDefault();
            formError.innerHTML = messages.map(msg => `• ${msg}`).join("<br>");
            formError.style.display = "block";
        }
    });
});
