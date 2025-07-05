# Guía de Pruebas - Gym Manager

## Requisitos Previos
- PHP 8.1+
- Node.js 18+
- Composer
- NPM

## Configuración y Ejecución

### Backend (Laravel)
```bash
# Instalar dependencias
composer install

# Configurar base de datos
php artisan migrate
php artisan db:seed

# Ejecutar servidor
php artisan serve
```

### Frontend (Vue 3)
```bash
# Instalar dependencias
npm install

# Ejecutar servidor de desarrollo
npm run dev
```

## Acceso a la Aplicación

### URLs
- **Frontend**: http://localhost:5173
- **Backend API**: http://127.0.0.1:8000/api

### Credenciales de Prueba
- **Administrador**: admin@gym.com / password123 (Acceso completo)
- **Secretario**: secretario@gym.com / password123 (Acceso limitado)

## Funcionalidades a Probar

### 1. Autenticación
- [ ] Login con credenciales válidas
- [ ] Login con credenciales inválidas
- [ ] Logout
- [ ] Redirección automática según estado de autenticación

### 2. Dashboard
- [ ] Visualización de estadísticas
- [ ] Contadores de clientes activos, morosos, próximos a vencer
- [ ] Ingresos del mes
- [ ] Actividad reciente

### 3. Gestión de Clientes
- [ ] Listar clientes
- [ ] Crear nuevo cliente
- [ ] **NUEVO:** Editar cliente existente (solo admin)
- [ ] **NUEVO:** Eliminar cliente (solo admin)
- [ ] Buscar y filtrar clientes
- [ ] Ver clientes morosos
- [ ] Ver clientes próximos a vencer
- [ ] **MEJORADO:** Ver detalle completo de cliente
- [ ] **MEJORADO:** Información de membresía activa con días restantes
- [ ] **MEJORADO:** Historial de asistencias del cliente
- [ ] **MEJORADO:** Estadísticas de asistencia del cliente

### 4. Gestión de Membresías
- [ ] **ADMIN ONLY:** Listar tipos de membresías
- [ ] **ADMIN ONLY:** Crear nueva membresía
- [ ] **ADMIN ONLY:** Editar membresía existente
- [ ] **ADMIN ONLY:** Eliminar membresía
- [ ] **AMBOS:** Asignar membresía a cliente con fecha manual/automática
- [ ] **AMBOS:** Renovar membresía de cliente
- [ ] **NUEVO - ADMIN ONLY:** Cambiar tipo de membresía de cliente
- [ ] **NUEVO - AMBOS:** Ver historial completo de membresías de cliente
- [ ] **MEJORADO:** Cálculo automático de fecha de vencimiento
- [ ] **MEJORADO:** Visualización de días restantes

### 5. **MEJORADO:** Registro de Asistencias
- [ ] **AMBOS:** Registrar ingreso de cliente por ID
- [ ] **AMBOS:** Registrar ingreso por búsqueda rápida (nombre, cédula, correo, teléfono)
- [ ] **AMBOS:** Validación de membresía válida al registrar ingreso
- [ ] **AMBOS:** Opción de permitir ingreso sin membresía válida
- [ ] **AMBOS:** Ver historial de asistencias con filtros (fecha, cliente, hoy)
- [ ] **AMBOS:** Estadísticas de asistencias (diarias, semanales, mensuales)
- [ ] **ADMIN ONLY:** Eliminar asistencia del día actual
- [ ] **MEJORADO:** Control de ingreso único por día
- [ ] **MEJORADO:** Verificación de estado de membresía al ingresar
- [ ] **MEJORADO:** Búsqueda por cédula completa o parcial

### 6. Validaciones y Manejo de Errores
- [ ] Validación de formularios
- [ ] Mensajes de error apropiados
- [ ] Mensajes de éxito
- [ ] Manejo de errores de conexión
- [ ] **NUEVO:** Validación de membresías vencidas
- [ ] **NUEVO:** Advertencias de membresías próximas a vencer

### 7. Enlaces Públicos de Cliente
- [ ] Generar enlace público desde detalle de cliente
- [ ] Generar enlace público desde lista de clientes
- [ ] Copiar enlace al portapapeles
- [ ] Enviar enlace por email (abre cliente de correo)
- [ ] Compartir por WhatsApp (abre WhatsApp Web)
- [ ] Abrir enlace en nueva pestaña
- [ ] Acceder al enlace público sin autenticación
- [ ] Visualizar información personal del cliente
- [ ] Ver estado de membresía actual
- [ ] Consultar historial de asistencias
- [ ] Ver estadísticas personales
- [ ] Ver historial de membresías

