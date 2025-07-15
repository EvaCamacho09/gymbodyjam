<template>
  <div class="cliente-detalle" v-if="cliente">
    <div class="page-header">
      <div class="header-left">
        <Button
          icon="pi pi-arrow-left"
          class="p-button-text"
          @click="$router.go(-1)"
        />
        <div class="cliente-title">
          <h1>{{ cliente.nombre }}</h1>
          <span class="cliente-cedula">{{ cliente.cedula }}</span>
        </div>
      </div>
      <div class="header-actions">
        <Button
          v-if="!estadisticas.ya_ingreso_hoy"
          icon="pi pi-sign-in"
          label="Registrar Ingreso"
          @click="registrarIngreso"
          class="p-button-success"
          :loading="registrandoIngreso"
        />
        <Button
          icon="pi pi-link"
          label="Enlace Público"
          @click="generarEnlacePublico"
          class="p-button-info p-button-outlined"
          :loading="generandoEnlace"
        />
        <Button
          v-if="permissions.canEditClientes"
          icon="pi pi-pencil"
          label="Editar"
          @click="editarCliente"
          class="p-button-outlined"
        />
      </div>
    </div>

    <div class="info-list mb-2">
      <!-- Medidas -->
      <Card class="estadisticas-asistencias">
        <template #title>
          <h3><i class="pi pi-chart-bar"></i> Estadísticas de Asistencia</h3>
        </template>
        <template #content>
          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-number">
                {{ estadisticas.total_asistencias }}
              </div>
              <div class="stat-label">Total Asistencias</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ estadisticas.asistencias_mes }}</div>
              <div class="stat-label">Este Mes</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">
                <Tag
                  :value="estadisticas.ya_ingreso_hoy ? 'Sí' : 'No'"
                  :severity="
                    estadisticas.ya_ingreso_hoy ? 'success' : 'secondary'
                  "
                />
              </div>
              <div class="stat-label">Ingreso Hoy</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">
                <Tag
                  :value="estadisticas.es_moroso ? 'Sí' : 'No'"
                  :severity="estadisticas.es_moroso ? 'danger' : 'success'"
                />
              </div>
              <div class="stat-label">Moroso</div>
            </div>
          </div>
        </template>
      </Card>
    </div>
    <br />

    <!-- Información del cliente -->
    <div class="cliente-info-grid">
      <!-- Datos personales -->
      <Card class="datos-personales">
        <template #title>
          <h3><i class="pi pi-user"></i> Información Personal</h3>
        </template>
        <template #content>
          <div class="info-list">
            <div class="info-item">
              <strong>Nombre:</strong>
              <span>{{ cliente.nombre }}</span>
            </div>
            <div class="info-item">
              <strong>Cédula:</strong>
              <span>{{ cliente.cedula }}</span>
            </div>
            <div class="info-item">
              <strong>Email:</strong>
              <span>{{ cliente.correo || "No registrado" }}</span>
            </div>
            <div class="info-item">
              <strong>Teléfono:</strong>
              <span>{{ cliente.telefono || "No registrado" }}</span>
            </div>
            <div class="info-item">
              <strong>Estado:</strong>
              <Tag
                :value="cliente.estado"
                :severity="cliente.estado === 'activo' ? 'success' : 'danger'"
              />
            </div>
            <div class="info-item">
              <strong>Fecha de registro:</strong>
              <span>{{ formatearFecha(cliente.created_at) }}</span>
            </div>
          </div>
        </template>
      </Card>

      <!-- Membresía actual -->
      <Card class="membresia-actual">
        <template #title>
          <h3><i class="pi pi-credit-card"></i> Membresía Actual</h3>
        </template>
        <template #content>
          <div v-if="membresiaActiva" class="membresia-info">
            <div class="membresia-header">
              <h4>{{ membresiaActiva.nombre }}</h4>
              <Tag
                :value="membresiaActiva.esta_vencida ? 'Vencida' : 'Activa'"
                :severity="membresiaActiva.esta_vencida ? 'danger' : 'success'"
              />
            </div>
            <div class="membresia-detalles">
              <div class="detalle-item">
                <strong>Precio:</strong>
                <span>${{ membresiaActiva.precio_pagado }}</span>
              </div>
              <div class="detalle-item">
                <strong>Fecha inicio:</strong>
                <span>{{ membresiaActiva.fecha_inicio }}</span>
              </div>
              <div class="detalle-item">
                <strong>Fecha vencimiento:</strong>
                <span>{{ membresiaActiva.fecha_vencimiento }}</span>
              </div>
              <div class="detalle-item">
                <strong>Días restantes:</strong>
                <span
                  :class="{
                    'text-danger': membresiaActiva.dias_restantes === 0,
                    'text-warning':
                      membresiaActiva.dias_restantes <= 7 &&
                      membresiaActiva.dias_restantes > 0,
                  }"
                >
                  {{ membresiaActiva.dias_restantes }} días
                </span>
              </div>
              <div class="detalle-item">
                <strong>Estado de pago:</strong>
                <Tag
                  :value="membresiaActiva.estado_pago"
                  :severity="
                    membresiaActiva.estado_pago === 'pagado'
                      ? 'success'
                      : 'warning'
                  "
                />
              </div>
            </div>
            <div class="membresia-acciones">
              <Button
                v-if="membresiaActiva.dias_restantes <= 7"
                label="Renovar Membresía"
                icon="pi pi-refresh"
                @click="abrirDialogoRenovacion"
                class="p-button-warning"
              />
              <Button
                v-if="permissions.canChangeMembresias"
                label="Cambiar Membresía"
                icon="pi pi-sync"
                @click="abrirDialogoCambio"
                class="p-button-info p-button-outlined"
              />
              <Button
                v-if="permissions.canChangeMembresias"
                label="Editar Membresía"
                icon="pi pi-pencil"
                @click="abrirDialogoEdicionMembresia"
                class="p-button-secondary p-button-outlined"
              />
              <Button
                label="Ver Historial"
                icon="pi pi-history"
                @click="verHistorialMembresias"
                class="p-button-help p-button-outlined"
              />
            </div>
          </div>
          <div v-else class="sin-membresia">
            <p>
              <i class="pi pi-info-circle"></i> Este cliente no tiene una
              membresía activa
            </p>
            <Button
              label="Asignar Membresía"
              icon="pi pi-plus"
              @click="mostrarDialogoAsignacion = true"
              class="p-button-success"
            />
          </div>
        </template>
      </Card>
    </div>

    <!-- Estadísticas de medidas -->
    <Card class="medidas">
      <template #title>
        <h3><i class="pi pi-calculator"></i>Medidas Corporales</h3>
      </template>
      <template #content>
        <div class="cliente-info-grid">
          <!-- Nuevos campos de medidas -->
          <div class="info-item" v-if="cliente.peso">
            <strong>Peso:</strong>
            <span>{{ cliente.peso }} kg</span>
          </div>
          <div class="info-item" v-if="cliente.altura">
            <strong>Altura:</strong>
            <span>{{ cliente.altura }} cm</span>
          </div>
          <div class="info-item" v-if="cliente.imc">
            <strong>IMC:</strong>
            <span>{{ cliente.imc }}</span>
          </div>


          <div class="info-item" v-if="cliente.cintura">
            <strong>Cintura:</strong>
            <span>{{ cliente.cintura }} cm</span>
          </div>
          <div class="info-item" v-if="cliente.cadera">
            <strong>Cadera:</strong>
            <span>{{ cliente.cadera }} cm</span>
          </div>
          <div class="info-item" v-if="cliente.pecho_torax">
            <strong>Pecho / Tórax:</strong>
            <span>{{ cliente.pecho_torax }} cm</span>
          </div>


          <div class="info-item" v-if="cliente.antebrazo">
            <strong>Brazo:</strong>
            <span>{{ cliente.antebrazo }} cm</span>
          </div>
          <div class="info-item" v-if="cliente.muslo">
            <strong>Piernas:</strong>
            <span>{{ cliente.muslo }} cm</span>
          </div>

          <div class="info-item" v-if="cliente.observaciones">
            <strong>Observaciones:</strong>
            <span>{{ cliente.observaciones }}</span>
          </div>
        </div>
      </template>
    </Card>
    <br />

    <!-- Historial de asistencias -->
    <Card class="historial-asistencias">
      <template #title>
        <h3>
          <i class="pi pi-calendar"></i> Historial de Asistencias (Últimas 10)
        </h3>
      </template>
      <template #content>
        <DataTable
          :value="cliente.asistencias"
          :loading="loading"
          responsiveLayout="scroll"
          stripedRows
        >
          <Column field="fecha_ingreso" header="Fecha y Hora" sortable>
            <template #body="{ data }">
              {{ data.fecha_ingreso }}
            </template>
          </Column>

          <Column field="membresia_valida" header="Estado Membresía">
            <template #body="{ data }">
              <Tag
                :value="data.membresia_valida ? 'Válida' : 'Vencida'"
                :severity="data.membresia_valida ? 'success' : 'danger'"
              />
            </template>
          </Column>

          <Column field="observaciones" header="Observaciones">
            <template #body="{ data }">
              {{ data.observaciones || "-" }}
            </template>
          </Column>
        </DataTable>

        <div
          v-if="!cliente.asistencias || cliente.asistencias.length === 0"
          class="no-data"
        >
          <p>
            <i class="pi pi-info-circle"></i> No hay registros de asistencia
          </p>
        </div>
      </template>
    </Card>

    <!-- Diálogo para renovar membresía -->
    <Dialog
      v-model:visible="mostrarDialogoRenovacion"
      header="Renovar Membresía"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <div class="dialog-content">
        <div class="renovacion-info">
          <div class="info-item">
            <strong>Membresía:</strong> {{ membresiaActiva?.nombre }}
          </div>
          <div class="info-item">
            <strong>Duración:</strong> {{ membresiaActiva?.duracion_dias }} días
          </div>
          <div class="info-item">
            <strong>Nueva fecha de vencimiento:</strong>
            {{ calcularNuevaFechaVencimiento() }}
          </div>
        </div>

        <div class="form-group">
          <label>Precio a pagar:</label>
          <InputText
            v-model.number="formularioRenovacion.precio_pagado"
            type="number"
            min="0"
            step="0.01"
            placeholder="Precio de la renovación"
          />
        </div>

        <div class="form-group">
          <label>Estado de pago:</label>
          <Dropdown
            v-model="formularioRenovacion.estado_pago"
            :options="[
              { label: 'Pagado', value: 'pagado' },
              { label: 'Pendiente', value: 'pendiente' },
            ]"
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione estado de pago"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancelar"
          class="p-button-text"
          @click="cerrarDialogoRenovacion"
        />
        <Button
          label="Renovar"
          @click="renovarMembresia"
          :loading="renovandoMembresia"
          :disabled="!formularioRenovacion.precio_pagado"
        />
      </template>
    </Dialog>

    <!-- Diálogo para asignar membresía -->
    <Dialog
      v-model:visible="mostrarDialogoAsignacion"
      header="Asignar Membresía"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <div class="dialog-content">
        <div class="form-group">
          <label>Seleccionar Membresía</label>
          <Dropdown
            v-model="formularioAsignacion.membresia_id"
            :options="membresiasDisponibles"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione una membresía"
            class="w-full"
          />
        </div>
        <div class="form-group">
          <label>Fecha de Inicio</label>
          <Calendar
            v-model="formularioAsignacion.fecha_inicio"
            dateFormat="dd/mm/yy"
            showIcon
          />
        </div>
        <div class="form-group">
          <label>Precio Pagado</label>
          <InputText
            v-model="formularioAsignacion.precio_pagado"
            type="number"
            step="0.01"
            placeholder="0.00"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancelar"
          class="p-button-text"
          @click="cerrarDialogoAsignacion"
        />
        <Button
          label="Asignar"
          @click="asignarMembresia"
          :loading="asignandoMembresia"
        />
      </template>
    </Dialog>

    <!-- Diálogo para cambiar membresía -->
    <Dialog
      v-model:visible="mostrarDialogoCambio"
      header="Cambiar Membresía"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <div class="dialog-content">
        <div class="form-group">
          <label>Nueva Membresía:</label>
          <Dropdown
            v-model="formularioCambio.nueva_membresia_id"
            :options="membresiasDisponibles"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione una membresía"
            :showClear="true"
            @change="actualizarPrecioCambio"
          />
        </div>

        <div class="form-group">
          <label>Precio a pagar:</label>
          <InputText
            v-model.number="formularioCambio.precio_pagado"
            type="number"
            min="0"
            step="0.01"
            placeholder="Precio del cambio"
          />
        </div>

        <div class="form-group">
          <label>Estado de pago:</label>
          <Dropdown
            v-model="formularioCambio.estado_pago"
            :options="[
              { label: 'Pagado', value: 'pagado' },
              { label: 'Pendiente', value: 'pendiente' },
            ]"
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione estado de pago"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancelar"
          class="p-button-text"
          @click="cerrarDialogoCambio"
        />
        <Button
          label="Cambiar"
          @click="cambiarMembresia"
          :loading="cambiandoMembresia"
          :disabled="
            !formularioCambio.nueva_membresia_id ||
            !formularioCambio.precio_pagado
          "
        />
      </template>
    </Dialog>

    <!-- Diálogo para ver historial de membresías -->
    <Dialog
      v-model:visible="mostrarHistorialMembresias"
      header="Historial de Membresías"
      :modal="true"
      :style="{ width: '800px' }"
    >
      <div class="dialog-content">
        <DataTable
          :value="historialMembresias"
          :loading="loading"
          responsiveLayout="scroll"
          stripedRows
        >
          <Column field="membresia.nombre" header="Membresía" sortable />
          <Column field="fecha_inicio" header="Fecha Inicio" sortable>
            <template #body="{ data }">
              {{ data.fecha_inicio }}
            </template>
          </Column>
          <Column field="fecha_vencimiento" header="Fecha Vencimiento" sortable>
            <template #body="{ data }">
              {{ data.fecha_vencimiento }}
            </template>
          </Column>
          <Column field="precio_pagado" header="Precio Pagado">
            <template #body="{ data }"> ${{ data.precio_pagado }} </template>
          </Column>
          <Column field="estado_pago" header="Estado">
            <template #body="{ data }">
              <Tag
                :value="data.estado_pago"
                :severity="
                  data.estado_pago === 'pagado' ? 'success' : 'warning'
                "
              />
            </template>
          </Column>
        </DataTable>
      </div>
      <template #footer>
        <Button
          label="Cerrar"
          class="p-button-text"
          @click="mostrarHistorialMembresias = false"
        />
      </template>
    </Dialog>

    <!-- Diálogo para editar membresía -->
    <Dialog
      v-model:visible="mostrarDialogoEdicionMembresia"
      header="Editar Membresía"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <div class="dialog-content">
        <div class="info-item">
          <strong>Membresía:</strong>
          <span>{{ membresiaActiva?.nombre }}</span>
        </div>

        <div class="form-group">
          <label>Fecha de inicio:</label>
          <Calendar
            v-model="formularioEdicionMembresia.fecha_inicio"
            dateFormat="dd/mm/yy"
            showIcon
          />
        </div>

        <div class="form-group">
          <label>Precio pagado:</label>
          <InputNumber
            v-model="formularioEdicionMembresia.precio_pagado"
            mode="currency"
            currency="USD"
            locale="es-CO"
          />
        </div>

   
      </div>
      <template #footer>
        <Button
          label="Cancelar"
          class="p-button-text"
          @click="cerrarDialogoEdicionMembresia"
        />
        <Button
          label="Guardar cambios"
          icon="pi pi-save"
          @click="guardarEdicionMembresia"
          :loading="editandoMembresia"
        />
      </template>
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
              Este enlace permite al cliente ver su información personal,
              membresía actual, historial de asistencias y estadísticas de forma
              segura y privada.
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
            <h4><i class="pi pi-cog"></i> Acciones</h4>
            <div class="acciones-grid">
              <Button
                label="Enviar por Email"
                icon="pi pi-send"
                class="p-button-outlined"
                @click="enviarPorEmail"
                :disabled="!cliente.correo"
              />
              <Button
                label="Compartir WhatsApp"
                icon="pi pi-whatsapp"
                class="p-button-outlined p-button-success"
                @click="compartirWhatsApp"
                :disabled="!cliente.telefono"
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
import { ref, reactive, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import { usePermissions } from "../composables/usePermissions";
import api from "../services/api";

export default {
  name: "ClienteDetalle",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const toast = useToast();
    const { permissions } = usePermissions();

    // Estado reactivo
    const cliente = ref(null);
    const membresiaActiva = ref(null);
    const estadisticas = ref({});
    const membresiasDisponibles = ref([]);
    const loading = ref(false);
    const registrandoIngreso = ref(false);
    const renovandoMembresia = ref(false);
    const asignandoMembresia = ref(false);
    const cambiandoMembresia = ref(false);
    const editandoMembresia = ref(false);
    const mostrarDialogoRenovacion = ref(false);
    const mostrarDialogoAsignacion = ref(false);
    const mostrarDialogoCambio = ref(false);
    const mostrarDialogoEdicionMembresia = ref(false);
    const mostrarHistorialMembresias = ref(false);
    const mostrarDialogoEnlacePublico = ref(false);
    const historialMembresias = ref([]);
    const generandoEnlace = ref(false);
    const enlacePublico = ref("");

    const formularioAsignacion = reactive({
      membresia_id: null,
      fecha_inicio: new Date(),
      precio_pagado: 0,
    });

    const formularioRenovacion = reactive({
      precio_pagado: 0,
      estado_pago: "pagado",
    });

    const formularioCambio = reactive({
      nueva_membresia_id: null,
      precio_pagado: 0,
      estado_pago: "pagado",
    });

    const formularioEdicionMembresia = reactive({
      membresia_id: null,
      cliente_membresia_id: null,
      fecha_inicio: null,
      fecha_vencimiento: null,
      precio_pagado: 0,
      estado_pago: "pagado",
    });

    // Métodos
    const cargarCliente = async () => {
      try {
        loading.value = true;
        const response = await api.getCliente(route.params.id);
        cliente.value = response.cliente;
        membresiaActiva.value = response.membresia_activa;
        estadisticas.value = response.estadisticas;
      } catch (error) {
        console.error("Error loading client:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Error al cargar información del cliente",
          life: 3000,
        });
        router.push("/clientes");
      } finally {
        loading.value = false;
      }
    };

    const cargarMembresias = async () => {
      try {
        const response = await api.getMembresias();
        membresiasDisponibles.value = response.data || response;
      } catch (error) {
        console.error("Error loading memberships:", error);
      }
    };

    const registrarIngreso = async () => {
      try {
        registrandoIngreso.value = true;
        await api.registrarIngreso({
          cliente_id: cliente.value.id,
          permitir_sin_membresia: true,
        });

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Ingreso registrado exitosamente",
          life: 3000,
        });

        cargarCliente(); // Recargar datos
      } catch (error) {
        const message =
          error.response?.data?.message || "Error al registrar ingreso";
        toast.add({
          severity: "error",
          summary: "Error",
          detail: message,
          life: 5000,
        });
      } finally {
        registrandoIngreso.value = false;
      }
    };

    const renovarMembresia = async () => {
      try {
        renovandoMembresia.value = true;
        await api.renovarMembresia(membresiaActiva.value.id, {
          precio_pagado: formularioRenovacion.precio_pagado,
          estado_pago: formularioRenovacion.estado_pago,
        });

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Membresía renovada exitosamente",
          life: 3000,
        });

        cerrarDialogoRenovacion();
        cargarCliente();
      } catch (error) {
        console.error("Error renovando membresía:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: error.response?.data?.message || "Error al renovar membresía",
          life: 3000,
        });
      } finally {
        renovandoMembresia.value = false;
      }
    };

    const asignarMembresia = async () => {
      try {
        asignandoMembresia.value = true;
        await api.asignarMembresia({
          cliente_id: cliente.value.id,
          membresia_id: formularioAsignacion.membresia_id,
          fecha_inicio: formularioAsignacion.fecha_inicio
            .toISOString()
            .split("T")[0],
          precio_pagado: formularioAsignacion.precio_pagado,
        });

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Membresía asignada exitosamente",
          life: 3000,
        });

        cerrarDialogoAsignacion();
        cargarCliente();
      } catch (error) {
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Error al asignar membresía",
          life: 3000,
        });
      } finally {
        asignandoMembresia.value = false;
      }
    };

    const cambiarMembresia = async () => {
      try {
        cambiandoMembresia.value = true;
        await api.cambiarMembresia(membresiaActiva.value.cliente_membresia_id, {
          nueva_membresia_id: formularioCambio.nueva_membresia_id,
          precio_pagado: formularioCambio.precio_pagado,
          estado_pago: formularioCambio.estado_pago,
        });

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Membresía cambiada exitosamente",
          life: 3000,
        });

        cerrarDialogoCambio();
        cargarCliente();
      } catch (error) {
        console.error("Error al cambiar membresía:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Error al cambiar membresía",
          life: 3000,
        });
      } finally {
        cambiandoMembresia.value = false;
      }
    };

    const editarCliente = () => {
      // Implementar navegación a formulario de edición
      router.push(`/clientes/${cliente.value.id}/editar`);
    };

    const generarEnlacePublico = async () => {
      try {
        generandoEnlace.value = true;
        const response = await api.generarEnlacePublico(cliente.value.id);
        enlacePublico.value = response.url;
        mostrarDialogoEnlacePublico.value = true;

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Enlace público generado correctamente",
          life: 3000,
        });
      } catch (error) {
        console.error("Error al generar enlace público:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "No se pudo generar el enlace público",
          life: 3000,
        });
      } finally {
        generandoEnlace.value = false;
      }
    };

    const copiarEnlace = async () => {
      try {
        await navigator.clipboard.writeText(enlacePublico.value);
        toast.add({
          severity: "success",
          summary: "Copiado",
          detail: "Enlace copiado al portapapeles",
          life: 2000,
        });
      } catch (error) {
        // Fallback para navegadores que no soportan clipboard API
        const textArea = document.createElement("textarea");
        textArea.value = enlacePublico.value;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);

        toast.add({
          severity: "success",
          summary: "Copiado",
          detail: "Enlace copiado al portapapeles",
          life: 2000,
        });
      }
    };

    const enviarPorEmail = () => {
      const asunto = encodeURIComponent("Tu información personal del gimnasio");
      const cuerpo = encodeURIComponent(
        `Hola ${cliente.value.nombre},\n\nPuedes consultar tu información personal, membresía y asistencias en el siguiente enlace:\n\n${enlacePublico.value}\n\nSaludos,\nEquipo del Gimnasio`
      );
      window.open(
        `mailto:${cliente.value.correo}?subject=${asunto}&body=${cuerpo}`
      );
    };

    const compartirWhatsApp = () => {
      const mensaje = encodeURIComponent(
        `Hola ${cliente.value.nombre}, puedes consultar tu información del gimnasio en: ${enlacePublico.value}`
      );
      const telefono = cliente.value.telefono.replace(/\D/g, ""); // Remover caracteres no numéricos
      window.open(`https://wa.me/${telefono}?text=${mensaje}`, "_blank");
    };

    const abrirEnlace = () => {
      window.open(enlacePublico.value, "_blank");
    };

    const verHistorialMembresias = async () => {
      try {
        loading.value = true;
        const response = await api.getHistorialMembresias(cliente.value.id);
        historialMembresias.value = response;
        mostrarHistorialMembresias.value = true;
      } catch (error) {
        console.error("Error al cargar historial de membresías:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "No se pudo cargar el historial de membresías",
          life: 3000,
        });
      } finally {
        loading.value = false;
      }
    };

    const actualizarPrecioCambio = () => {
      if (formularioCambio.nueva_membresia_id) {
        const membresiaSeleccionada = membresiasDisponibles.value.find(
          (m) => m.id === formularioCambio.nueva_membresia_id
        );
        if (membresiaSeleccionada) {
          formularioCambio.precio_pagado = membresiaSeleccionada.precio;
        }
      }
    };

    const cerrarDialogoAsignacion = () => {
      mostrarDialogoAsignacion.value = false;
      formularioAsignacion.membresia_id = null;
      formularioAsignacion.fecha_inicio = new Date();
      formularioAsignacion.precio_pagado = 0;
    };

    const cerrarDialogoRenovacion = () => {
      mostrarDialogoRenovacion.value = false;
      formularioRenovacion.precio_pagado = 0;
      formularioRenovacion.estado_pago = "pagado";
    };

    const cerrarDialogoCambio = () => {
      mostrarDialogoCambio.value = false;
      formularioCambio.nueva_membresia_id = null;
      formularioCambio.precio_pagado = 0;
      formularioCambio.estado_pago = "pagado";
    };

    const abrirDialogoRenovacion = () => {
      // Inicializar formulario con precio de la membresía actual
      formularioRenovacion.precio_pagado = membresiaActiva.value?.precio || 0;
      formularioRenovacion.estado_pago = "pagado";
      mostrarDialogoRenovacion.value = true;
    };

    const abrirDialogoCambio = () => {
      formularioCambio.nueva_membresia_id = null;
      formularioCambio.precio_pagado = 0;
      formularioCambio.estado_pago = "pagado";
      mostrarDialogoCambio.value = true;
    };

    const calcularNuevaFechaVencimiento = () => {
      if (!membresiaActiva.value) return "";
      const fecha = new Date(membresiaActiva.value.fecha_vencimiento);
      fecha.setDate(fecha.getDate() + membresiaActiva.value.duracion_dias);
      return fecha.toLocaleDateString("es-ES");
    };

    const abrirDialogoEdicionMembresia = () => {
      if (!membresiaActiva.value) return;

      formularioEdicionMembresia.cliente_membresia_id =
        membresiaActiva.value.cliente_membresia_id;
      formularioEdicionMembresia.membresia_id =
        membresiaActiva.value.membresia_id;
      formularioEdicionMembresia.fecha_inicio = new Date(
        membresiaActiva.value.fecha_inicio
      );
      formularioEdicionMembresia.fecha_vencimiento = new Date(
        membresiaActiva.value.fecha_vencimiento
      );
      formularioEdicionMembresia.precio_pagado =
        membresiaActiva.value.precio_pagado || 0;
      formularioEdicionMembresia.estado_pago =
        membresiaActiva.value.estado_pago || "pagado";

      mostrarDialogoEdicionMembresia.value = true;
    };

    const cerrarDialogoEdicionMembresia = () => {
      mostrarDialogoEdicionMembresia.value = false;
    };

    const guardarEdicionMembresia = async () => {
      try {
        editandoMembresia.value = true;

        await api.editarMembresiaCliente(
          formularioEdicionMembresia.cliente_membresia_id,
          {
            fecha_inicio: formularioEdicionMembresia.fecha_inicio
              .toISOString()
              .split("T")[0],

            precio_pagado: formularioEdicionMembresia.precio_pagado,
          
          }
        );

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Membresía actualizada exitosamente",
          life: 3000,
        });

        cerrarDialogoEdicionMembresia();
        cargarCliente(); // Recargar datos del cliente para actualizar la vista
      } catch (error) {
        console.error("Error al editar membresía:", error);
        toast.add({
          severity: "error",
          summary: "Error",
          detail:
            error.response?.data?.message || "Error al editar la membresía",
          life: 3000,
        });
      } finally {
        editandoMembresia.value = false;
      }
    };

    // Utilidades
    const formatearFecha = (fecha) => {
      return new Intl.DateTimeFormat("es-CO", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        timeZone: "America/Bogota",
      }).format(new Date(fecha));
    };

    const formatearFechaHora = (fecha) => {
      return new Intl.DateTimeFormat("es-CO", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        timeZone: "America/Bogota",
      });
    };

    // Lifecycle
    onMounted(() => {
      cargarCliente();
      cargarMembresias();
    });

    return {
      // Estado
      cliente,
      membresiaActiva,
      estadisticas,
      membresiasDisponibles,
      historialMembresias,
      loading,
      registrandoIngreso,
      renovandoMembresia,
      asignandoMembresia,
      cambiandoMembresia,
      editandoMembresia,
      mostrarDialogoRenovacion,
      mostrarDialogoAsignacion,
      mostrarDialogoCambio,
      mostrarDialogoEdicionMembresia,
      mostrarHistorialMembresias,
      mostrarDialogoEnlacePublico,
      generandoEnlace,
      enlacePublico,
      formularioAsignacion,
      formularioRenovacion,
      formularioCambio,
      formularioEdicionMembresia,

      // Métodos
      registrarIngreso,
      renovarMembresia,
      asignarMembresia,
      cambiarMembresia,
      verHistorialMembresias,
      editarCliente,
      generarEnlacePublico,
      copiarEnlace,
      enviarPorEmail,
      compartirWhatsApp,
      abrirEnlace,
      cerrarDialogoAsignacion,
      cerrarDialogoRenovacion,
      cerrarDialogoCambio,
      cerrarDialogoEdicionMembresia,
      abrirDialogoRenovacion,
      abrirDialogoCambio,
      abrirDialogoEdicionMembresia,
      guardarEdicionMembresia,
      actualizarPrecioCambio,
      calcularNuevaFechaVencimiento,
      formatearFecha,
      formatearFechaHora,
      permissions,
    };
  },
};
</script>

