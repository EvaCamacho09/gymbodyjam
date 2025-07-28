<template>
  <div class="clientes">
    <!-- Header con filtros y acciones -->
    <Card class="filters-card">
      <template #content>
        <div class="filters-section">
          <div class="filters-left">
            <div class="search-group">
              <InputText
                v-model="filters.search"
                placeholder="Buscar por nombre, cédula o email..."
                class="search-input"
                @input="handleSearch"
              />
            </div>
            
            <div class="filter-group">
              <select v-model="filters.estado" @change="loadClientes" class="estado-filter">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
              </select>
            </div>
            
            <div class="filter-group">
              <select v-model="filters.membresia" @change="loadClientes" class="membresia-filter">
                <option value="">Todas las membresías</option>
                <option value="activa">Membresía Activa</option>
                <option value="moroso">Clientes Morosos</option>
                <option value="proximos">Próximos a Vencer (7 días)</option>
                <option value="sin_membresia">Sin Membresía</option>
              </select>
            </div>
          </div>
          
          <div class="filters-right">
            <Button
              icon="pi pi-plus"
              label="Nuevo Cliente"
              @click="showCreateDialog = true"
            />
          </div>
        </div>
      </template>
    </Card>

    <!-- Tabla de clientes -->
    <Card class="table-card">
      <template #content>
        <DataTable
          :value="clientes.data"
          :loading="loading"
          :paginator="true"
          :rows="clientes.per_page"
          :totalRecords="clientes.total"
          :lazy="true"
          @page="onPage"
          responsiveLayout="scroll"
          class="clientes-table"
        >
          <Column field="nombre" header="Nombre" sortable></Column>
          <Column field="cedula" header="Cédula" sortable></Column>
          <Column field="correo" header="Email" sortable></Column>
          <Column field="telefono" header="Teléfono"></Column>
          <Column field="estado" header="Estado">
            <template #body="slotProps">
              <span
                :class="{
                  'status-badge': true,
                  'status-active': slotProps.data.estado === 'activo',
                  'status-inactive': slotProps.data.estado === 'inactivo'
                }"
              >
                {{ slotProps.data.estado }}
              </span>
            </template>
          </Column>
          <Column header="Estado Membresía">
            <template #body="slotProps">
              <div class="membresia-status">
                <Tag
                  v-if="slotProps.data.es_moroso"
                  value="Moroso"
                  severity="danger"
                  icon="pi pi-exclamation-triangle"
                />
                <Tag
                  v-else-if="slotProps.data.dias_restantes !== undefined && slotProps.data.dias_restantes <= 7 && slotProps.data.dias_restantes > 0"
                  value="Por Vencer"
                  severity="warning"
                  icon="pi pi-clock"
                />
                <Tag
                  v-else-if="slotProps.data.dias_restantes !== undefined && slotProps.data.dias_restantes > 7"
                  value="Activa"
                  severity="success"
                  icon="pi pi-check"
                />
                <Tag
                  v-else
                  value="Sin Membresía"
                  severity="secondary"
                  icon="pi pi-minus"
                />
              </div>
            </template>
          </Column>
          <Column header="Días Restantes">
            <template #body="slotProps">
              <div class="dias-restantes">
                <span v-if="slotProps.data.dias_restantes !== undefined && slotProps.data.dias_restantes >= 0 && slotProps.data.dias_restantes !== null">
                  {{ slotProps.data.dias_restantes  }} días
                </span>
                <span v-else-if="slotProps.data.dias_restantes === null" class="vencida">
                  0 días (Sin Membresía)
                </span>
                <span v-else-if="slotProps.data.dias_restantes !== undefined && slotProps.data.dias_restantes < 0" class="vencida">
                  Vencida hace {{ Math.abs(slotProps.data.dias_restantes) }} días
                </span>
                <span v-else class="sin-membresia">
                  N/A
                </span>
              </div>
            </template>
          </Column>
          <Column header="Acciones">
            <template #body="slotProps">
              <div class="action-buttons">
                <Button
                  icon="pi pi-eye"
                  class="p-button-text p-button-info"
                  @click="viewCliente(slotProps.data)"
                  v-tooltip="'Ver detalles'"
                />
                <Button
                  icon="pi pi-link"
                  class="p-button-text p-button-help"
                  @click="generarEnlacePublico(slotProps.data)"
                  v-tooltip="'Enlace público'"
                />
                <Button
                  v-if="permissions.canEditClientes"
                  icon="pi pi-edit"
                  class="p-button-text p-button-warning"
                  @click="editCliente(slotProps.data)"
                  v-tooltip="'Editar'"
                />
                <Button
                  v-if="permissions.canDeleteClientes"
                  icon="pi pi-trash"
                  class="p-button-text p-button-danger"
                  @click="confirmDelete(slotProps.data)"
                  v-tooltip="'Eliminar'"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Dialog para crear/editar cliente -->
    <Dialog
      v-model:visible="showCreateDialog"
      :header="editingCliente ? 'Editar Cliente' : 'Nuevo Cliente'"
      :modal="true"
      :closable="true"
      :style="{ width: '500px' }"
    >
      <form @submit.prevent="saveCliente" class="cliente-form">
        <div class="form-group">
          <label for="nombre">Nombre *</label>
          <InputText
            id="nombre"
            v-model="clienteForm.nombre"
            :class="{ 'p-invalid': errors.nombre }"
            required
          />
          <small v-if="errors.nombre" class="p-error">{{ errors.nombre }}</small>
        </div>

        <div class="form-group">
          <label for="cedula">Cédula *</label>
          <InputText
            id="cedula"
            v-model="clienteForm.cedula"
            :class="{ 'p-invalid': errors.cedula }"
            required
          />
          <small v-if="errors.cedula" class="p-error">{{ errors.cedula }}</small>
        </div>

        <div class="form-group">
          <label for="correo">Email</label>
          <InputText
            id="correo"
            v-model="clienteForm.correo"
            type="email"
            :class="{ 'p-invalid': errors.correo }"
          />
          <small v-if="errors.correo" class="p-error">{{ errors.correo }}</small>
        </div>

        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <InputText
            id="telefono"
            v-model="clienteForm.telefono"
            :class="{ 'p-invalid': errors.telefono }"
          />
          <small v-if="errors.telefono" class="p-error">{{ errors.telefono }}</small>
        </div>

        <div class="form-group">
          <label for="estado">Estado *</label>
          <select
            id="estado"
            v-model="clienteForm.estado"
            :class="{ 'p-invalid': errors.estado }"
            required
          >
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
          <small v-if="errors.estado" class="p-error">{{ errors.estado }}</small>
        </div>

        <div class="form-actions">
          <Button
            type="button"
            label="Cancelar"
            class="p-button-text"
            @click="closeDialog"
          />
          <Button
            type="submit"
            :label="editingCliente ? 'Actualizar' : 'Crear'"
            :loading="saving"
            icon="pi pi-save"
          />
        </div>
      </form>
    </Dialog>

    <!-- Dialog para mostrar enlace público -->
    <Dialog
      v-model:visible="mostrarDialogoEnlacePublico"
      modal
      header="Enlace Público del Cliente"
      :style="{ width: '600px' }"
      class="p-fluid"
    >
      <div class="dialog-content">
        <div class="enlace-publico-info">
          <div class="info-section">
            <h4><i class="pi pi-info-circle"></i> Información</h4>
            <p>
              Este enlace permite al cliente <strong>{{ clienteSeleccionado?.nombre }}</strong> 
              ver su información personal, membresía actual, historial de asistencias y estadísticas 
              de forma segura y privada.
            </p>
          </div>
          
          <div class="enlace-section">
            <h4><i class="pi pi-link"></i> Enlace Público</h4>
            <div class="url-container">
              <input
                type="text"
                v-model="enlacePublico"
                readonly
                class="url-input"
                @click="$event.target.select()"
              />
              <Button
                icon="pi pi-copy"
                class="p-button-outlined"
                @click="copiarEnlace"
                v-tooltip="'Copiar enlace'"
              />
            </div>
          </div>
          
          <div class="acciones-section">
            <h4><i class="pi pi-cog"></i> Acciones Rápidas</h4>
            <div class="acciones-grid">
              <Button
                label="Enviar por Email"
                icon="pi pi-send"
                class="p-button-outlined"
                @click="enviarPorEmail"
                :disabled="!clienteSeleccionado?.correo"
              />
              <Button
                label="Compartir WhatsApp"
                icon="pi pi-whatsapp"
                class="p-button-outlined p-button-success"
                @click="compartirWhatsApp"
                :disabled="!clienteSeleccionado?.telefono"
              />
              <Button
                label="Abrir en nueva pestaña"
                icon="pi pi-external-link"
                class="p-button-outlined"
                @click="abrirEnlace"
              />
            </div>
          </div>
        </div>
      </div>
      <template #footer>
        <Button
          label="Cerrar"
          class="p-button-text"
          @click="mostrarDialogoEnlacePublico = false"
        />
      </template>
    </Dialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { usePermissions } from '../composables/usePermissions';
