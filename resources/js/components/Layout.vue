<template>
  <div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>
          <i class="pi pi-chart-line"></i>
          Gym Manager
        </h2>
      </div>
      
      <nav class="sidebar-nav">
        <router-link to="/" class="nav-item" active-class="active">
          <i class="pi pi-home"></i>
          Dashboard
        </router-link>
        
        <router-link to="/clientes" class="nav-item" active-class="active">
          <i class="pi pi-users"></i>
          Clientes
        </router-link>
        
        <router-link 
          v-if="permissions.canViewMembresias" 
          to="/membresias" 
          class="nav-item" 
          active-class="active"
        >
          <i class="pi pi-credit-card"></i>
          Membresías
        </router-link>
        
        <router-link to="/asistencias" class="nav-item" active-class="active">
          <i class="pi pi-calendar-times"></i>
          Asistencias
        </router-link>
        
        <a href="/asistencia" target="_blank" class="nav-item nav-external">
          <i class="pi pi-external-link"></i>
          Asistencia Pública
        </a>
      </nav>
    </div>

    <!-- Main content -->
    <div class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-content">
          <div class="header-left">
            <h1>{{ $route.name }}</h1>
          </div>
          
          <div class="header-right">
            <span class="user-info">
              <i class="pi pi-user"></i>
              {{ user?.name }} ({{ user?.role }})
            </span>
            
            <Button
              icon="pi pi-sign-out"
              label="Salir"
              class="p-button-text"
              @click="handleLogout"
            />
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="page-content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { usePermissions } from '../composables/usePermissions';
import api from '../services/api';

export default {
  name: 'Layout',
  setup() {
    const router = useRouter();
    const toast = useToast();
    const user = ref(null);
    const { permissions } = usePermissions();

    const loadUser = async () => {
      try {
        const userData = localStorage.getItem('user');
        if (userData) {
          user.value = JSON.parse(userData);
        }
      } catch (error) {
        console.error('Error loading user:', error);
      }
    };

    const handleLogout = async () => {
      try {
        await api.logout();
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        toast.add({
          severity: 'success',
          summary: 'Sesión cerrada',
          detail: 'Has cerrado sesión exitosamente',
          life: 3000
        });
        router.push('/login');
      } catch (error) {
        console.error('Error logging out:', error);
        // Cerrar sesión localmente de todos modos
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push('/login');
      }
    };

    onMounted(() => {
      loadUser();
    });

    return {
      user,
      permissions,
      handleLogout
    };
  }
}
</script>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
}

.sidebar {
  width: 250px;
  background: #1e293b;
  color: white;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid #334155;
}

.sidebar-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: #cbd5e1;
  text-decoration: none;
  transition: all 0.2s;
}

.nav-item:hover {
  background: #334155;
  color: white;
}

.nav-item.active {
  background: #3b82f6;
  color: white;
}

.nav-external {
  border-top: 1px solid #334155;
  margin-top: 0.5rem;
  position: relative;
}

.nav-external:hover {
  background: #059669;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left h1 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.page-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .layout {
    flex-direction: column;
  }
  
  .sidebar {
    width: 100%;
    height: auto;
  }
  
  .sidebar-nav {
    display: flex;
    overflow-x: auto;
  }
  
  .nav-item {
    white-space: nowrap;
  }
}
</style>
