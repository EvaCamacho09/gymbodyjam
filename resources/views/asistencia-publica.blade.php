<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM MANAGER - Registro de Asistencia</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/primeicons@6.0.1/primeicons.css">
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
            user-select: none;
            position: relative;
            overflow-x: hidden;
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

        .container {
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
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 20px auto;
            display: block;
            border: 4px solid #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }

        .subtitle {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 40px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 20px;
            border: 2px solid rgba(225, 229, 233, 0.8);
            border-radius: 10px;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            background: rgba(248, 249, 250, 0.9);
            backdrop-filter: blur(5px);
            text-align: center;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.4rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            min-height: 60px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .clientes-list {
            margin: 20px 0;
            text-align: left;
        }

        .cliente-item {
            background: rgba(248, 249, 250, 0.9);
            border: 2px solid rgba(225, 229, 233, 0.8);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .cliente-item:hover,
        .cliente-item:active {
            border-color: #667eea;
            background: rgba(240, 242, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .cliente-nombre {
            font-weight: 600;
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .cliente-cedula {
            color: #666;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .cliente-membresia {
            margin-top: 8px;
            font-size: 1rem;
            font-weight: 500;
        }

        .estado-activa {
            color: #28a745;
        }

        .estado-por-vencer {
            color: #ffc107;
        }

        .estado-vencida {
            color: #dc3545;
        }

        .loading {
            display: none;
            margin: 20px 0;
        }

        .spinner {
            border: 3px solid rgba(243, 243, 243, 0.3);
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .alert-success {
            background: rgba(212, 237, 218, 0.9);
            border: 1px solid rgba(195, 230, 203, 0.8);
            color: #155724;
        }

        .alert-error {
            background: rgba(248, 215, 218, 0.9);
            border: 1px solid rgba(245, 198, 203, 0.8);
            color: #721c24;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin: 10% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            animation: slideIn 0.3s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .modal-text {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .modal-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .dias-info {
            background: rgba(248, 249, 250, 0.9);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
            backdrop-filter: blur(5px);
        }

        /* Estilos para autocompletado */
        .autocomplete-suggestions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(225, 229, 233, 0.8);
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 300px;
            overflow-y: auto;
            position: absolute;
            width: calc(100% - 4px);
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .autocomplete-suggestion {
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid rgba(241, 243, 245, 0.8);
            transition: background-color 0.2s ease;
        }

        .autocomplete-suggestion:hover,
        .autocomplete-suggestion.active {
            background: rgba(240, 242, 255, 0.9);
        }

        .autocomplete-suggestion:last-child {
            border-bottom: none;
        }

        /* Indicador de inactividad */
        .timeout-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #666;
            display: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
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

        /* Header con info de usuario */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(225, 229, 233, 0.8);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
                max-width: 95%;
            }

            .logo {
                font-size: 2rem;
            }

            .logo-image {
                width: 100px;
                height: 100px;
                margin: 15px auto;
            }

            .modal-content {
                margin: 20% auto;
                padding: 25px 20px;
            }

            input[type="text"] {
                font-size: 1.2rem;
                padding: 18px;
            }

            .btn {
                font-size: 1.2rem;
                min-height: 55px;
            }

            .cliente-nombre {
                font-size: 1.2rem;
            }

            .image-indicator {
                bottom: 10px;
                right: 10px;
                font-size: 0.7rem;
                padding: 6px 10px;
            }

            .timeout-indicator {
                top: 10px;
                right: 10px;
                font-size: 0.8rem;
                padding: 8px 12px;
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

    <!-- Indicador de imagen -->
    <div class="image-indicator">
        <span id="current-image">Imagen 1/6</span>
    </div>

    <!-- Indicador de timeout -->
    <div id="timeoutIndicator" class="timeout-indicator">
        Auto-reinicio en <span id="timeoutCounter">30</span>s
    </div>

    <div class="container">
        <div class="logo">GYM MANAGER</div>
        <img src="{{ asset('images/logo.jpg') }}" alt="Gym Manager Logo" class="logo-image" />
        <div class="subtitle">Registro de Asistencia</div>

        <form id="busquedaForm">
            <div class="form-group" style="position: relative;">
                <input type="text" id="busqueda" name="busqueda"
                    placeholder="Ingresa tu nombre o número de cédula..." autocomplete="off" required>
                <div id="autocompleteContainer" class="autocomplete-suggestions"></div>
            </div>
            <button type="submit" class="btn" id="buscarBtn">
                Buscar Cliente
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">Buscando...</p>
        </div>

        <div id="clientesList" class="clientes-list"></div>
        <div id="alertContainer"></div>
    </div>

    <!-- Modal de Éxito -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">✅</div>
            <div class="modal-title">¡Asistencia Registrada!</div>
            <div class="modal-text" id="modalText"></div>
            <div class="dias-info" id="diasInfo"></div>
            <button class="modal-btn" onclick="closeModal()">Entendido</button>
        </div>
    </div>

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

        // Elementos del DOM
        const busquedaForm = document.getElementById('busquedaForm');
        const busquedaInput = document.getElementById('busqueda');
        const buscarBtn = document.getElementById('buscarBtn');
        const loading = document.getElementById('loading');
        const clientesList = document.getElementById('clientesList');
        const alertContainer = document.getElementById('alertContainer');
        const successModal = document.getElementById('successModal');
        const modalText = document.getElementById('modalText');
        const diasInfo = document.getElementById('diasInfo');
        const autocompleteContainer = document.getElementById('autocompleteContainer');
        const timeoutIndicator = document.getElementById('timeoutIndicator');
        const timeoutCounter = document.getElementById('timeoutCounter');

        // Variables de control
        let currentClientes = [];
        let selectedIndex = -1;
        let searchTimeout;
        let inactivityTimeout;
        let countdownInterval;
        let timeoutSeconds = 30;

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

        // Event listeners
        busquedaForm.addEventListener('submit', buscarClientes);
        busquedaInput.addEventListener('input', handleInput);
        busquedaInput.addEventListener('keydown', handleKeydown);
        busquedaInput.addEventListener('focus', resetInactivityTimer);
        document.addEventListener('click', hideAutocomplete);
        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('touchstart', resetInactivityTimer);

        function handleInput(e) {
            const value = e.target.value.trim();
            resetInactivityTimer();

            clearTimeout(searchTimeout);
            if (value.length >= 2) {
                searchTimeout = setTimeout(() => {
                    buscarClientesAutocompletado(value);
                }, 300);
            } else {
                hideAutocomplete();
                limpiarResultados();
            }
        }

        function handleKeydown(e) {
            const suggestions = autocompleteContainer.children;
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
                    updateSelection();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection();
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && suggestions[selectedIndex]) {
                        const clienteId = suggestions[selectedIndex].dataset.clienteId;
                        const clienteNombre = suggestions[selectedIndex].dataset.clienteNombre;
                        seleccionarCliente(parseInt(clienteId), clienteNombre);
                    } else {
                        buscarClientes(e);
                    }
                    break;
                case 'Escape':
                    hideAutocomplete();
                    break;
                case 'n':
                case 'N':
                    // Para testing: cambiar imagen manualmente
                    changeImage();
                    break;
            }
        }

        function updateSelection() {
            const suggestions = autocompleteContainer.children;
            Array.from(suggestions).forEach((item, index) => {
                item.classList.toggle('active', index === selectedIndex);
            });
        }

        function hideAutocomplete() {
            autocompleteContainer.style.display = 'none';
            selectedIndex = -1;
        }

        async function buscarClientesAutocompletado(busqueda) {
            try {
                const response = await fetch('/api/asistencia/buscar-cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        busqueda
                    })
                });

                const data = await response.json();
                if (data.success && data.clientes.length > 0) {
                    mostrarAutocompletado(data.clientes);
                } else {
                    hideAutocomplete();
                }
            } catch (error) {
                console.error('Error en autocompletado:', error);
                hideAutocomplete();
            }
        }

        function mostrarAutocompletado(clientes) {
            currentClientes = clientes;
            autocompleteContainer.innerHTML = clientes.map(cliente => {
                const membresia = cliente.membresia_actual;
                let estadoText = '';
                let estadoClass = '';

                if (membresia) {
                  
                    if (membresia.vencida) {
                        estadoText = 'Membresía vencida';
                        estadoClass = 'estado-vencida';
                    } else if (membresia.dias_restantes <= 5) {
                        estadoText = `${membresia.dias_restantes} días restantes`;
                        estadoClass = 'estado-por-vencer';
                    } else {
                        estadoText = `${membresia.dias_restantes} días restantes`;
                        estadoClass = 'estado-activa';
                    }
                } else {
                    estadoText = 'Sin membresía';
                    estadoClass = 'estado-vencida';
                }

                return `
                    <div class="autocomplete-suggestion"
                          data-cliente-id="${cliente.id}"
                          data-cliente-nombre="${cliente.nombre}"
                         onclick="seleccionarCliente(${cliente.id}, '${cliente.nombre}')">
                        <div class="cliente-nombre">${cliente.nombre}</div>
                        <div class="cliente-cedula">Cédula: ${cliente.cedula}</div>
                        <div class="cliente-membresia ${estadoClass}">${estadoText}</div>
                    </div>
                `;
            }).join('');

            autocompleteContainer.style.display = 'block';
            selectedIndex = -1;
        }

        function seleccionarCliente(clienteId, nombreCliente) {
            hideAutocomplete();
            busquedaInput.value = nombreCliente;
            registrarAsistencia(clienteId, nombreCliente);
        }

        async function buscarClientes(e) {
            e.preventDefault();
            const busqueda = busquedaInput.value.trim();

            if (!busqueda || busqueda.length < 2) {
                mostrarAlerta('Por favor ingresa al menos 2 caracteres', 'error');
                return;
            }

            hideAutocomplete();
            mostrarLoading(true);
            limpiarResultados();

            try {
                const response = await fetch('/api/asistencia/buscar-cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        busqueda
                    })
                });

                const data = await response.json();
                if (data.success) {
                    mostrarClientes(data.clientes);
                } else {
                    mostrarAlerta('Error al buscar clientes', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function mostrarClientes(clientes) {
            if (clientes.length === 0) {
                mostrarAlerta('No se encontraron clientes con ese nombre o cédula', 'error');
                return;
            }

            if (clientes.length === 1) {
                const cliente = clientes[0];
                seleccionarCliente(cliente.id, cliente.nombre);
                return;
            }

            clientesList.innerHTML = clientes.map(cliente => {
                const membresia = cliente.membresia_actual;
                let membresiaText = '';
                let estadoClass = '';

                if (membresia) {
                    if (membresia.vencida) {
                        membresiaText = `Membresía vencida (${membresia.fecha_vencimiento})`;
                        estadoClass = 'estado-vencida';
                    } else if (membresia.dias_restantes <= 5) {
                        membresiaText =
                        `${membresia.nombre} - ${Math.abs(membresia.dias_restantes)} días restantes`;
                        estadoClass = 'estado-por-vencer';
                    } else {
                        membresiaText = `${membresia.nombre} - ${membresia.dias_restantes} días restantes`;
                        estadoClass = 'estado-activa';
                    }
                } else {
                    membresiaText = 'Sin membresía activa';
                    estadoClass = 'estado-vencida';
                }

                return `
                    <div class="cliente-item" onclick="registrarAsistencia(${cliente.id}, '${cliente.nombre}')">
                        <div class="cliente-nombre">${cliente.nombre}</div>
                        <div class="cliente-cedula">Cédula: ${cliente.cedula}</div>
                        <div class="cliente-membresia ${estadoClass}">${membresiaText}</div>
                    </div>
                `;
            }).join('');
        }

        async function registrarAsistencia(clienteId, nombreCliente) {
            mostrarLoading(true);
            hideAutocomplete();

            try {
                const response = await fetch('/api/asistencia/registrar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        cliente_id: clienteId
                    })
                });

                const data = await response.json();
                if (data.success) {
                   
                    modalText.textContent =
                        `¡Hola ${data.cliente.nombre}! Tu asistencia ha sido registrada exitosamente.`;

                    let diasText = '';
                    let diasClass = '';

                    if (data.membresia.vencida) {
                        diasText =
                            `⚠️ Tu membresía "${data.membresia.nombre}" venció el ${data.membresia.fecha_vencimiento}. Por favor renueva tu membresía.`;
                        diasClass = 'estado-vencida';
                    } else if (data.membresia.dias_restantes <= 5) {
                        diasText =
                            `⏰ Tu membresía "${data.membresia.nombre}" vence en ${data.membresia.dias_restantes} día(s). ¡Considera renovarla pronto!`;
                        diasClass = 'estado-por-vencer';
                    } else {
                        diasText =
                            `✅ Tu membresía "${data.membresia.nombre}" tiene ${data.membresia.dias_restantes} días restantes.`;
                        diasClass = 'estado-activa';
                    }

                    diasInfo.innerHTML = `<div class="${diasClass}">${diasText}</div>`;
                    successModal.style.display = 'block';

                    setTimeout(() => {
                        closeModal();
                        reiniciarFormulario();
                    }, 5000);
                } else {
                    mostrarAlerta(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error al registrar asistencia', 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Funciones de timeout e inactividad
        function resetInactivityTimer() {
            clearTimeout(inactivityTimeout);
            clearInterval(countdownInterval);
            timeoutIndicator.style.display = 'none';
            timeoutSeconds = 30;

            inactivityTimeout = setTimeout(() => {
                startCountdown();
            }, 120000); // 2 minutos
        }

        function startCountdown() {
            timeoutSeconds = 30;
            timeoutIndicator.style.display = 'block';
            countdownInterval = setInterval(() => {
                timeoutSeconds--;
                timeoutCounter.textContent = timeoutSeconds;
                if (timeoutSeconds <= 0) {
                    reiniciarFormulario();
                }
            }, 1000);
        }

        function reiniciarFormulario() {
            clearTimeout(inactivityTimeout);
            clearInterval(countdownInterval);
            timeoutIndicator.style.display = 'none';

            busquedaInput.value = '';
            limpiarResultados();
            hideAutocomplete();
            mostrarLoading(false);

            setTimeout(() => {
                busquedaInput.focus();
            }, 100);

            resetInactivityTimer();
        }

        function mostrarLoading(show) {
            loading.style.display = show ? 'block' : 'none';
            buscarBtn.disabled = show;
            if (show) {
                hideAutocomplete();
            }
        }

        function limpiarResultados() {
            clientesList.innerHTML = '';
            alertContainer.innerHTML = '';
        }

        function mostrarAlerta(mensaje, tipo) {
            alertContainer.innerHTML = `
                <div class="alert alert-${tipo === 'error' ? 'error' : 'success'}">
                    ${mensaje}
                </div>
            `;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        function closeModal() {
            successModal.style.display = 'none';
        }

        // Event listeners para el modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeModal();
            }
        });

        // Inicialización
        window.addEventListener('load', function() {
            initializeSlider();
            busquedaInput.focus();
            resetInactivityTimer();
        });

        // Prevenir zoom en dispositivos móviles al hacer doble tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>

</html>
