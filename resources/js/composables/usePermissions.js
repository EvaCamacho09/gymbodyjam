import { computed } from 'vue';

// Composable para manejar permisos de usuario
export function usePermissions() {
  // Obtener usuario del localStorage
  const getUser = () => {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  };

  const user = computed(() => getUser());
  const userRole = computed(() => {
    const currentUser = user.value;
    return currentUser?.role || 'secretario';
  });

  // Verificar si es administrador
  const isAdmin = computed(() => userRole.value === 'admin');

  // Verificar si es secretario
  const isSecretary = computed(() => userRole.value === 'secretario');

  // Permisos específicos
  const permissions = computed(() => ({
    // Dashboard - Ambos pueden ver
    canViewDashboard: true,

    // Clientes
    canViewClientes: true,
    canCreateClientes: true,
    canEditClientes: isAdmin.value,
    canDeleteClientes: isAdmin.value,

    // Membresías
    canViewMembresias: isAdmin.value,
    canCreateMembresias: isAdmin.value,
    canEditMembresias: isAdmin.value,
    canDeleteMembresias: isAdmin.value,
    canAssignMembresias: true, // Ambos pueden asignar
    canRenewMembresias: true,  // Ambos pueden renovar
    canChangeMembresias: isAdmin.value,

    // Asistencias
    canViewAsistencias: true,
    canCreateAsistencias: true,
    canDeleteAsistencias: isAdmin.value,

    // Administración
    canManageUsers: isAdmin.value,
    canViewReports: isAdmin.value,
    canViewFullStats: isAdmin.value
  }));

  return {
    user,
    userRole,
    isAdmin,
    isSecretary,
    permissions
  };
}
