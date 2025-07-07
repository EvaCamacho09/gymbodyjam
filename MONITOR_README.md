# Página Pública de Registro de Asistencias - Monitor de Autoservicio

## Descripción
Esta página está diseñada específicamente para ser utilizada en un monitor de autoservicio en el gimnasio, permitiendo a los clientes registrar su asistencia sin necesidad de login administrativo.

## URLs de Acceso
- **Principal**: `http://tu-dominio.com/asistencia`
- **Alternativa**: `http://tu-dominio.com/monitor`

## Funcionalidades

### ✅ Búsqueda Inteligente
- **Autocompletado en tiempo real**: Muestra sugerencias mientras el usuario escribe
- **Búsqueda por nombre o cédula**: Acepta ambos criterios de búsqueda
- **Navegación con teclado**: Flechas arriba/abajo, Enter para seleccionar, Escape para cerrar
- **Selección automática**: Si solo hay un resultado, se selecciona automáticamente

### ✅ Interfaz Touch-Friendly
- **Botones grandes**: Optimizados para pantallas táctiles
- **Texto legible**: Tamaños de fuente aumentados
- **Feedback visual**: Animaciones y efectos hover/active
- **Prevención de zoom**: Evita zoom accidental en dispositivos móviles

### ✅ Registro de Asistencia
- **Un solo toque**: Seleccionar cliente y registrar asistencia inmediatamente
- **Validaciones automáticas**: 
  - Verifica membresía activa
  - Previene doble registro el mismo día
  - Calcula días restantes de membresía

### ✅ Modal de Confirmación
- **Información completa**: Nombre del cliente y estado de membresía
- **Indicadores visuales**:
  - ✅ Verde: Membresía activa (más de 5 días)
  - ⏰ Amarillo: Por vencer (5 días o menos)
  - ⚠️ Rojo: Membresía vencida
- **Auto-cierre**: Se cierra automáticamente después de 5 segundos

### ✅ Gestión de Inactividad
- **Timer de inactividad**: 2 minutos sin actividad
- **Countdown visual**: Muestra 30 segundos de cuenta regresiva
- **Reinicio automático**: Limpia formulario y vuelve al estado inicial
- **Reset por actividad**: Cualquier interacción reinicia el timer

### ✅ Responsive Design
- **Adaptable**: Funciona en diferentes tamaños de pantalla
- **Mobile-first**: Optimizado para tablets y pantallas táctiles
- **Desktop compatible**: También funciona con mouse y teclado

## Casos de Uso

### Escenario 1: Cliente Regular
1. Cliente toca la pantalla del monitor
2. Escribe su nombre o cédula
3. Aparecen sugerencias en tiempo real
4. Selecciona su nombre
5. Ve confirmación con días restantes de membresía

### Escenario 2: Cliente con Membresía por Vencer
1. Cliente registra asistencia
2. Modal muestra alerta amarilla con días restantes
3. Se sugiere renovar pronto la membresía

### Escenario 3: Cliente con Membresía Vencida
1. Cliente intenta registrar asistencia
2. Sistema permite el registro pero muestra alerta roja
3. Se solicita renovación de membresía

### Escenario 4: Inactividad del Monitor
1. Monitor sin uso por 2 minutos
2. Aparece countdown de 30 segundos
3. Se reinicia automáticamente al estado inicial
4. Listo para el siguiente usuario

## Validaciones de Seguridad

### ✅ Restricciones de Acceso
- **Solo clientes activos**: Solo muestra clientes con estado 'activo'
- **Membresía requerida**: Requiere membresía activa para registrar asistencia
- **Una asistencia por día**: Previene múltiples registros el mismo día

### ✅ Protección CSRF
- **Token de seguridad**: Todas las peticiones incluyen token CSRF
- **Validación server-side**: Laravel valida todos los requests

## Configuración para Producción

### Monitor Dedicado
1. **Navegador en modo kiosco**: Usar Chrome/Firefox en pantalla completa
2. **Bookmark**: Guardar la URL como página de inicio
3. **Desactivar interacciones**: Bloquear acceso a menús del navegador
4. **Pantalla táctil**: Configurar para uso touch

### Optimizaciones Recomendadas
1. **Cache del navegador**: Configurar para cargar rápido
2. **Conexión estable**: Asegurar WiFi/Ethernet confiable
3. **Backup de energia**: UPS para evitar cortes
4. **Mantenimiento**: Limpiar pantalla regularmente

## Troubleshooting

### Problema: No aparecen clientes
- **Verificar**: Conexión a internet
- **Revisar**: Estado de clientes en base de datos
- **Comprobar**: Configuración del servidor Laravel

### Problema: Error al registrar asistencia
- **Verificar**: Cliente tiene membresía activa
- **Revisar**: No haya registrado asistencia hoy
- **Comprobar**: Logs del servidor para errores

### Problema: Página no responde
- **Refrescar**: F5 o recargar página
- **Verificar**: Servidor Laravel ejecutándose
- **Revisar**: Conexión de red

## Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Backend**: Laravel (PHP)
- **Base de datos**: SQLite (configurable)
- **Estilos**: CSS Grid, Flexbox, Gradientes
- **Interacciones**: Touch events, Keyboard navigation

## Mantenimiento

### Revisiones Regulares
- **Semanalmente**: Verificar funcionamiento general
- **Mensualmente**: Revisar logs de errores
- **Trimestralmente**: Actualizar datos de prueba

### Actualizaciones
- **Backend**: Mantener Laravel actualizado
- **Frontend**: Revisar compatibilidad con navegadores
- **Seguridad**: Aplicar parches de seguridad

---

**¡La página está lista para ser utilizada en un monitor de autoservicio!** 🚀

Accede a través de: `http://tu-dominio.com/asistencia` o `http://tu-dominio.com/monitor`
