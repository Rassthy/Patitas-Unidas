<div align="center">

<img src="https://img.shields.io/badge/version-1.0.0--MVP-ff6b6b?style=for-the-badge" alt="Version">
<img src="https://img.shields.io/badge/estado-MVP%20listo-4ecdc4?style=for-the-badge" alt="Estado">
<img src="https://img.shields.io/badge/licencia-MIT-f7dc6f?style=for-the-badge" alt="Licencia">

# 🐾 Patitas Unidas

**La plataforma social para quienes luchan por los animales.**  
Adopta, publica, conecta y apoya — todo en un mismo lugar.

---

</div>

## ¿Qué es Patitas Unidas?

Patitas Unidas es una aplicación web desarrollada con **Laravel + PHP** orientada a la comunidad de protección animal. Permite a protectoras, voluntarios y particulares publicar mascotas en adopción, reportar animales perdidos, conectar mediante chat privado y apoyar causas a través de donaciones.

El proyecto nació como MVP funcional con todas las bases para escalar a una plataforma real de impacto social.

---

## ✨ Funcionalidades principales

- 🔐 **Autenticación completa** — registro, login, recuperación de contraseña
- 🐶 **Gestión de mascotas** — ficha completa con fotos, vacunas y recordatorios
- 📢 **Foro por categorías** — adopción, mascotas perdidas y apoyo a animales
- 💬 **Chat privado** entre usuarios
- 🔔 **Sistema de notificaciones** en tiempo real
- ❤️ **Donaciones con PayPal** integrado
- 🚨 **Sistema de reportes** de contenido
- 👤 **Perfiles públicos** con valoraciones entre usuarios

---

## 🛠️ Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8+ · Laravel 11 |
| Frontend | Blade · Bootstrap 5 · JavaScript |
| Base de datos | MySQL |
| Autenticación | Laravel Auth |
| Pagos | PayPal SDK (Sandbox) |
| Correo | Gmail SMTP |
| Estilos | CSS personalizado · Bootstrap |

---

## 🚀 Instalación local

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/patitas-unidas.git
cd patitas-unidas

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install && npm run build

# 4. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales de BD, mail y PayPal

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Enlazar almacenamiento
php artisan storage:link

# 8. Arrancar el servidor
php artisan serve
```

Accede en `http://localhost:8000` 🎉

---

## 📁 Estructura del proyecto

```
patitas-unidas/
├── app/
│   ├── Http/Controllers/   # Lógica de cada módulo
│   ├── Models/             # Modelos Eloquent
│   └── Providers/
├── database/
│   ├── migrations/         # Estructura de la BD
│   └── seeders/            # Datos iniciales (categorías)
├── public/                 # Assets públicos
├── resources/
│   ├── views/              # Plantillas Blade
│   ├── css/
│   └── js/
└── routes/
    └── web.php             # Todas las rutas de la app
```

---

## 🌐 Demo

> 🚧 Despliegue en producción próximamente — el enlace se actualizará aquí.

---

## 👥 Equipo

Desarrollado con 🧡 por:

| Nombre | GitHub |
|---|---|
| Leonardo Calderón | [@Rassthy](https://github.com/Rassthy) |
| Jonathan Diez | [@JonathanDiez](https://github.com/JonathanDiez) |
| Alejandro Fraile | [@alexfraile](https://github.com/alexfraile) |

---

<div align="center">

**Patitas Unidas** · Hecho con ❤️ para los animales

</div>