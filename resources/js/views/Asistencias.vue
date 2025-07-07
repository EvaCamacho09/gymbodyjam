<template>
  <div class="asistencias">
    <div class="page-header">
      <h1>Registro de Asistencias</h1>
      <div class="header-actions">
        <Button
          icon="pi pi-plus"
          label="Registrar Ingreso"
          @click="mostrarDialogoIngreso = true"
          class="p-button-success"
        />
      </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
      <Card class="stat-card">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-calendar-times"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.asistencias_hoy || 0 }}</h3>
              <p>Ingresos Hoy</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-chart-line"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.asistencias_semana || 0 }}</h3>
              <p>Ingresos Semana</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-users"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.clientes_activos_hoy || 0 }}</h3>
              <p>Clientes Únicos Hoy</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-chart-bar"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.promedio_diario_mes || 0 }}</h3>
              <p>Promedio Diario</p>
            </div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Filtros -->
    <Card class="filtros-card">
      <template #content>
        <div class="filtros">
          <div class="filtro-item">
            <label>Buscar Cliente</label>
            <InputText
              v-model="filtros.busqueda"
              placeholder="Nombre, cédula, correo o teléfono"
              @input="debounceSearch"
            />
          </div>
          <div class="filtro-item">
            <label>Fecha</label>
            <Calendar
              v-model="filtros.fecha"
              dateFormat="dd/mm/yy"
              @date-select="aplicarFiltros"
              showIcon
            />
          </div>
          <div class="filtro-item">
            <Button
              label="Hoy"
              :class="{ 'p-button-outlined': !filtros.hoy }"
              @click="filtrarHoy"
            />
          </div>
          <div class="filtro-item">
            <Button
              label="Limpiar"
              class="p-button-secondary p-button-outlined"
              @click="limpiarFiltros"
            />
          </div>
        </div>
      </template>
    </Card>

    <!-- Tabla de asistencias -->
    <Card>
      <template #content>
        <DataTable
          :value="asistencias"
          :loading="loading"
          paginator
          :rows="20"
          responsiveLayout="scroll"
          stripedRows
        >
          <Column field="cliente.nombre" header="Cliente" sortable>
            <template #body="{ data }">
              <div class="cliente-info">
                <strong>{{ data.cliente.nombre }}</strong>
                <small>{{ data.cliente.cedula }}</small>
              </div>
            </template>
          </Column>
          
          <Column field="fecha_ingreso" header="Fecha y Hora" sortable>
            <template #body="{ data }">
              {{ formatearFechaHora(data.fecha_ingreso) }}
            </template>
          </Column>
          
          <Column field="clienteMembresia" header="Membresía">
            <template #body="{ data }">
              <div v-if="data.cliente_membresia">
                <span class="membresia-badge">
                  {{ data.cliente_membresia.membresia.nombre }}
                </span>
                <small class="membresia-venc">
                  Vence: {{ formatearFecha(data.cliente_membresia.fecha_vencimiento) }}
                </small>
              </div>
              <span v-else class="sin-membresia">Sin membresía</span>
            </template>
          </Column>
          
          <Column field="membresia_valida" header="Estado">
            <template #body="{ data }">
              <Tag
                :value="data.membresia_valida ? 'Válida' : 'Vencida'"
                :severity="data.membresia_valida ? 'success' : 'danger'"
              />
            </template>
          </Column>
          
          <Column field="observaciones" header="Observaciones">
            <template #body="{ data }">
              {{ data.observaciones || '-' }}
            </template>
          </Column>
          
          <Column header="Acciones">
            <template #body="{ data }">
              <Button
                v-if="permissions.canDeleteAsistencias"
                icon="pi pi-trash"
                class="p-button-rounded p-button-text p-button-danger p-button-sm"
                @click="confirmarEliminar(data)"
                v-tooltip="'Eliminar'"
              />
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Diálogo para registrar ingreso -->
    <Dialog
      v-model:visible="mostrarDialogoIngreso"
      header="Registrar Ingreso"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <div class="dialog-content">
        <div class="form-group">
          <label>Buscar Cliente</label>
          <InputText
            v-model="formularioIngreso.busqueda"
            placeholder="Nombre, cédula, correo o teléfono del cliente"
            @input="buscarClientes"
            :class="{ 'p-invalid': errores.busqueda }"
          />
          <small v-if="errores.busqueda" class="p-error">{{ errores.busqueda }}</small>
        </div>

        <!-- Resultados de búsqueda -->
        <div v-if="clientesEncontrados.length > 0" class="clientes-encontrados">
          <h4>Clientes encontrados:</h4>
          <div
            v-for="cliente in clientesEncontrados"
            :key="cliente.id"
            class="cliente-item"
            @click="seleccionarCliente(cliente)"
          >
            <div class="cliente-info">
              <strong>{{ cliente.nombre }}</strong>
              <small>{{ cliente.email }} | {{ cliente.telefono }}</small>
              <div class="cliente-estado">
                <Tag
                  :value="cliente.es_moroso ? 'Moroso' : 'Activo'"
                  :severity="cliente.es_moroso ? 'danger' : 'success'"
                />
                <span v-if="cliente.dias_restantes > 0">
                  {{ cliente.dias_restantes }} días restantes
                </span>
                <span v-else-if="cliente.dias_restantes === 0">
                  Vence hoy
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Cliente seleccionado -->
        <div v-if="clienteSeleccionado" class="cliente-seleccionado">
          <h4>Cliente seleccionado:</h4>
          <div class="cliente-card">
            <strong>{{ clienteSeleccionado.nombre }}</strong>
            <div class="cliente-detalles">
              <span>Días restantes: {{ clienteSeleccionado.dias_restantes }}</span>
              <Tag
                :value="clienteSeleccionado.es_moroso ? 'Moroso' : 'Activo'"
                :severity="clienteSeleccionado.es_moroso ? 'danger' : 'success'"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="checkbox-group">
            <Checkbox
              v-model="formularioIngreso.permitirSinMembresia"
              :binary="true"
              inputId="permitir_sin_membresia"
            />
            <label for="permitir_sin_membresia">
              Permitir ingreso sin membresía válida
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>Observaciones</label>
          <Textarea
            v-model="formularioIngreso.observaciones"
            rows="3"
            placeholder="Observaciones adicionales (opcional)"
          />
          
          <!-- Información de membresía -->
          <div v-if="clienteSeleccionado && clienteSeleccionado.dias_restantes !== undefined" class="membresia-info">
            <div v-if="clienteSeleccionado.dias_restantes > 0" class="membresia-activa">
              <strong>✅ Membresía válida - {{ clienteSeleccionado.dias_restantes }} días restantes</strong>
            </div>
            <div v-else-if="clienteSeleccionado.dias_restantes === 0" class="membresia-vence-hoy">
              <strong>⚠️ La membresía vence HOY</strong>
            </div>
            <div v-else class="membresia-vencida">
              <strong>❌ Membresía vencida hace {{ Math.abs(clienteSeleccionado.dias_restantes) }} días</strong>
            </div>
          </div>
          <div v-else-if="clienteSeleccionado && clienteSeleccionado.dias_restantes === undefined" class="sin-membresia-info">
            <strong>❌ Cliente sin membresía activa</strong>
          </div>
        </div>
      </div>

      <template #footer>
        <Button
          label="Cancelar"
          class="p-button-text"
          @click="cerrarDialogoIngreso"
        />
        <Button
          label="Registrar Ingreso"
          :loading="guardandoIngreso"
          @click="registrarIngreso"
          :disabled="!clienteSeleccionado"
        />
      </template>
    </Dialog>

    <!-- Diálogo de confirmación -->
    <ConfirmDialog />
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { usePermissions } from '../composables/usePermissions';
import api from '../services/api';

