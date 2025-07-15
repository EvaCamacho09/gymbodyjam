<template>
  <!-- Slider de fondo -->
  <div class="background-slider">
    <div
      v-for="(image, index) in gymImages"
      :key="index"
      class="background-image"
      :class="{ active: currentImageIndex === index }"
      :style="{ backgroundImage: `url(${image})` }"
    ></div>
  </div>

  <!-- Overlay con blur -->
  <div class="background-overlay"></div>

  <!-- Indicador de imagen -->
  <div class="image-indicator">
    <span>Imagen {{ currentImageIndex + 1 }}/{{ gymImages.length }}</span>
  </div>

  <div class="login-container">
    <Card class="login-card">
      <template #header>
        <div class="login-header">
          <i
            class="pi pi-chart-line"
            style="font-size: 2rem; color: #3b82f6"
          ></i>
          <h2>Gym Manager</h2>
          <div class="subtitle">Acceso Administrador</div>
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
            <small v-if="errors.email" class="p-error">{{
              errors.email
            }}</small>
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
            <small v-if="errors.password" class="p-error">{{
              errors.password
            }}</small>
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
import { ref, reactive, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import api from "../services/api";

export default {
  name: "Login",
  setup() {
    const router = useRouter();
    const toast = useToast();
    const loading = ref(false);

    // Variables para el slider de imágenes
    const currentImageIndex = ref(0);
    let imageInterval = null;

    // URLs de las imágenes de Pexels
    const gymImages = ref([
      "https://images.pexels.com/photos/1229356/pexels-photo-1229356.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
      "https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
      "https://images.pexels.com/photos/4944972/pexels-photo-4944972.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
      "https://images.pexels.com/photos/416747/pexels-photo-416747.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
      "https://images.pexels.com/photos/3112004/pexels-photo-3112004.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
      "https://images.pexels.com/photos/1954524/pexels-photo-1954524.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop",
    ]);

    const form = reactive({
      email: "",
      password: "",
    });

    const errors = reactive({
      email: "",
      password: "",
    });

    // Función para cambiar imagen
    const changeImage = () => {
      currentImageIndex.value =
        (currentImageIndex.value + 1) % gymImages.value.length;
      console.log(
        `Cambiando a imagen ${currentImageIndex.value + 1}: ${
          gymImages.value[currentImageIndex.value]
        }`
      );
    };

    // Función para inicializar el slider
    const initializeSlider = () => {
      // Precargar todas las imágenes
      gymImages.value.forEach((src) => {
        const img = new Image();
        img.src = src;
      });

      // Cambiar imagen cada 2 minutos (120000 ms)
      imageInterval = setInterval(changeImage, 120000);

      console.log("Slider inicializado. Cambio cada 30 minutos.");
    };

    // Función para testing (cambio cada 10 segundos)
    const initializeSliderTest = () => {
      gymImages.value.forEach((src) => {
        const img = new Image();
        img.src = src;
      });

      // Para testing: cambiar cada 10 segundos
      imageInterval = setInterval(changeImage, 10000);
      console.log("Slider en modo test. Cambio cada 10 segundos.");
    };

    const clearErrors = () => {
      errors.email = "";
      errors.password = "";
    };

    const handleLogin = async () => {
      clearErrors();
      loading.value = true;

      try {
        const response = await api.login(form);

        // Guardar token y usuario
        localStorage.setItem("token", response.token);
        localStorage.setItem("user", JSON.stringify(response.user));

        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Has iniciado sesión correctamente",
          life: 3000,
        });

        router.push("/");
      } catch (error) {
        console.error("Login error:", error);

        if (error.response?.status === 422) {
          const validationErrors = error.response.data.errors;
          if (validationErrors) {
            Object.keys(validationErrors).forEach((key) => {
              errors[key] = validationErrors[key][0];
            });
          }
        } else {
          toast.add({
            severity: "error",
            summary: "Error",
            detail: error.response?.data?.message || "Error al iniciar sesión",
            life: 5000,
          });
        }
      } finally {
        loading.value = false;
      }
    };

    // Función para cambio manual (testing)
    const nextImage = () => {
      changeImage();
    };

    // Event listener para testing (presionar 'N' para siguiente imagen)
    const handleKeydown = (e) => {
      if (e.key.toLowerCase() === "n") {
        nextImage();
      }
    };

    onMounted(() => {
      // Usar initializeSlider() para producción (30 minutos)
      // Usar initializeSliderTest() para testing (10 segundos)
      initializeSlider(); // Cambiar por initializeSliderTest() para probar

      // Agregar event listener para testing
      document.addEventListener("keydown", handleKeydown);
    });

    onUnmounted(() => {
      if (imageInterval) {
        clearInterval(imageInterval);
      }
      document.removeEventListener("keydown", handleKeydown);
    });

    return {
      form,
      errors,
      loading,
      handleLogin,
      gymImages,
      currentImageIndex,
      nextImage,
    };
  },
};
</script>

