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

document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM cargado");
  cargarProductos();
  cargarImpresoras();
});

function cargarProductos() {
  console.log("Cargando productos desde json-server...");
  
  fetch("http://172.16.221.74:3000/productes")
      .then(response => {
          if (!response.ok) {
              throw new Error("No se pudo cargar el JSON desde json-server");
          }
          return response.json();
      })
      .then(data => {
          console.log("Datos recibidos:", data);
          const contenedor = document.getElementById("contenedor-productos");

          data.forEach(producto => {
              const card = document.createElement("div");
              card.classList.add("tarjeta-producto");

              card.innerHTML = `
                  <img src="${producto.img}" alt="${producto.nom}">
                  <h2>${producto.nom}</h2>
                  <p class="producto-descripcion">${producto.descripcio}</p>
                  <span class="producto-precio">${producto.preu.toFixed(2)} €</span>
                  <a href="src/ver_producto.html?id=${producto.id}&tipo=productes" class="boton">Ver Detalles</a>              
                `;

              contenedor.appendChild(card);
          });
      })
      .catch(err => console.error("Error cargando productos:", err));
}

function cargarImpresoras() {
  console.log("Cargando impresoras desde json-server...");
  
  fetch("http://172.16.221.74:3000/impresoras")
      .then(response => {
          if (!response.ok) {
              throw new Error("No se pudo cargar el JSON desde json-server");
          }
          return response.json();
      })
      .then(data => {
          console.log("Datos recibidos:", data);
          const contenedor = document.getElementById("contenedor-impresoras");

          data.forEach(impresora => {
              const card = document.createElement("div");
              card.classList.add("tarjeta-producto");

              card.innerHTML = `
                  <img src="${impresora.img}" alt="${impresora.nom}">
                  <h2>${impresora.nom}</h2>
                  <p class="producto-descripcion">${impresora.descripcio}</p>
                  <span class="producto-precio">${impresora.preu.toFixed(2)} €</span>
                  <a href="src/ver_producto.html?id=${impresora.id}&tipo=impresoras" class="boton">Ver Detalles</a>       
                `;

              contenedor.appendChild(card);
          });
      })
      .catch(err => console.error("Error cargando impresoras:", err));
}