import api from '../services/api';

export default {
  name: 'Clientes',
  setup() {
    const router = useRouter();
    const route = useRoute();
    const toast = useToast();
    const confirm = useConfirm();
    const { permissions } = usePermissions();
    const loading = ref(false);
    const saving = ref(false);
    const showCreateDialog = ref(false);
    const editingCliente = ref(null);
    const mostrarDialogoEnlacePublico = ref(false);
    const clienteSeleccionado = ref(null);
    const enlacePublico = ref('');
    const generandoEnlace = ref(false);

    const clientes = ref({
      data: [],
      per_page: 15,
      total: 0,
      current_page: 1
    });

    const filters = reactive({
      search: '',
      estado: '',
      membresia: ''
    });

    const clienteForm = reactive({
      nombre: '',
      cedula: '',
      correo: '',
      telefono: '',
      estado: 'activo'
    });

    const errors = reactive({
      nombre: '',
      cedula: '',
      correo: '',
      telefono: '',
      estado: ''
    });

    let searchTimeout = null;

    const clearErrors = () => {
      Object.keys(errors).forEach(key => {
        errors[key] = '';
      });
    };

    const clearForm = () => {
      Object.keys(clienteForm).forEach(key => {
        clienteForm[key] = key === 'estado' ? 'activo' : '';
      });
    };

    const loadClientes = async (page = 1) => {
      try {
        loading.value = true;
        const params = {
          page,
          ...filters
        };
        const response = await api.getClientes(params);
        clientes.value = response;
      } catch (error) {
        console.error('Error loading clients:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al cargar clientes',
          life: 3000
        });
      } finally {
        loading.value = false;
      }
    };

    const handleSearch = () => {
      if (searchTimeout) {
        clearTimeout(searchTimeout);
      }
      searchTimeout = setTimeout(() => {
        loadClientes();
      }, 500);
    };

    const onPage = (event) => {
      loadClientes(event.page + 1);
    };

    const editCliente = (cliente) => {
      editingCliente.value = cliente;
      Object.keys(clienteForm).forEach(key => {
        clienteForm[key] = cliente[key] || '';
      });
      showCreateDialog.value = true;
    };

    const saveCliente = async () => {
      clearErrors();
      saving.value = true;

      try {
        if (editingCliente.value) {
          await api.updateCliente(editingCliente.value.id, clienteForm);
          toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Cliente actualizado correctamente',
            life: 3000
          });
        } else {
          await api.createCliente(clienteForm);
          toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Cliente creado correctamente',
            life: 3000
          });
        }
        
        closeDialog();
        loadClientes();
      } catch (error) {
        console.error('Error saving client:', error);
        
        if (error.response?.status === 422) {
          const validationErrors = error.response.data.errors;
          if (validationErrors) {
            Object.keys(validationErrors).forEach(key => {
              errors[key] = validationErrors[key][0];
            });
          }
        } else {
          toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Error al guardar cliente',
            life: 5000
          });
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (cliente) => {
      confirm.require({
        message: `¿Estás seguro de eliminar al cliente ${cliente.nombre}?`,
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => deleteCliente(cliente.id)
      });
    };

    const deleteCliente = async (id) => {
      try {
        await api.deleteCliente(id);
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Cliente eliminado correctamente',
          life: 3000
        });
        loadClientes();
      } catch (error) {
        console.error('Error deleting client:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al eliminar cliente',
          life: 3000
        });
      }
    };

    const closeDialog = () => {
      showCreateDialog.value = false;
      editingCliente.value = null;
      clearForm();
      clearErrors();
    };

    const viewCliente = (cliente) => {
      router.push(`/clientes/${cliente.id}`);
    };

    const generarEnlacePublico = async (cliente) => {
      try {
        generandoEnlace.value = true;
        clienteSeleccionado.value = cliente;
        
        const response = await api.generarEnlacePublico(cliente.id);
        enlacePublico.value = response.url;
        mostrarDialogoEnlacePublico.value = true;
        
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Enlace público generado correctamente',
          life: 3000
        });
      } catch (error) {
        console.error('Error al generar enlace público:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'No se pudo generar el enlace público',
          life: 3000
        });
      } finally {
        generandoEnlace.value = false;
      }
    };

    const copiarEnlace = async () => {
      try {
        await navigator.clipboard.writeText(enlacePublico.value);
        toast.add({
          severity: 'success',
          summary: 'Copiado',
          detail: 'Enlace copiado al portapapeles',
          life: 2000
        });
      } catch (error) {
        // Fallback para navegadores que no soportan clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = enlacePublico.value;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        toast.add({
          severity: 'success',
          summary: 'Copiado',
          detail: 'Enlace copiado al portapapeles',
          life: 2000
        });
      }
    };

    const enviarPorEmail = () => {
      const asunto = encodeURIComponent('Tu información personal del gimnasio');
      const cuerpo = encodeURIComponent(`Hola ${clienteSeleccionado.value.nombre},\n\nPuedes consultar tu información personal, membresía y asistencias en el siguiente enlace:\n\n${enlacePublico.value}\n\nSaludos,\nEquipo del Gimnasio`);
      window.open(`mailto:${clienteSeleccionado.value.correo}?subject=${asunto}&body=${cuerpo}`);
    };

    const compartirWhatsApp = () => {
      const mensaje = encodeURIComponent(`Hola ${clienteSeleccionado.value.nombre}, puedes consultar tu información del gimnasio en: ${enlacePublico.value}`);
      const telefono = clienteSeleccionado.value.telefono.replace(/\D/g, ''); // Remover caracteres no numéricos
      window.open(`https://wa.me/${telefono}?text=${mensaje}`, '_blank');
    };

    const abrirEnlace = () => {
      window.open(enlacePublico.value, '_blank');
    };

    onMounted(() => {
      // Verificar si hay filtros desde el dashboard
      if (route.query.filtro) {
        switch (route.query.filtro) {
          case 'morosos':
            filters.membresia = 'moroso';
            break;
          case 'proximos':
            filters.membresia = 'proximos';
            break;
          case 'activos':
            filters.estado = 'activo';
            break;
        }
      }
      loadClientes();
    });

    return {
      clientes,
      filters,
      clienteForm,
      errors,
      loading,
      saving,
      showCreateDialog,
      editingCliente,
      mostrarDialogoEnlacePublico,
      clienteSeleccionado,
      enlacePublico,
      generandoEnlace,
      permissions,
      loadClientes,
      handleSearch,
      onPage,
      editCliente,
      saveCliente,
      confirmDelete,
      closeDialog,
      viewCliente,
      generarEnlacePublico,
      copiarEnlace,
      enviarPorEmail,
      compartirWhatsApp,
      abrirEnlace
    };
  }
}
</script>

