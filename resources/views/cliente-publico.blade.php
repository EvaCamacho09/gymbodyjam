<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Información - {{ $datos['cliente']['nombre'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .status-active {
            background: #4CAF50;
            color: white;
        }

        .status-expired {
            background: #f44336;
            color: white;
        }

        .status-warning {
            background: #ff9800;
            color: white;
        }

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


        .cliente-container {
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

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            color: white;
        }

        .subtitle {
            color: white;
            font-size: 1rem;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(225, 229, 233, 0.8);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(248, 249, 250, 0.9);
            backdrop-filter: blur(5px);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .error {
            background: rgba(248, 215, 218, 0.9);
            border: 1px solid rgba(245, 198, 203, 0.8);
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            backdrop-filter: blur(5px);
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .credentials-info {
            background: rgba(230, 243, 255, 0.9);
            border: 1px solid rgba(179, 217, 255, 0.8);
            color: #0066cc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: left;
            backdrop-filter: blur(5px);
        }

        .credentials-info h4 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        .credentials-info p {
            margin: 5px 0;
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

        .asistencia-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
        }

        .asistencia-fecha {
            font-weight: 600;

        }

        .asistencia-hora {

            font-size: 0.875rem;
        }

        .membresia-activa {
            background: #4CAF50;
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .membresia-vencida {
            background: #d32f2f;
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .membresia-por-vencer {
            background: #f57c00;
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .dias-restantes {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .cliente-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .logo {
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
</head>

<body>
    <!-- Slider de fondo -->
    <div class="background-slider">
        <div class="background-image active" data-image="1"></div>
        <div class="background-image" data-image="2"></div>
        <div class="background-image" data-image="3"></div>
        <div class="background-image" data-image="4"></div>
        <div class="background-image" data-image="5"></div>
        <div class="background-image" data-image="6"></div>
    </div>

    <!-- Overlay con blur -->
    <div class="background-overlay"></div>

    <div class="cliente-container">
        <div class="logo">GYM MANAGER</div>
        <div class="subtitle">Información Cliente</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label"><strong>Nombre:</strong></span>
                <span class="info-value">{{ $datos['cliente']['nombre'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><strong>Cédula:</strong></span>
                <span class="info-value">{{ $datos['cliente']['cedula'] }}</span>
            </div>

            <div class="info-row">
                <span class="info-label"><strong>Estado:</strong></span>
                <span class="status-badge">{{ ucfirst($datos['cliente']['estado']) }}</span>
            </div>
        </div>

        <div class="card-body">
            @if ($datos['membresia_activa'])
                @php
                    $diasRestantes = $datos['membresia_activa']['dias_restantes'];
                    $estaVencida = $datos['membresia_activa']['esta_vencida'];

                    $claseMembresia = 'membresia-activa';
                    $iconoMembresia = 'fas fa-check-circle';
                    $textoEstado = 'Membresía Activa';

                    if ($estaVencida) {
                        $claseMembresia = 'membresia-vencida';
                        $iconoMembresia = 'fas fa-times-circle';
                        $textoEstado = 'Membresía Vencida';
                    } elseif ($diasRestantes <= 7) {
                        $claseMembresia = 'membresia-por-vencer';
                        $iconoMembresia = 'fas fa-exclamation-triangle';
                        $textoEstado = 'Membresía Por Vencer';
                    }
                @endphp

                <div class="{{ $claseMembresia }}">
                    <div class="row">
                        <h4><i class="{{ $iconoMembresia }}"></i> {{ $textoEstado }}</h4>
                        <h5>{{ $datos['membresia_activa']['nombre'] }}</h5>
                        <div class="dias-restantes">
                            @if ($estaVencida)
                                Vencida hace {{ abs($diasRestantes) }} días
                            @else
                                {{ $diasRestantes }} días restantes
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <span class="info-label"><strong>Fecha de Inicio:</strong></span>
                    <span
                        class="info-value">{{ \Carbon\Carbon::parse($datos['membresia_activa']['fecha_inicio'])->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><strong>Fecha de Vencimiento:</strong></span>
                    <span
                        class="info-value">{{ \Carbon\Carbon::parse($datos['membresia_activa']['fecha_vencimiento'])->format('d/m/Y') }}</span>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No tienes una membresía activa en este momento.
                </div>
            @endif
        </div>

        <div class="info-row">
           <span class="info-label"><strong>RECOMENDACIONES SEGÚN TUS OBJETIVOS:</strong></span>
           <span> {{$datos['recomendaciones']}}</span>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // URLs de las imágenes de Pexels
        const gymImages = [
            'https://images.pexels.com/photos/1229356/pexels-photo-1229356.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
            'https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
            'https://images.pexels.com/photos/4944972/pexels-photo-4944972.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
            'https://images.pexels.com/photos/416747/pexels-photo-416747.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
            'https://images.pexels.com/photos/3112004/pexels-photo-3112004.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
            'https://images.pexels.com/photos/1954524/pexels-photo-1954524.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop'
        ];

        let currentImageIndex = 0;
        const backgroundImages = document.querySelectorAll('.background-image');
        const imageIndicator = document.getElementById('current-image');

        // Función para precargar imágenes
        function preloadImages() {
            gymImages.forEach((src, index) => {
                const img = new Image();
                img.onload = () => {
                    backgroundImages[index].style.backgroundImage = `url(${src})`;
                };
                img.src = src;
            });
        }

        // Función para cambiar imagen
        function changeImage() {
            // Ocultar imagen actual
            backgroundImages[currentImageIndex].classList.remove('active');

            // Cambiar al siguiente índice
            currentImageIndex = (currentImageIndex + 1) % gymImages.length;

            // Mostrar nueva imagen
            backgroundImages[currentImageIndex].classList.add('active');

            // Actualizar indicador
            imageIndicator.textContent = `Imagen ${currentImageIndex + 1}/${gymImages.length}`;

            console.log(`Cambiando a imagen ${currentImageIndex + 1}: ${gymImages[currentImageIndex]}`);
        }

        // Función para inicializar el slider
        function initializeSlider() {
            // Precargar todas las imágenes
            preloadImages();

            // Configurar la primera imagen
            backgroundImages[0].style.backgroundImage = `url(${gymImages[0]})`;
            imageIndicator.textContent = `Imagen 1/${gymImages.length}`;

            // Cambiar imagen cada 2 minutos (120000 ms)
            setInterval(changeImage, 120000);

            console.log('Slider inicializado. Cambio cada 30 minutos.');
        }

        // Función para testing (cambio cada 10 segundos) - comentar en producción
        function initializeSliderTest() {
            preloadImages();
            backgroundImages[0].style.backgroundImage = `url(${gymImages[0]})`;
            imageIndicator.textContent = `Imagen 1/${gymImages.length}`;

            // Para testing: cambiar cada 10 segundos
            setInterval(changeImage, 10000);
            console.log('Slider en modo test. Cambio cada 10 segundos.');
        }

        // Inicializar cuando la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            // Usar initializeSlider() para producción (30 minutos)
            // Usar initializeSliderTest() para testing (10 segundos)
            initializeSlider(); // Cambiar por initializeSliderTest() para probar
        });

        // Función para cambio manual (opcional, para testing)
        function nextImage() {
            changeImage();
        }

        // Agregar evento de teclado para testing (presionar 'N' para siguiente imagen)
        document.addEventListener('keydown', function(e) {
            if (e.key.toLowerCase() === 'n') {
                nextImage();
            }
        });
    </script>
</body>

</html>
