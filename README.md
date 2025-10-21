# 🖨️ Printhub

Bienvenido al repositorio de **Printhub**, una solución integral para la gestión e impresión eficiente de proyectos.  
Este repositorio contiene tanto el entorno **cliente** como el **servidor**, junto con la infraestructura necesaria para el correcto funcionamiento del sistema.

---

## 🧩 Estructura del Proyecto

Printhub/
├── Entorno-Cliente/ # Aplicación Frontend (Vite)
├── Entorno-Servidor/ # Backend con Docker
├── Documentacion-Riesgos-Laborables # Documentos necesarios para mantener precaución
└── README.md # Este archivo


---

## 🚀 Inicialización del entorno de desarrollo

### 🖥️ Entorno Cliente

1. Abre una terminal en la carpeta del proyecto:

   cd Entorno-Cliente

2. Lanza el entorno de desarrollo:

    npm run dev

Una vez levantado, puedes acceder al sitio web en tu navegador en:
👉 http://localhost:5173

⚙️ Entorno Servidor
1. Abre una terminal en la carpeta del servidor:

    cd Entorno-Servidor

2. Levanta los contenedores Docker con:

sudo docker compose up -d

Esto iniciará los servicios necesarios para que el servidor pueda realizar la validación correcta y mantener la comunicación con el entorno cliente.

🗂️ Kanban del Proyecto
Consulta el progreso, tareas y planificación del proyecto en nuestro tablero Kanban:

🔗 Ver tablero en GitHub Projects → [![Kanban Board]](https://github.com/orgs/PRJ-INTERMODULAR-PEDRO-JOAN/projects/1)


🧠 Tecnologías principales

Frontend: Vite

Backend: Docker --> Nginx

Infraestructura: Docker & Docker Compose

Gestión: GitHub Projects (Kanban)