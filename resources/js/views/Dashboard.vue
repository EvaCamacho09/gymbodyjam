<template>
  <div class="dashboard">
    

    <!-- Estadísticas principales -->
    <div class="stats-grid">
      <Card class="stat-card total-clientes clickable" @click="navegarClientes('activos')">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-users"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.total_clientes || 0 }}</h3>
              <p>Clientes Activos</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card clientes-morosos clickable" @click="navegarClientes('morosos')">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.clientes_morosos || 0 }}</h3>
              <p>Clientes Morosos</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card proximos-vencer clickable" @click="navegarClientes('proximos')">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-clock"></i>
            </div>
            <div class="stat-info">
              <h3>{{ estadisticas.clientes_proximos_vencer || 0 }}</h3>
              <p>Próximos a Vencer</p>
            </div>
          </div>
        </template>
      </Card>

      <Card class="stat-card">
        <template #content>
          <div class="stat-content">
            <div class="stat-icon">
              <i class="pi pi-dollar"></i>
            </div>
            <div class="stat-info">
              <h3>{{ formatCurrency(estadisticas.ingresos_mes) }}</h3>
              <p>Ingresos del Mes</p>
            </div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Botones de acceso rápido -->
    <div class="quick-actions">
      <Card>
        <template #content>
          <div class="actions-content">
            <h3>Acciones Rápidas</h3>
            <div class="actions-grid">
              <Button 
                icon="pi pi-plus" 
                label="Nuevo Cliente" 
                class="p-button-outlined"
                @click="$router.push('/clientes/nuevo')"
              />
              <Button 
                icon="pi pi-list" 
                label="Ver Clientes"
                class="p-button-outlined"
                @click="$router.push('/clientes')"
              />
              <Button 
                icon="pi pi-calendar" 
                label="Asistencias"
                class="p-button-outlined"
                @click="$router.push('/asistencias')"
              />
              <Button 
                icon="pi pi-credit-card" 
                label="Membresías"
                class="p-button-outlined"
                @click="$router.push('/membresias')"
              />
            </div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Información de membresías -->
    <div class="info-grid">
      <Card>
        <template #content>
          <div class="membresias-content">
            <h3>Membresías Activas</h3>
            <div v-if="estadisticas.membresias?.length" class="membresias-list">
              <div 
                v-for="membresia in estadisticas.membresias" 
                :key="membresia.id" 
                class="membresia-item"
              >
                <div class="membresia-info">
                  <h4>{{ membresia.nombre }}</h4>
                  <p>{{ formatCurrency(membresia.precio) }}/mes</p>
                </div>
                <div class="membresia-stats">
                  <span class="count">{{ membresia.clientes_count }}</span>
                  <small>clientes</small>
                </div>
              </div>
            </div>
            <div v-else class="no-data">
              <p>No hay información de membresías disponible</p>
            </div>
          </div>
        </template>
      </Card>

      <Card>
        <template #content>
          <div class="actividad-content">
            <h3>Actividad Reciente</h3>
            <div v-if="actividad.asistencias_hoy > 0" class="actividad-item">
              <h4>Asistencias Hoy: {{ actividad.asistencias_hoy }}</h4>
            </div>
            <div v-if="actividad.ultimos_clientes?.length" class="actividad-list">
              <h4>Últimos Clientes Registrados</h4>
              <div 
                v-for="cliente in actividad.ultimos_clientes"
                :key="cliente.id"
                class="cliente-item"
              >
                <span class="cliente-nombre">{{ cliente.nombre }} {{ cliente.apellido }}</span>
                <small class="cliente-fecha">{{ formatDate(cliente.created_at) }}</small>
              </div>
            </div>
            <div v-if="!actividad.asistencias_hoy && !actividad.ultimos_clientes?.length" class="no-data">
              <p>No hay actividad reciente</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Card from 'primevue/card';
import Button from 'primevue/button';
import api from '../services/api';