export default {
  name: 'Asistencias',
  setup() {
    const toast = useToast();
    const confirm = useConfirm();
    const { permissions } = usePermissions();
    
    // Estado reactivo
    const asistencias = ref([]);
    const estadisticas = ref({});
    const loading = ref(false);
    const mostrarDialogoIngreso = ref(false);
    const guardandoIngreso = ref(false);
    const clientesEncontrados = ref([]);
    const clienteSeleccionado = ref(null);
    
    const filtros = reactive({
      busqueda: '',
      fecha: null,
      hoy: true
    });
    
    const formularioIngreso = reactive({
      busqueda: '',
      permitirSinMembresia: false,
      observaciones: ''
    });
    
    const errores = reactive({
      busqueda: ''
    });

    // Métodos
    const cargarAsistencias = async () => {
      try {
        loading.value = true;
        const params = {};
        
        if (filtros.busqueda) params.cliente_id = filtros.busqueda;
        if (filtros.fecha) params.fecha = formatearFechaParaAPI(filtros.fecha);
        if (filtros.hoy) params.hoy = true;
        
        const response = await api.getAsistencias(params);
        asistencias.value = response.data || response;
      } catch (error) {
        console.error('Error loading asistencias:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al cargar asistencias',
          life: 3000
        });
      } finally {
        loading.value = false;
      }
    };

    const cargarEstadisticas = async () => {
      try {
        const response = await api.getEstadisticasAsistencias();
        estadisticas.value = response;
      } catch (error) {
        console.error('Error loading statistics:', error);
      }
    };

    const buscarClientes = async () => {
      if (formularioIngreso.busqueda.length < 3) {
        clientesEncontrados.value = [];
        return;
      }

      try {
        const response = await api.get('/clientes', {
          params: { search: formularioIngreso.busqueda }
        });
        
        if (response.data) {
          clientesEncontrados.value = response.data.map(cliente => {
            let diasRestantes = undefined;
            let esMoroso = true;
            
            if (cliente.membresias && cliente.membresias.length > 0) {
              // Encontrar la membresía más reciente (con fecha de vencimiento más lejana)
              const membresiaActiva = cliente.membresias.reduce((latest, current) => {
                const currentVenc = new Date(current.pivot.fecha_vencimiento);
                const latestVenc = new Date(latest.pivot.fecha_vencimiento);
                return currentVenc > latestVenc ? current : latest;
              });
              
              const vencimiento = new Date(membresiaActiva.pivot.fecha_vencimiento);
              const hoy = new Date();
              hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
              vencimiento.setHours(0, 0, 0, 0); // Normalizar a medianoche
              
              diasRestantes = Math.ceil((vencimiento - hoy) / (1000 * 60 * 60 * 24));
              esMoroso = diasRestantes < 0;
            }
            
            return {
              id: cliente.id,
              nombre: cliente.nombre,
              cedula: cliente.cedula,
              correo: cliente.correo,
              telefono: cliente.telefono,
              dias_restantes: diasRestantes,
              es_moroso: esMoroso
            };
          });
        }
      } catch (error) {
        console.error('Error buscando clientes:', error);
        clientesEncontrados.value = [];
      }
    };

    const seleccionarCliente = (cliente) => {
      clienteSeleccionado.value = cliente;
      clientesEncontrados.value = [];
      formularioIngreso.busqueda = cliente.nombre;
    };

    const registrarIngreso = async () => {
      if (!clienteSeleccionado.value) {
        errores.busqueda = 'Debe seleccionar un cliente';
        return;
      }

      try {
        guardandoIngreso.value = true;
        
        const response = await api.registrarIngreso({
          cliente_id: clienteSeleccionado.value.id,
          permitir_sin_membresia: formularioIngreso.permitirSinMembresia,
          observaciones: formularioIngreso.observaciones
        });

        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: response?.message || 'Ingreso registrado exitosamente',
          life: 3000
        });

        // Cerrar modal y recargar datos
        cerrarDialogoIngreso();
        cargarAsistencias();
        cargarEstadisticas();
      } catch (error) {
        console.error('Error registrando ingreso:', error);
        
        // Verificar si es error de membresía inválida
        if (error.response?.data?.membresia_invalida && error.response?.data?.requiere_permiso) {
          toast.add({
            severity: 'warn',
            summary: 'Membresía Requerida',
            detail: 'El cliente no tiene una membresía válida. Active la opción "Permitir ingreso sin membresía válida" para continuar.',
            life: 7000
          });
          // NO cerramos el modal para que el usuario pueda activar la opción
        } else if (error.response?.data?.ya_ingreso) {
          // Cliente ya ingresó hoy
          toast.add({
            severity: 'info',
            summary: 'Ya registrado',
            detail: error.response.data.message,
            life: 5000
          });
          cerrarDialogoIngreso(); // Cerrar porque no hay nada que el usuario pueda hacer
        } else {
          // Otros errores
          const message = error.response?.data?.message || 'Error al registrar ingreso';
          toast.add({
            severity: 'error',
            summary: 'Error',
            detail: message,
            life: 5000
          });
          // Para errores generales, también cerramos el modal
          cerrarDialogoIngreso();
        }
      } finally {
        guardandoIngreso.value = false;
      }
    };

    const confirmarEliminar = (asistencia) => {
      confirm.require({
        message: '¿Está seguro de eliminar esta asistencia?',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: async () => {
          await eliminarAsistencia(asistencia.id);
        },
        reject: () => {
          // No hacer nada si se cancela
        }
      });
    };

    const eliminarAsistencia = async (id) => {
      try {
        await api.deleteAsistencia(id);
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Asistencia eliminada exitosamente',
          life: 3000
        });
        // Recargar datos después de eliminar
        await cargarAsistencias();
        await cargarEstadisticas();
      } catch (error) {
        console.error('Error eliminando asistencia:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: error.response?.data?.message || 'Error al eliminar asistencia',
          life: 3000
        });
      }
    };

    const cerrarDialogoIngreso = () => {
      mostrarDialogoIngreso.value = false;
      formularioIngreso.busqueda = '';
      formularioIngreso.permitirSinMembresia = false;
      formularioIngreso.observaciones = '';
      clientesEncontrados.value = [];
      clienteSeleccionado.value = null;
      errores.busqueda = '';
    };

    const filtrarHoy = () => {
      filtros.hoy = !filtros.hoy;
      if (filtros.hoy) {
        filtros.fecha = null;
      }
      cargarAsistencias();
    };

    const aplicarFiltros = () => {
      if (filtros.fecha) {
        filtros.hoy = false;
      }
      cargarAsistencias();
    };

    const limpiarFiltros = () => {
      filtros.busqueda = '';
      filtros.fecha = null;
      filtros.hoy = true;
      cargarAsistencias();
    };

    // Utilidades
    const formatearFechaHora = (fecha) => {
      return new Date(fecha).toLocaleString('es-ES');
    };

    const formatearFecha = (fecha) => {
      return new Date(fecha).toLocaleDateString('es-ES');
    };

    const formatearFechaParaAPI = (fecha) => {
      return fecha.toISOString().split('T')[0];
    };

    const esHoy = (fecha) => {
      const hoy = new Date();
      const fechaAsistencia = new Date(fecha);
      return hoy.toDateString() === fechaAsistencia.toDateString();
    };

    const debounceSearch = (() => {
      let timeout;
      return () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          cargarAsistencias();
        }, 500);
      };
    })();

    // Lifecycle
    onMounted(() => {
      cargarAsistencias();
      cargarEstadisticas();
    });

    return {
      // Estado
      asistencias,
      estadisticas,
      loading,
      mostrarDialogoIngreso,
      guardandoIngreso,
      clientesEncontrados,
      clienteSeleccionado,
      filtros,
      formularioIngreso,
      errores,
      permissions,
      
      // Métodos
      cargarAsistencias,
      buscarClientes,
      seleccionarCliente,
      registrarIngreso,
      confirmarEliminar,
      cerrarDialogoIngreso,
      filtrarHoy,
      aplicarFiltros,
      limpiarFiltros,
      formatearFechaHora,
      formatearFecha,
      esHoy,
      debounceSearch
    };
  }
}
</script>

