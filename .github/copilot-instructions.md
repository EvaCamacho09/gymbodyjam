<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Gym Manager - Instrucciones para Copilot

Este es un proyecto de gestión de gimnasio desarrollado con Laravel (backend) y Vue 3 (frontend).

## Arquitectura del Proyecto

### Backend (Laravel)
- **Framework**: Laravel 10
- **Autenticación**: Laravel Sanctum (API tokens)
- **Base de datos**: SQLite (desarrollo)
- **API**: RESTful API en `/api/*`

### Frontend (Vue 3)
- **Framework**: Vue 3 con Composition API
- **UI Library**: PrimeVue con tema Saga Blue
- **Router**: Vue Router 4
- **HTTP Client**: Axios
- **Build Tool**: Vite

## Estructura de Datos

### Modelos principales:
1. **User**: Usuarios administrativos (admin, secretario)
2. **Cliente**: Clientes del gimnasio
3. **Membresia**: Tipos de membresías
4. **ClienteMembresia**: Relación cliente-membresía con fechas

### Funcionalidades principales:
- Autenticación y autorización por roles
- CRUD de clientes y membresías
- Dashboard con estadísticas
- Control de membresías vencidas y próximas a vencer
- Asignación y renovación de membresías

## Convenciones de Código

### Laravel (Backend):
- Usar Resource Controllers para APIs
- Validaciones en los controladores
- Relaciones Eloquent bien definidas
- Middleware para autorización
- Respuestas JSON consistentes

### Vue 3 (Frontend):
- Composition API en lugar de Options API
- Componentes SFC (Single File Components)
- Props y emit tipados
- Composables para lógica reutilizable
- Naming convention: PascalCase para componentes

### Estilo de código:
- Comentarios en español
- Variables y funciones en camelCase
- Clases en PascalCase
- Constantes en UPPER_CASE

## Patrones de diseño utilizados:
- Repository pattern implícito con Eloquent
- Service layer para lógica de negocio
- Composables para lógica de UI reutilizable
- Event-driven architecture para notificaciones