### 8. Dashboard Interactivo
- [ ] Hacer clic en tarjeta "Clientes Activos" navega a clientes con filtro activo
- [ ] Hacer clic en tarjeta "Clientes Morosos" navega a clientes morosos
- [ ] Hacer clic en tarjeta "Próximos a Vencer" navega a clientes próximos a vencer

### 9. Vista de Clientes Mejorada
- [ ] Nueva columna "Estado Membresía" con indicadores visuales:
  - Moroso (rojo)
  - Por Vencer (amarillo)
  - Activa (verde)
  - Sin Membresía (gris)
- [ ] Nueva columna "Días Restantes" mostrando:
  - Días restantes si está activa
  - "Vencida hace X días" si está vencida
  - "N/A" si no tiene membresía
- [ ] Filtro adicional por estado de membresía:
  - Todas las membresías
  - Membresía Activa
  - Clientes Morosos
  - Próximos a Vencer (7 días)
  - Sin Membresía

## Control de Permisos por Rol

### Administrador (admin@gym.com)
- ✅ Acceso completo a todas las funcionalidades
- ✅ Ver, crear, editar y eliminar clientes
- ✅ Ver, crear, editar y eliminar membresías
- ✅ Asignar, renovar y cambiar membresías
- ✅ Ver y crear asistencias
- ✅ Eliminar asistencias
- ✅ Ver historial completo de membresías

### Secretario (secretario@gym.com) - Acceso Limitado
- ✅ Ver dashboard
- ✅ Ver y crear clientes
- ❌ **NO puede** editar o eliminar clientes
- ❌ **NO puede** acceder al módulo de membresías directamente
- ✅ Asignar y renovar membresías a clientes
- ❌ **NO puede** cambiar tipo de membresía
- ✅ Ver, crear asistencias
- ❌ **NO puede** eliminar asistencias
- ✅ Ver historial de membresías

## Estructura de Datos de Prueba

### Usuarios
- 1 Administrador (admin@gym.com)
- 1 Secretario (secretario@gym.com)

### Membresías de Prueba
- Básica: $30/mes
- Premium: $50/mes  
- VIP: $80/mes

### Clientes de Prueba
- 10 clientes con diferentes estados de membresía
- Algunos con membresías vencidas
- Algunos próximos a vencer

## API Endpoints

### Autenticación
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Información del usuario autenticado

### Dashboard
- `GET /api/dashboard/estadisticas` - Estadísticas generales
- `GET /api/dashboard/actividad-reciente` - Actividad reciente

### Clientes
- `GET /api/clientes` - Listar clientes
- `POST /api/clientes` - Crear cliente
- `GET /api/clientes/{id}` - Ver cliente
- `PUT /api/clientes/{id}` - Actualizar cliente
- `DELETE /api/clientes/{id}` - Eliminar cliente
- `GET /api/clientes-morosos` - Clientes morosos
- `GET /api/clientes-proximos-vencer` - Próximos a vencer
- `POST /api/asignar-membresia` - Asignar membresía a cliente
- `POST /api/renovar-membresia/{clienteMembresiaId}` - Renovar membresía
- `POST /api/clientes/{cliente}/enlace-publico` - Generar enlace público

### Asistencias
- `GET /api/asistencias` - Listar asistencias con filtros
- `POST /api/asistencias` - Crear asistencia (uso interno)
- `GET /api/asistencias/{id}` - Ver asistencia específica
- `DELETE /api/asistencias/{id}` - Eliminar asistencia (solo del día actual)
- `POST /api/registrar-ingreso` - Registrar ingreso por ID de cliente
- `POST /api/registrar-ingreso-busqueda` - Registrar ingreso por búsqueda
  - Busca por: nombre, cédula, correo, teléfono
  - Ejemplos de búsqueda: "Juan", "12345678", "juan@email.com", "123-456"
- `GET /api/estadisticas-asistencias` - Estadísticas de asistencias

### Enlaces Públicos
- `GET /cliente/{token}` - Vista pública del cliente (sin autenticación)

## Solución de Problemas

### Error 404 en API
- Verificar que Laravel esté ejecutándose en puerto 8000
- Ejecutar `php artisan route:clear` y `php artisan config:clear`

### Error de conexión frontend-backend
- Verificar proxy en vite.config.js
- Reiniciar ambos servidores

### Error de base de datos
- Verificar que existe `database/database.sqlite`
- Ejecutar `php artisan migrate:fresh --seed`

## Notas Técnicas

- **Frontend**: Vue 3 con Composition API, PrimeVue, Vite
- **Backend**: Laravel 10, Sanctum, SQLite
- **Autenticación**: Token-based con Laravel Sanctum
- **Proxy**: Vite proxy para desarrollo (frontend → backend)