<style scoped>
.asistencias {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  margin: 0;
  color: #2c3e50;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
  border: none;
}

.stat-card .p-card-content {
  padding: 1.5rem;
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  color: white;
}

.stat-icon {
  font-size: 2rem;
  opacity: 0.8;
}

.stat-info h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
}

.stat-info p {
  margin: 0;
  opacity: 0.9;
}

.filtros-card {
  margin-bottom: 2rem;
}

.filtros {
  display: grid;
  grid-template-columns: 2fr 1fr auto auto;
  gap: 1rem;
  align-items: end;
}

.filtro-item label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.cliente-info {
  display: flex;
  flex-direction: column;
}

.cliente-info small {
  color: #6b7280;
}

.membresia-badge {
  background: #3b82f6;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  display: inline-block;
  margin-bottom: 0.25rem;
}

.membresia-venc {
  display: block;
  color: #6b7280;
  font-size: 0.75rem;
}

.sin-membresia {
  color: #ef4444;
  font-style: italic;
}

.dialog-content {
  padding: 1rem 0;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.clientes-encontrados {
  margin: 1rem 0;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  max-height: 200px;
  overflow-y: auto;
}

.cliente-item {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cliente-item:hover {
  background-color: #f9fafb;
}

.cliente-item:last-child {
  border-bottom: none;
}

.cliente-estado {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.cliente-seleccionado {
  margin: 1rem 0;
  padding: 1rem;
  background: #f0f9ff;
  border: 1px solid #0ea5e9;
  border-radius: 6px;
}

.cliente-card {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.cliente-detalles {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.membresia-info {
  margin-top: 0.75rem;
  padding: 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
}

.membresia-activa {
  background: #dcfce7;
  border: 1px solid #16a34a;
  color: #166534;
}

.membresia-vence-hoy {
  background: #fef3c7;
  border: 1px solid #d97706;
  color: #92400e;
}

.membresia-vencida {
  background: #fee2e2;
  border: 1px solid #dc2626;
  color: #991b1b;
}

.sin-membresia-info {
  margin-top: 0.75rem;
  padding: 0.75rem;
  background: #f3f4f6;
  border: 1px solid #6b7280;
  border-radius: 6px;
  color: #374151;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .filtros {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
}
</style>
