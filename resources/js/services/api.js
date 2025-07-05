import axios from 'axios';

const API_BASE_URL = '/api';

// Configurar axios
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor para incluir token en todas las peticiones
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Interceptor para manejar respuestas
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default {
  // Autenticación
  async login(credentials) {
    const response = await api.post('/login', credentials);
    return response.data;
  },

  async logout() {
    const response = await api.post('/logout');
    return response.data;
  },

  async me() {
    const response = await api.get('/me');
    return response.data;
  },

  async register(userData) {
    const response = await api.post('/register', userData);
    return response.data;
  },

  // Dashboard
  async getEstadisticas() {
    const response = await api.get('/dashboard/estadisticas');
    return response.data;
  },

  async getActividadReciente() {
    const response = await api.get('/dashboard/actividad-reciente');
    return response.data;
  },

  // Clientes
  async getClientes(params = {}) {
    const response = await api.get('/clientes', { params });
    return response.data;
  },

  async getCliente(id) {
    const response = await api.get(`/clientes/${id}`);
    return response.data;
  },

  async createCliente(clienteData) {
    const response = await api.post('/clientes', clienteData);
    return response.data;
  },

  async updateCliente(id, clienteData) {
    const response = await api.put(`/clientes/${id}`, clienteData);
    return response.data;
  },

  async deleteCliente(id) {
    const response = await api.delete(`/clientes/${id}`);
    return response.data;
  },

  async getClientesMorosos() {
    const response = await api.get('/clientes-morosos');
    return response.data;
  },

  async getClientesProximosVencer() {
    const response = await api.get('/clientes-proximos-vencer');
    return response.data;
  },

  async generarEnlacePublico(clienteId) {
    const response = await api.post(`/clientes/${clienteId}/enlace-publico`);
    return response.data;
  },

  // Membresías
  async getMembresias() {
    const response = await api.get('/membresias');
    return response.data;
  },

  async getMembresia(id) {
    const response = await api.get(`/membresias/${id}`);
    return response.data;
  },

  async createMembresia(membresiaData) {
    const response = await api.post('/membresias', membresiaData);
    return response.data;
  },

  async updateMembresia(id, membresiaData) {
    const response = await api.put(`/membresias/${id}`, membresiaData);
    return response.data;
  },

  async deleteMembresia(id) {
    const response = await api.delete(`/membresias/${id}`);
    return response.data;
  },

  async asignarMembresia(data) {
    const response = await api.post('/asignar-membresia', data);
    return response.data;
  },

  async renovarMembresia(clienteMembresiaId, data) {
    const response = await api.post(`/renovar-membresia/${clienteMembresiaId}`, data);
    return response.data;
  },

  async cambiarMembresia(clienteMembresiaId, data) {
    const response = await api.post(`/cambiar-membresia/${clienteMembresiaId}`, data);
    return response.data;
  },

  async getHistorialMembresias(clienteId) {
    const response = await api.get(`/historial-membresias/${clienteId}`);
    return response.data;
  },

  // Asistencias
  async getAsistencias(params = {}) {
    const response = await api.get('/asistencias', { params });
    return response.data;
  },

  async getAsistencia(id) {
    const response = await api.get(`/asistencias/${id}`);
    return response.data;
  },

  async registrarIngreso(data) {
    const response = await api.post('/registrar-ingreso', data);
    return response.data;
  },

  async registrarIngresoPorBusqueda(data) {
    const response = await api.post('/registrar-ingreso-busqueda', data);
    return response.data;
  },

  async getEstadisticasAsistencias() {
    const response = await api.get('/estadisticas-asistencias');
    return response.data;
  },

  async deleteAsistencia(id) {
    const response = await api.delete(`/asistencias/${id}`);
    return response.data;
  },

  // Métodos HTTP genéricos
  async get(url, config = {}) {
    const response = await api.get(url, config);
    return response.data;
  },

  async post(url, data = {}, config = {}) {
    const response = await api.post(url, data, config);
    return response.data;
  },

  async put(url, data = {}, config = {}) {
    const response = await api.put(url, data, config);
    return response.data;
  },

  async delete(url, config = {}) {
    const response = await api.delete(url, config);
    return response.data;
  }
};
