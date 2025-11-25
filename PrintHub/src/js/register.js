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