export default {
  name: 'Dashboard',
  components: {
    Card,
    Button
  },
  setup() {
    const router = useRouter();
    const estadisticas = ref({
      total_clientes: 0,
      clientes_morosos: 0,
      clientes_proximos_vencer: 0,
      ingresos_mes: 0,
      membresias: []
    });
    const actividad = ref({
      asistencias_hoy: 0,
      ultimos_clientes: [],
      ultimas_membresias_asignadas: []
    });
    const loading = ref(false);
    const debugInfo = ref({});

    // Función segura para acceder a localStorage
    const getFromLocalStorage = (key) => {
      if (typeof window !== 'undefined' && window.localStorage) {
        return localStorage.getItem(key);
      }
      return null;
    };

    const setToLocalStorage = (key, value) => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem(key, value);
      }
    };

    const loadEstadisticas = async () => {
      try {
        loading.value = true;
        console.log('Cargando estadísticas...');
        console.log('Token en localStorage:', getFromLocalStorage('token'));
        
        const response = await api.get('/dashboard/estadisticas');
        console.log('Respuesta estadísticas:', response);
        estadisticas.value = response;
        debugInfo.value.estadisticasLoaded = true;
      } catch (error) {
        console.error('Error al cargar estadísticas:', error);
        console.error('Error status:', error.response?.status);
        console.error('Error data:', error.response?.data);
        debugInfo.value.estadisticasError = error.response?.data || error.message;
        
        // Solo establecer valores por defecto si estadisticas está completamente vacío
        if (!estadisticas.value || (!estadisticas.value.total_clientes && estadisticas.value.total_clientes !== 0)) {
          estadisticas.value = {
            total_clientes: 0,
            clientes_morosos: 0,
            clientes_proximos_vencer: 0,
            ingresos_mes: 0,
            membresias: []
          };
        }
      } finally {
        loading.value = false;
      }
    };

    const loadActividad = async () => {
      try {
        console.log('Cargando actividad...');
        const response = await api.get('/dashboard/actividad-reciente');
        console.log('Respuesta actividad:', response.data);
        actividad.value = response.data;
        debugInfo.value.actividadLoaded = true;
      } catch (error) {
        console.error('Error al cargar actividad:', error);
        console.error('Error status:', error.response?.status);
        console.error('Error data:', error.response?.data);
        debugInfo.value.actividadError = error.response?.data || error.message;
        
        // Solo establecer valores por defecto si actividad está completamente vacío
        if (!actividad.value || (!actividad.value.asistencias_hoy && actividad.value.asistencias_hoy !== 0)) {
          actividad.value = {
            asistencias_hoy: 0,
            ultimos_clientes: [],
            ultimas_membresias_asignadas: []
          };
        }
      }
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(amount || 0);
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    const navegarClientes = (filtro) => {
      router.push({
        name: 'Clientes',
        query: { estado: filtro }
      });
    };

    // Test de conectividad
    const testConnectivity = async () => {
      try {
        console.log('Testing API connectivity...');
        const response = await api.get('/test');
        console.log('API Test Response:', response.data);
        debugInfo.value.apiTest = response.data;
      } catch (error) {
        console.error('API Test Error:', error);
        debugInfo.value.apiTestError = error.message;
      }
    };

    const setupTestAuth = () => {
      const token = '10|7SUQt2OKzIAPwbJAk1T6EkuXcABDNhZM3jEPEswEe129b3c8';
      const user = {
        id: 1,
        name: 'Administrador',
        email: 'admin@gym.com',
        role: 'admin'
      };
      
      setToLocalStorage('token', token);
      setToLocalStorage('user', JSON.stringify(user));
      
      console.log('✅ Test auth setup complete');
      debugInfo.value.authSetup = true;
      
      // Reload data
      reloadData();
    };

    const reloadData = () => {
      debugInfo.value = { reloading: true };
      testConnectivity();
      loadEstadisticas();
      loadActividad();
    };

    onMounted(() => {
      testConnectivity();
      loadEstadisticas();
      loadActividad();
    });

    return {
      estadisticas,
      actividad,
      loading,
      debugInfo,
      formatCurrency,
      formatDate,
      navegarClientes,
      setupTestAuth,
      reloadData,
      getFromLocalStorage
    };
  }
}
</script>

<style scoped>
.dashboard {
  padding: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  transition: all 0.3s ease;
}

.stat-card.clickable {
  cursor: pointer;
}

.stat-card.clickable:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-card.total-clientes:hover {
  border-left: 4px solid #10b981;
}

.stat-card.clientes-morosos:hover {
  border-left: 4px solid #ef4444;
}

.stat-card.proximos-vencer:hover {
  border-left: 4px solid #f59e0b;
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.total-clientes .stat-icon {
  background: #e6fffa;
  color: #10b981;
}

.clientes-morosos .stat-icon {
  background: #fef2f2;
  color: #ef4444;
}

.proximos-vencer .stat-icon {
  background: #fefbf2;
  color: #f59e0b;
}

.stat-card:last-child .stat-icon {
  background: #f0f9ff;
  color: #3b82f6;
}

.stat-info h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-info p {
  margin: 0.5rem 0 0 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.quick-actions {
  margin-bottom: 2rem;
}

.actions-content h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #1f2937;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}

.membresias-content h3,
.actividad-content h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #1f2937;
}

.membresias-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.membresia-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.5rem;
  border-left: 3px solid #3b82f6;
}

.membresia-info h4 {
  margin: 0;
  color: #1f2937;
  font-size: 1rem;
}

.membresia-info p {
  margin: 0.25rem 0 0 0;
  color: #059669;
  font-weight: 600;
}

.membresia-stats {
  text-align: center;
}

.membresia-stats .count {
  display: block;
  font-size: 1.25rem;
  font-weight: bold;
  color: #3b82f6;
}

.membresia-stats small {
  color: #6b7280;
}

.actividad-list {
  margin-top: 1rem;
}

.actividad-list h4 {
  margin: 0 0 0.75rem 0;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cliente-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.cliente-item:last-child {
  border-bottom: none;
}

.cliente-nombre {
  font-weight: 500;
  color: #1f2937;
}

.cliente-fecha {
  color: #6b7280;
}

.actividad-item h4 {
  margin: 0;
  color: #059669;
  font-size: 1.125rem;
}

.no-data {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

@media (max-width: 768px) {
  .dashboard {
    padding: 0.5rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
