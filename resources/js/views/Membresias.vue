<template>
  <div class="membresias">
    <!-- Header con acciones -->
    <Card class="header-card">
      <template #content>
        <div class="header-section">
          <div class="header-left">
            <h2>Gestión de Membresías</h2>
            <p>Administra los tipos de membresías del gimnasio</p>
          </div>
          
          <div class="header-right">
            <Button
              icon="pi pi-plus"
              label="Nueva Membresía"
              @click="showCreateDialog = true"
            />
          </div>
        </div>
      </template>
    </Card>

    <!-- Lista de membresías -->
    <div class="membresias-grid">
      <Card
        v-for="membresia in membresias"
        :key="membresia.id"
        class="membresia-card"
        :class="{ 'membresia-inactive': !membresia.activa }"
      >
        <template #header>
          <div class="membresia-header">
            <h3>{{ membresia.nombre }}</h3>
            <span
              :class="{
                'status-badge': true,
                'status-active': membresia.activa,
                'status-inactive': !membresia.activa
              }"
            >
              {{ membresia.activa ? 'Activa' : 'Inactiva' }}
            </span>
          </div>
        </template>
        
        <template #content>
          <div class="membresia-details">
            <div class="detail-item">
              <strong>Precio:</strong> ${{ membresia.precio }}
            </div>
            <div class="detail-item">
              <strong>Duración:</strong> {{ membresia.duracion_dias }} días
            </div>
            <div class="detail-item" v-if="membresia.descripcion">
              <strong>Descripción:</strong> {{ membresia.descripcion }}
            </div>
          </div>
        </template>
        
        <template #footer>
          <div class="membresia-actions">
            <Button
              icon="pi pi-edit"
              label="Editar"
              class="p-button-text p-button-warning"
              @click="editMembresia(membresia)"
            />
            <Button
              icon="pi pi-trash"
              label="Eliminar"
              class="p-button-text p-button-danger"
              @click="confirmDelete(membresia)"
            />
          </div>
        </template>
      </Card>
    </div>

    <!-- Dialog para crear/editar membresía -->
    <Dialog
      v-model:visible="showCreateDialog"
      :header="editingMembresia ? 'Editar Membresía' : 'Nueva Membresía'"
      :modal="true"
      :closable="true"
      :style="{ width: '500px' }"
    >
      <form @submit.prevent="saveMembresia" class="membresia-form">
        <div class="form-group">
          <label for="nombre">Nombre *</label>
          <InputText
            id="nombre"
            v-model="membresiaForm.nombre"
            :class="{ 'p-invalid': errors.nombre }"
            required
          />
          <small v-if="errors.nombre" class="p-error">{{ errors.nombre }}</small>
        </div>

        <div class="form-group">
          <label for="precio">Precio *</label>
          <InputText
            id="precio"
            v-model="membresiaForm.precio"
            type="number"
            step="0.01"
            min="0"
            :class="{ 'p-invalid': errors.precio }"
            required
          />
          <small v-if="errors.precio" class="p-error">{{ errors.precio }}</small>
        </div>

        <div class="form-group">
          <label for="duracion_dias">Duración (días) *</label>
          <InputText
            id="duracion_dias"
            v-model="membresiaForm.duracion_dias"
            type="number"
            min="1"
            :class="{ 'p-invalid': errors.duracion_dias }"
            required
          />
          <small v-if="errors.duracion_dias" class="p-error">{{ errors.duracion_dias }}</small>
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción</label>
          <textarea
            id="descripcion"
            v-model="membresiaForm.descripcion"
            rows="3"
            :class="{ 'p-invalid': errors.descripcion }"
            class="form-textarea"
          ></textarea>
          <small v-if="errors.descripcion" class="p-error">{{ errors.descripcion }}</small>
        </div>

        <div class="form-group">
          <label>
            <input
              type="checkbox"
              v-model="membresiaForm.activa"
              class="form-checkbox"
            />
            Membresía activa
          </label>
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
            :label="editingMembresia ? 'Actualizar' : 'Crear'"
            :loading="saving"
            icon="pi pi-save"
          />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import api from '../services/api';