<style scoped>
/* Fondo con imágenes rotativas */
.background-slider {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -2;
}

.background-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  opacity: 0;
  transition: opacity 2s ease-in-out;
}

.background-image.active {
  opacity: 1;
}

/* Overlay con blur y oscurecimiento */
.background-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  backdrop-filter: blur(1px);
  background: rgba(0, 0, 0, 0.4);
  z-index: -1;
}

/* Indicador de imagen actual */
.image-indicator {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  backdrop-filter: blur(10px);
  z-index: 2;
}

.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  position: relative;
  z-index: 1;
}

.login-card {
  width: 100%;
  max-width: 400px;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.login-card {
  background: rgba(255, 255, 255, 0.15);
  /* Blanco translúcido */
  backdrop-filter: blur(10px);
  /* Difuminado detrás */
  -webkit-backdrop-filter: blur(10px);
  /* Compatibilidad Safari */
  border: 1px solid rgba(255, 255, 255, 0.2);
  /* Borde sutil */
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  /* Sombra oscura */
  padding: 40px;
  width: 100%;
  max-width: 600px;
  text-align: center;
  position: relative;
  z-index: 1;
}

.login-header {
  text-align: center;
  padding: 2rem 0 1rem;
}

.login-header h2 {
  margin-top: 0.5rem;
  color: #1f2937;
  font-weight: 600;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

.form-group :deep(.p-inputtext),
.form-group :deep(.p-password) {
  width: 100%;
  background: rgba(248, 249, 250, 0.9) !important;
  backdrop-filter: blur(5px);
  border: 2px solid rgba(225, 229, 233, 0.8) !important;
  transition: all 0.3s ease;
}

.form-group :deep(.p-inputtext:focus),
.form-group :deep(.p-password:focus-within) {
  background: rgba(255, 255, 255, 0.95) !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.form-group :deep(.p-password .p-inputtext) {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

.login-button {
  width: 100%;
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  margin-top: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border: none !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  transition: all 0.3s ease;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4) !important;
}

.login-button:active {
  transform: translateY(0);
}

.login-footer {
  text-align: center;
  padding: 1rem;
  background: rgba(248, 250, 252, 0.9);
  backdrop-filter: blur(5px);
  border-top: 1px solid rgba(229, 231, 235, 0.8);
  color: #6b7280;
}

.p-error {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Estilos específicos para PrimeVue */
:deep(.p-card) {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

:deep(.p-card-header) {
  background: transparent !important;
  border-bottom: 1px solid rgba(229, 231, 235, 0.8);
}

:deep(.p-card-content) {
  background: transparent !important;
}

@media (max-width: 768px) {
  .login-container {
    padding: 1rem 0.5rem;
  }

  .login-card {
    max-width: 95%;
    margin: 1rem;
  }

  .login-header {
    padding: 1.5rem 0 1rem;
  }

  .login-header h2 {
    font-size: 1.5rem;
  }

  .image-indicator {
    bottom: 10px;
    right: 10px;
    font-size: 0.7rem;
    padding: 6px 10px;
  }
}
</style>
