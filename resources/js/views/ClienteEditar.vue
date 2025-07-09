<template>
  <div class="cliente-editar">
    <div class="page-header">
      <div class="header-left">
        <Button
          icon="pi pi-arrow-left"
          class="p-button-text"
          @click="$router.go(-1)"
        />
        <h1>Editar Cliente</h1>
      </div>
    </div>

    <Card>
      <template #content>
        <form @submit.prevent="actualizarCliente" class="cliente-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Nombre Completo *</label>
              <InputText
                v-model="formulario.nombre"
                placeholder="Ingrese el nombre completo"
                :class="{ 'p-invalid': errores.nombre }"
                required
              />
              <small v-if="errores.nombre" class="p-error">{{
                errores.nombre
              }}</small>
            </div>

            <div class="form-group">
              <label>Cédula *</label>
              <InputText
                v-model="formulario.cedula"
                placeholder="Ingrese la cédula"
                :class="{ 'p-invalid': errores.cedula }"
                required
              />
              <small v-if="errores.cedula" class="p-error">{{
                errores.cedula
              }}</small>
            </div>

            <div class="form-group">
              <label>Email</label>
              <InputText
                v-model="formulario.correo"
                type="email"
                placeholder="Ingrese el email"
                :class="{ 'p-invalid': errores.correo }"
              />
              <small v-if="errores.correo" class="p-error">{{
                errores.correo
              }}</small>
            </div>

            <div class="form-group">
              <label>Teléfono</label>
              <InputText
                v-model="formulario.telefono"
                placeholder="Ingrese el teléfono"
                :class="{ 'p-invalid': errores.telefono }"
              />
              <small v-if="errores.telefono" class="p-error">{{
                errores.telefono
              }}</small>
            </div>

            <!-- Medidas corporales -->
            <div class="form-group">
              <label>Peso (kg)</label>
              <InputNumber v-model="formulario.peso" :min="0" />
            </div>

            <div class="form-group">
              <label>Altura (cm)</label>
              <InputNumber v-model="formulario.altura" :min="0" />
            </div>

            <div class="form-group">
              <label>% Grasa Corporal</label>
              <InputNumber
                v-model="formulario.porcentaje_grasa"
                :min="0"
                :max="100"
                :step="0.1"
              />
            </div>

            <div class="form-group">
              <label>Masa Muscular</label>
              <InputNumber v-model="formulario.masa_muscular" :min="0" />
            </div>

            <div class="form-group">
              <label>Cintura (cm)</label>
              <InputNumber v-model="formulario.cintura" :min="0" />
            </div>

            <div class="form-group">
              <label>Cadera (cm)</label>
              <InputNumber v-model="formulario.cadera" :min="0" />
            </div>

            <div class="form-group">
              <label>Pecho / Tórax (cm)</label>
              <InputNumber v-model="formulario.pecho_torax" :min="0" />
            </div>

            <div class="form-group">
              <label>Bíceps Relajado (cm)</label>
              <InputNumber v-model="formulario.biceps_relajado" :min="0" />
            </div>

            <div class="form-group">
              <label>Bíceps Contraído (cm)</label>
              <InputNumber v-model="formulario.biceps_contraido" :min="0" />
            </div>

            <div class="form-group">
              <label>Antebrazo (cm)</label>
              <InputNumber v-model="formulario.antebrazo" :min="0" />
            </div>

            <div class="form-group">
              <label>Muslo (cm)</label>
              <InputNumber v-model="formulario.muslo" :min="0" />
            </div>

            <div class="form-group">
              <label>Pantorrilla (cm)</label>
              <InputNumber v-model="formulario.pantorrilla" :min="0" />
            </div>

            <div class="form-group">
              <label>Frecuencia Cardíaca en Reposo (lpm)</label>
              <InputNumber v-model="formulario.frecuencia_cardiaca" :min="0" />
            </div>

            <div class="form-group">
              <label>Presión Arterial</label>
              <InputText
                v-model="formulario.presion_arterial"
                placeholder="Ej: 120/80"
              />
            </div>

            <div class="form-group">
              <label>Estado</label>
              <Dropdown
                v-model="formulario.estado"
                :options="[
                  { label: 'Activo', value: 'activo' },
                  { label: 'Inactivo', value: 'inactivo' },
                ]"
                optionLabel="label"
                optionValue="value"
                placeholder="Seleccione el estado"
              />
            </div>

            <div class="form-group">
              <label>Observaciones</label>
              <Textarea
                v-model="formulario.observaciones"
                rows="4"
                placeholder="Observaciones adicionales (opcional)"
              />
            </div>
          </div>

          <div class="form-actions">
            <Button
              label="Cancelar"
              class="p-button-text"
              @click="$router.go(-1)"
            />
            <Button
              type="submit"
              label="Actualizar Cliente"
              :loading="guardando"
              icon="pi pi-save"
            />
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import api from "../services/api";