<style scoped>
.cliente-detalle {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.cliente-title h1 {
  margin: 0;
  color: #2c3e50;
}

.cliente-cedula {
  color: #6b7280;
  font-size: 0.9rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.cliente-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.mb {
  margin-bottom: 2rem;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
  border-bottom: none;
}

.membresia-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.membresia-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.membresia-header h4 {
  margin: 0;
  color: #2c3e50;
}

.membresia-detalles {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detalle-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.membresia-acciones {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}

.membresia-acciones .p-button {
  flex: 1;
  min-width: 120px;
}

.sin-membresia {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.sin-membresia i {
  margin-right: 0.5rem;
}

.estadisticas-asistencia {
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #3b82f6;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: #6b7280;
  font-size: 0.9rem;
}

.historial-asistencias {
  margin-bottom: 2rem;
}

.no-data {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.no-data i {
  margin-right: 0.5rem;
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

.renovacion-info,
.info-item {
  margin-bottom: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
  border-bottom: none;
}

.text-danger {
  color: #dc2626;
  font-weight: bold;
}

.text-warning {
  color: #f59e0b;
  font-weight: bold;
}

.w-full {
  width: 100%;
}

@media (max-width: 768px) {
  .cliente-info-grid {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .header-actions {
    justify-content: center;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
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
  font-family: "Courier New", monospace;
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
</style>
