<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Información - {{ $datos['cliente']['nombre'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2196F3;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --info-color: #00BCD4;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            padding: 2rem 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #1976D2);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
            border: none;
        }
        
        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: var(--success-color);
            color: white;
        }
        
        .status-expired {
            background: var(--danger-color);
            color: white;
        }
        
        .status-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            color: #333;
        }
        
        .asistencia-hora {
            color: #666;
            font-size: 0.875rem;
        }
        
        .membresia-activa {
            background: linear-gradient(135deg, var(--success-color), #45a049);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .membresia-vencida {
            background: linear-gradient(135deg, var(--danger-color), #d32f2f);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .membresia-por-vencer {
            background: linear-gradient(135deg, var(--warning-color), #f57c00);
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
            .container {
                padding: 1rem;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Información Personal -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Mi Información Personal</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value">{{ $datos['cliente']['nombre'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Cédula:</span>
                            <span class="info-value">{{ $datos['cliente']['cedula'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Correo:</span>
                            <span class="info-value">{{ $datos['cliente']['correo'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value">{{ $datos['cliente']['telefono'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Estado:</span>
                            <span class="status-badge status-active">{{ ucfirst($datos['cliente']['estado']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Membresía Actual -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card"></i> Mi Membresía Actual</h3>
                    </div>
                    <div class="card-body">
                        @if($datos['membresia_activa'])
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><i class="{{ $iconoMembresia }}"></i> {{ $textoEstado }}</h4>
                                        <h5>{{ $datos['membresia_activa']['nombre'] }}</h5>
                                        <p class="mb-0">{{ $datos['membresia_activa']['descripcion'] }}</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="dias-restantes">
                                            @if($estaVencida)
                                                Vencida hace {{ abs($diasRestantes) }} días
                                            @else
                                                {{ $diasRestantes }} días restantes
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Fecha de Inicio:</span>
                                        <span class="info-value">{{ \Carbon\Carbon::parse($datos['membresia_activa']['fecha_inicio'])->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Fecha de Vencimiento:</span>
                                        <span class="info-value">{{ \Carbon\Carbon::parse($datos['membresia_activa']['fecha_vencimiento'])->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Precio Pagado:</span>
                                        <span class="info-value">${{ number_format($datos['membresia_activa']['precio_pagado'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Estado de Pago:</span>
                                        <span class="status-badge status-active">{{ ucfirst($datos['membresia_activa']['estado_pago']) }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No tienes una membresía activa en este momento.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-bar"></i> Mis Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $datos['estadisticas']['asistencias_este_mes'] }}</div>
                                    <div class="stat-label">Asistencias Este Mes</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $datos['estadisticas']['total_asistencias'] }}</div>
                                    <div class="stat-label">Total de Asistencias</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asistencias Recientes -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Mis Asistencias Recientes</h3>
                    </div>
                    <div class="card-body">
                        @if($datos['asistencias_recientes']->count() > 0)
                            @foreach($datos['asistencias_recientes'] as $asistencia)
                                <div class="asistencia-item">
                                    <div>
                                        <div class="asistencia-fecha">
                                            {{ \Carbon\Carbon::parse($asistencia->fecha_ingreso)->format('d/m/Y') }}
                                        </div>
                                        <div class="asistencia-hora">
                                            {{ \Carbon\Carbon::parse($asistencia->fecha_ingreso)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        @if($asistencia->clienteMembresia && $asistencia->clienteMembresia->membresia)
                                            <small class="text-muted">{{ $asistencia->clienteMembresia->membresia->nombre }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No tienes asistencias registradas aún.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historial de Membresías -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Historial de Membresías</h3>
                    </div>
                    <div class="card-body">
                        @if($datos['historial_membresias']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Membresía</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datos['historial_membresias'] as $membresia)
                                            <tr>
                                                <td>
                                                    <strong>{{ $membresia->nombre }}</strong><br>
                                                    <small class="text-muted">{{ $membresia->descripcion }}</small>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($membresia->pivot->fecha_inicio)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($membresia->pivot->fecha_vencimiento)->format('d/m/Y') }}</td>
                                                <td>${{ number_format($membresia->pivot->precio_pagado, 0, ',', '.') }}</td>
                                                <td>
                                                    @if(\Carbon\Carbon::parse($membresia->pivot->fecha_vencimiento)->isPast())
                                                        <span class="badge bg-danger">Vencida</span>
                                                    @else
                                                        <span class="badge bg-success">Activa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No tienes historial de membresías.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-white mt-4">
                    <p>
                        <i class="fas fa-dumbbell"></i> 
                        Sistema de Gestión de Gimnasio
                    </p>
                    <small>Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