export default {
  name: "ClienteEditar",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    const guardando = ref(false);
    const loading = ref(false);

    const formulario = reactive({
      nombre: "",
      cedula: "",
      correo: "",
      telefono: "",
      estado: "activo",
      observaciones: "",

      // Campos adicionales
      peso: null,
      altura: null,
      porcentaje_grasa: null,
      masa_muscular: null,
      cintura: null,
      cadera: null,
      pecho_torax: null,
      biceps_relajado: null,
      biceps_contraido: null,
      antebrazo: null,
      muslo: null,
      pantorrilla: null,
      frecuencia_cardiaca: null,
      presion_arterial: "",
    });

    const errores = reactive({
      nombre: "",
      cedula: "",
      correo: "",
      telefono: "",
    });

    const cargarCliente = async () => {
      try {
        loading.value = true;
        const response = await api.getCliente(route.params.id);
        const cliente = response.cliente;

        // Llenar formulario con datos del cliente
        formulario.nombre = cliente.nombre;
        formulario.cedula = cliente.cedula;
        formulario.correo = cliente.correo || "";
        formulario.telefono = cliente.telefono || "";
        formulario.estado = cliente.estado;
        formulario.observaciones = cliente.observaciones || "";

        formulario.peso = cliente.peso;
        formulario.altura = cliente.altura;
        formulario.porcentaje_grasa = cliente.porcentaje_grasa;
        formulario.masa_muscular = cliente.masa_muscular;
        formulario.cintura = cliente.cintura;
        formulario.cadera = cliente.cadera;
        formulario.pecho_torax = cliente.pecho_torax;
        formulario.biceps_relajado = cliente.biceps_relajado;
        formulario.biceps_contraido = cliente.biceps_contraido;
        formulario.antebrazo = cliente.antebrazo;
        formulario.muslo = cliente.muslo;
        formulario.pantorrilla = cliente.pantorrilla;
        formulario.frecuencia_cardiaca = cliente.frecuencia_cardiaca;
        formulario.presion_arterial = cliente.presion_arterial || "";
      } catch (error) {
        console.error("Error cargando cliente:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Error al cargar los datos del cliente",
          life: 3000,
        });
        router.go(-1);
      } finally {
        loading.value = false;
      }
    };

    const limpiarErrores = () => {
      errores.nombre = "";
      errores.cedula = "";
      errores.correo = "";
      errores.telefono = "";
    };

    const validarFormulario = () => {
      limpiarErrores();
      let valid = true;

      if (!formulario.nombre.trim()) {
        errores.nombre = "El nombre es requerido";
        valid = false;
      }

      if (!formulario.cedula.trim()) {
        errores.cedula = "La cédula es requerida";
        valid = false;
      }

      if (formulario.correo && !/\S+@\S+\.\S+/.test(formulario.correo)) {
        errores.correo = "El email no es válido";
        valid = false;
      }

      return valid;
    };

    const actualizarCliente = async () => {
      if (!validarFormulario()) return;

      try {
        guardando.value = true;

        await api.updateCliente(route.params.id, {
          nombre: formulario.nombre.trim(),
          cedula: formulario.cedula.trim(),
          correo: formulario.correo.trim() || null,
          telefono: formulario.telefono.trim() || null,
          estado: formulario.estado,
          observaciones: formulario.observaciones.trim() || null,

          // Nuevas medidas
          peso: formulario.peso,
          altura: formulario.altura,
          porcentaje_grasa: formulario.porcentaje_grasa,
          masa_muscular: formulario.masa_muscular,
          cintura: formulario.cintura,
          cadera: formulario.cadera,
          pecho_torax: formulario.pecho_torax,
          biceps_relajado: formulario.biceps_relajado,
          biceps_contraido: formulario.biceps_contraido,
          antebrazo: formulario.antebrazo,
          muslo: formulario.muslo,
          pantorrilla: formulario.pantorrilla,
          frecuencia_cardiaca: formulario.frecuencia_cardiaca,
          presion_arterial: formulario.presion_arterial.trim() || null,
        });

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Cliente actualizado exitosamente",
          life: 3000,
        });

        router.push(`/clientes/${route.params.id}`);
      } catch (error) {
        console.error("Error actualizando cliente:", error);

        // Manejar errores de validación del servidor
        if (error.response?.data?.errors) {
          const serverErrors = error.response.data.errors;
          Object.keys(serverErrors).forEach((field) => {
            if (errores.hasOwnProperty(field)) {
              errores[field] = serverErrors[field][0];
            }
          });
        } else {
          toast.add({
            severity: "error",
            summary: "Error",
            detail:
              error.response?.data?.message || "Error al actualizar el cliente",
            life: 3000,
          });
        }
      } finally {
        guardando.value = false;
      }
    };

    onMounted(() => {
      cargarCliente();
    });

    return {
      formulario,
      errores,
      guardando,
      loading,
      actualizarCliente,
    };
  },
};
</script>

<style scoped>
.cliente-editar {
  max-width: 800px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-left h1 {
  margin: 0;
  color: #2c3e50;
}

.cliente-form {
  padding: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-group .p-inputtext,
.form-group .p-dropdown,
.form-group .p-inputtextarea {
  width: 100%;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>
