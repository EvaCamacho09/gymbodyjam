<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gym Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 30px;
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
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .credentials-info {
            background: #e6f3ff;
            border: 1px solid #b3d9ff;
            color: #0066cc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: left;
        }

        .credentials-info h4 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        .credentials-info p {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">GYM MANAGER</div>
        <div class="subtitle">Acceso al Monitor de Asistencias</div>

       

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('web.login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder=""
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <button type="submit" class="btn">
                Iniciar Sesión
            </button>
        </form>

        <div class="back-link">
            <a href="/">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