<style scoped>
.clientes {
  max-width: 1200px;
  margin: 0 auto;
}

.filters-card {
  margin-bottom: 1.5rem;
}

.filters-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.filters-left {
  display: flex;
  gap: 1rem;
  flex: 1;
}

.search-input {
  min-width: 300px;
}

.estado-filter {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.875rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-active {
  background: #dcfce7;
  color: #166534;
}

.status-inactive {
  background: #fee2e2;
  color: #991b1b;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.cliente-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #374151;
}

.form-group select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.875rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.p-error {
  color: #dc2626;
  font-size: 0.75rem;
}

@media (max-width: 768px) {
  .filters-section {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filters-left {
    flex-direction: column;
  }
  
  .search-input {
    min-width: auto;
  }
}

/* Estilos para el enlace público */
.enlace-publico-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-section,
.enlace-section,
.acciones-section {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid var(--primary-color);
}

.info-section h4,
.enlace-section h4,
.acciones-section h4 {
  margin: 0 0 0.75rem 0;
  color: var(--primary-color);
  font-size: 1rem;
  font-weight: 600;
}

.info-section p {
  margin: 0;
  color: #666;
  line-height: 1.5;
}

.url-container {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.url-input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
  background: white;
  cursor: pointer;
}

.url-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}

.acciones-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 0.75rem;
}

@media (max-width: 600px) {
  .acciones-grid {
    grid-template-columns: 1fr;
  }
  
  .url-container {
    flex-direction: column;
  }
  
  .url-input {
    width: 100%;
  }
}

/* Estilos para las nuevas columnas */
.membresia-status {
  display: flex;
  align-items: center;
}

.dias-restantes {
  font-weight: 500;
}

.dias-restantes .vencida {
  color: #dc3545;
  font-weight: 600;
}

.dias-restantes .sin-membresia {
  color: #6c757d;
  font-style: italic;
}

.filter-group {
  margin-right: 1rem;
}

.membresia-filter {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.875rem;
  min-width: 180px;
}

.membresia-filter:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}
</style>
