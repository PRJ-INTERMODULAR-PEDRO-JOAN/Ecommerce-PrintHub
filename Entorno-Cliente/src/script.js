// Selección de elementos
const sidebar = document.querySelector(".sidebar");
const toggleBtn = document.querySelector(".menu-toggle");

// Toggle del menú lateral (abrir/cerrar con el mismo botón)
toggleBtn.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// Missatge botons productes
document.querySelectorAll('.btn').forEach(button => {
  button.addEventListener('click', () => {
    alert('Estàs veient més informació del producte!');
  });
});
