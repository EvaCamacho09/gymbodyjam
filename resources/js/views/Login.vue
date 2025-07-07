<template>
  <div class="login-container">
    <Card class="login-card">
      <template #header>
        <div class="login-header">
          <i class="pi pi-chart-line" style="font-size: 2rem; color: #3b82f6;"></i>
          <h2>Gym Manager</h2>
        </div>
      </template>
      
      <template #content>
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Email</label>
            <InputText
              id="email"
              v-model="form.email"
              type="email"
              placeholder="Ingresa tu email"
              :class="{ 'p-invalid': errors.email }"
              required
            />
            <small v-if="errors.email" class="p-error">{{ errors.email }}</small>
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña</label>
            <Password
              id="password"
              v-model="form.password"
              placeholder="Ingresa tu contraseña"
              :class="{ 'p-invalid': errors.password }"
              :feedback="false"
              toggleMask
              required
            />
            <small v-if="errors.password" class="p-error">{{ errors.password }}</small>
          </div>
          
          <Button
            type="submit"
            label="Iniciar Sesión"
            :loading="loading"
            class="login-button"
            icon="pi pi-sign-in"
          />
        </form>
      </template>
      
    
    </Card>
  </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import api from '../services/api';

export default {
  name: 'Login',
  setup() {
    const router = useRouter();
    const toast = useToast();
    const loading = ref(false);
    
    const form = reactive({
      email: '',
      password: ''
    });
    
    const errors = reactive({
      email: '',
      password: ''
    });

    const clearErrors = () => {
      errors.email = '';
      errors.password = '';
    };

    const handleLogin = async () => {
      clearErrors();
      loading.value = true;
      
      try {
        const response = await api.login(form);
        
        // Guardar token y usuario
        localStorage.setItem('token', response.token);
        localStorage.setItem('user', JSON.stringify(response.user));
        
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Has iniciado sesión correctamente',
          life: 3000
        });
        
        router.push('/');
      } catch (error) {
        console.error('Login error:', error);
        
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
            detail: error.response?.data?.message || 'Error al iniciar sesión',
            life: 5000
          });
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      errors,
      loading,
      handleLogin
    };
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1rem;
}

.login-card {
  width: 100%;
  max-width: 400px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.login-header {
  text-align: center;
  padding: 2rem 0 1rem;
}

.login-header h2 {
  margin-top: 0.5rem;
  color: #1f2937;
  font-weight: 600;
}

.login-form {
  padding: 0 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
}

.form-group .p-inputtext,
.form-group .p-password {
  width: 100%;
}

.login-button {
  width: 100%;
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  margin-top: 1rem;
}

.login-footer {
  text-align: center;
  padding: 1rem;
  background: #f8fafc;
  border-top: 1px solid #e5e7eb;
  color: #6b7280;
}

.p-error {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style>