export default {
  name: 'Membresias',
  setup() {
    const toast = useToast();
    const confirm = useConfirm();
    const loading = ref(false);
    const saving = ref(false);
    const showCreateDialog = ref(false);
    const editingMembresia = ref(null);

    const membresias = ref([]);

    const membresiaForm = reactive({
      nombre: '',
      precio: '',
      duracion_dias: '',
      descripcion: '',
      activa: true
    });

    const errors = reactive({
      nombre: '',
      precio: '',
      duracion_dias: '',
      descripcion: ''
    });

    const clearErrors = () => {
      Object.keys(errors).forEach(key => {
        errors[key] = '';
      });
    };

    const clearForm = () => {
      Object.keys(membresiaForm).forEach(key => {
        membresiaForm[key] = key === 'activa' ? true : '';
      });
    };

    const loadMembresias = async () => {
      try {
        loading.value = true;
        const response = await api.getMembresias();
        membresias.value = response;
      } catch (error) {
        console.error('Error loading memberships:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al cargar membresías',
          life: 3000
        });
      } finally {
        loading.value = false;
      }
    };

    const editMembresia = (membresia) => {
      editingMembresia.value = membresia;
      Object.keys(membresiaForm).forEach(key => {
        membresiaForm[key] = membresia[key];
      });
      showCreateDialog.value = true;
    };

    const saveMembresia = async () => {
      clearErrors();
      saving.value = true;

      try {
        if (editingMembresia.value) {
          await api.updateMembresia(editingMembresia.value.id, membresiaForm);
          toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Membresía actualizada correctamente',
            life: 3000
          });
        } else {
          await api.createMembresia(membresiaForm);
          toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Membresía creada correctamente',
            life: 3000
          });
        }
        
        closeDialog();
        loadMembresias();
      } catch (error) {
        console.error('Error saving membership:', error);
        
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
            detail: error.response?.data?.message || 'Error al guardar membresía',
            life: 5000
          });
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (membresia) => {
      confirm.require({
        message: `¿Estás seguro de eliminar la membresía ${membresia.nombre}?`,
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => deleteMembresia(membresia.id)
      });
    };

    const deleteMembresia = async (id) => {
      try {
        await api.deleteMembresia(id);
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Membresía eliminada correctamente',
          life: 3000
        });
        loadMembresias();
      } catch (error) {
        console.error('Error deleting membership:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al eliminar membresía',
          life: 3000
        });
      }
    };

    const closeDialog = () => {
      showCreateDialog.value = false;
      editingMembresia.value = null;
      clearForm();
      clearErrors();
    };

    onMounted(() => {
      loadMembresias();
    });

    return {
      membresias,
      membresiaForm,
      errors,
      loading,
      saving,
      showCreateDialog,
      editingMembresia,
      loadMembresias,
      editMembresia,
      saveMembresia,
      confirmDelete,
      closeDialog
    };
  }
}
</script>

<style scoped>
.membresias {
  max-width: 1200px;
  margin: 0 auto;
}

.header-card {
  margin-bottom: 2rem;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left h2 {
  margin: 0 0 0.5rem 0;
  color: #1f2937;
}

.header-left p {
  margin: 0;
  color: #6b7280;
}

.membresias-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.membresia-card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.membresia-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.membresia-inactive {
  opacity: 0.7;
}

.membresia-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.membresia-header h3 {
  margin: 0;
  color: #1f2937;
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

.membresia-details {
  padding: 1rem;
}

.detail-item {
  margin-bottom: 0.75rem;
  color: #374151;
}

.detail-item:last-child {
  margin-bottom: 0;
}

.membresia-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
}

.membresia-form {
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

.form-textarea {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-family: inherit;
  font-size: 0.875rem;
  resize: vertical;
}

.form-checkbox {
  margin-right: 0.5rem;
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
  .header-section {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  
  .membresias-grid {
    grid-template-columns: 1fr;
  }
}
</style>
