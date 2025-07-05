# Gym Manager

Sistema de gestión integral para gimnasios desarrollado con Laravel 10 (backend) y Vue 3 (frontend).

## 🚀 Características

### Funcionalidades Principales
- ✅ **Autenticación de usuarios** con roles (admin, secretario)
- ✅ **Gestión de clientes** (CRUD completo)
- ✅ **Gestión de membresías** (crear tipos, asignar, renovar)
- ✅ **Dashboard administrativo** con estadísticas en tiempo real
- ✅ **Control de vencimientos** (clientes morosos y próximos a vencer)
- ✅ **API RESTful** completa para todas las operaciones

### Tecnologías Utilizadas

#### Backend
- **Laravel 10** - Framework PHP moderno
- **Laravel Sanctum** - Autenticación API con tokens
- **SQLite** - Base de datos para desarrollo
- **Eloquent ORM** - Manejo de datos y relaciones

#### Frontend
- **Vue 3** - Framework JavaScript reactivo
- **Composition API** - Nuevo paradigma de Vue 3
- **PrimeVue** - Biblioteca de componentes UI (tema Saga Blue)
- **Vue Router 4** - Navegación SPA
- **Axios** - Cliente HTTP para API
- **Vite** - Build tool rápido

## 🛠️ Instalación y Configuración

### Prerrequisitos
- PHP 8.1+
- Composer
- Node.js 16+
- NPM/Yarn

### 1. Clonar e instalar dependencias
```bash
# Instalar dependencias de Laravel
composer install

# Instalar dependencias de Node.js
npm install
```

### 2. Configurar entorno
```bash
# La configuración ya está lista en el proyecto
# Base de datos SQLite preconfigurada
```

### 3. Configurar base de datos
```bash
# Ejecutar migraciones y seeders
php artisan migrate:fresh --seed
```

### 4. Compilar assets del frontend
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 5. Iniciar servidor de desarrollo
```bash
# Servidor Laravel
php artisan serve

# En otra terminal, servidor de Vite (para desarrollo)
npm run dev
```

## 👥 Usuarios de Prueba

El sistema viene con usuarios preconfigurados:

### Administrador
- **Email:** admin@gym.com
- **Contraseña:** password
- **Permisos:** Acceso completo

### Secretario
- **Email:** secretario@gym.com
- **Contraseña:** password
- **Permisos:** Gestión de clientes y membresías

## 📊 Estructura de la Base de Datos

### Tablas Principales

#### `users`
- Usuarios administrativos del sistema
- Campos: `id`, `name`, `email`, `password`, `role`

#### `clientes`
- Información de los clientes del gimnasio
- Campos: `id`, `nombre`, `cedula`, `correo`, `telefono`, `estado`

#### `membresias`
- Tipos de membresías disponibles
- Campos: `id`, `nombre`, `duracion_dias`, `precio`, `descripcion`, `activa`

#### `cliente_membresia`
- Relación entre clientes y sus membresías
- Campos: `id`, `cliente_id`, `membresia_id`, `fecha_inicio`, `fecha_vencimiento`, `precio_pagado`, `estado_pago`

## 🔗 API Endpoints

### Autenticación
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Obtener usuario actual
- `POST /api/register` - Registrar usuario (solo admin)

### Dashboard
- `GET /api/dashboard/estadisticas` - Estadísticas generales
- `GET /api/dashboard/actividad-reciente` - Actividad reciente

### Clientes
- `GET /api/clientes` - Lista de clientes (con filtros)
- `POST /api/clientes` - Crear cliente
- `GET /api/clientes/{id}` - Obtener cliente
- `PUT /api/clientes/{id}` - Actualizar cliente
- `DELETE /api/clientes/{id}` - Eliminar cliente

### Membresías
- `GET /api/membresias` - Lista de membresías
- `POST /api/membresias` - Crear membresía
- `POST /api/asignar-membresia` - Asignar membresía a cliente

## 🎨 Características del Frontend

### Componentes Principales
- **Layout** - Sidebar de navegación y header
- **Dashboard** - Vista principal con estadísticas
- **Clientes** - Gestión completa de clientes
- **Membresías** - Administración de tipos de membresías
- **Login** - Autenticación de usuarios

### Características de UX/UI
- ✅ Diseño responsivo para móviles y desktop
- ✅ Tema moderno con PrimeVue (Saga Blue)
- ✅ Notificaciones toast para feedback
- ✅ Confirmaciones para acciones destructivas
- ✅ Filtros y búsqueda en tiempo real

## ⚡ Comandos Útiles

```bash
# Reiniciar base de datos con datos de prueba
php artisan migrate:fresh --seed

# Ver rutas de API
php artisan route:list --path=api

# Compilar para producción
npm run build
```

---

**Desarrollado con ❤️ para la gestión eficiente de gimnasios**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
