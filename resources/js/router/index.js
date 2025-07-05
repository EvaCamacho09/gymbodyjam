import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import Clientes from '../views/Clientes.vue';
import ClienteDetalle from '../views/ClienteDetalle.vue';
import ClienteEditar from '../views/ClienteEditar.vue';
import Membresias from '../views/Membresias.vue';
import Asistencias from '../views/Asistencias.vue';
import Layout from '../components/Layout.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    component: Layout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: '/clientes',
        name: 'Clientes',
        component: Clientes
      },
      {
        path: '/clientes/:id',
        name: 'ClienteDetalle',
        component: ClienteDetalle
      },
      {
        path: '/clientes/:id/editar',
        name: 'ClienteEditar',
        component: ClienteEditar
      },
      {
        path: '/membresias',
        name: 'Membresias',
        component: Membresias
      },
      {
        path: '/asistencias',
        name: 'Asistencias',
        component: Asistencias
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Guard de navegación
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.meta.requiresGuest && token) {
    next('/');
  } else {
    next();
  }
});

export default router;
