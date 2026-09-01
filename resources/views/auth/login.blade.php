<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – Infortech CRM</title>
    
    <!-- Fuentes: Inter para UI limpia, Playfair Display para el toque híbrido/elegante del título -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #e56b0c; --primary-hover: #cf5e08;
            --text-main: #ffffff; --text-muted: #cbd5e1;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            /* Fondo corporativo profundo con foco de luz en la esquina superior izquierda */
            background: radial-gradient(circle at 0% 0%, #1a2a50 0%, #0b1120 70%, #070b15 100%);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            padding: 20px;
            color: var(--text-main);
        }

        .login-wrapper {
            width: 100%;
            max-width: 520px; /* Ampliado para hacer el form más grande */
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
        }

        .login-logo {
            margin-bottom: 30px;
            text-align: center;
        }
        .login-logo img {
            width: 100%;
            max-width: 500px; /* Logo gigante para compensar si la imagen tiene relleno transparente */
            height: auto;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
        }

        /* Glassmorphism Card */
        .form-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-top: 1px solid rgba(255, 255, 255, 0.25);
            border-left: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            padding: 50px 45px 40px; /* Padding incrementado */
        }

        /* Tipografía Híbrida / Elegante */
        .form-title { 
            font-family: 'Playfair Display', serif;
            font-size: 32px; /* Título más grande */
            font-weight: 600; 
            color: #ffffff; 
            margin-bottom: 8px; 
            text-align: center; 
            letter-spacing: 0.5px;
        }
        .form-subtitle { 
            font-size: 15px; /* Subtítulo más grande */
            color: var(--text-muted); 
            margin-bottom: 35px; 
            text-align: center; 
            font-weight: 300;
        }

        .form-group-custom { margin-bottom: 24px; }
        .form-label-custom { display: block; font-size: 11.5px; font-weight: 600; color: #e2e8f0; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; pointer-events: none; transition: color 0.3s; }
        
        /* Inputs Nítidos y Limpios */
        .form-input { 
            width: 100%; 
            background: rgba(255, 255, 255, 0.95); 
            border: 1px solid rgba(255,255,255,0.8); 
            border-radius: 12px; 
            color: #0f172a; 
            font-size: 16px; /* Texto de input más grande */
            font-family: 'Inter', sans-serif; 
            padding: 16px 18px 16px 48px; /* Inputs más altos y espaciosos */
            transition: all 0.3s ease; 
            outline: none; 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .form-input:focus { 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(229,107,12,0.15), inset 0 2px 4px rgba(0,0,0,0.02); 
            background: #ffffff; 
        }
        .form-input:focus + .input-icon, .form-input:focus ~ .input-icon { color: var(--primary); }
        .form-input::placeholder { color: #94a3b8; font-weight: 400; }

        .toggle-password { 
            position: absolute; right: 16px; top: 50%; transform: translateY(-50%); 
            background: none; border: none; color: #94a3b8; 
            cursor: pointer; font-size: 17px; transition: color 0.2s; padding: 0; 
        }
        .toggle-password:hover { color: #0f172a; }

        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .remember-wrap { display: flex; align-items: center; gap: 10px; }
        .custom-check { 
            width: 16px; height: 16px; 
            accent-color: var(--primary); 
            cursor: pointer; 
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .remember-label { font-size: 13px; color: #e2e8f0; cursor: pointer; font-weight: 300; }

        /* Botón Focal Naranja con Soft Drop Shadow */
        .btn-login { 
            width: 100%; 
            background: var(--primary); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; 
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            font-size: 17px; /* Botón más grande */
            font-weight: 600; 
            letter-spacing: 0.5px;
            padding: 16px; /* Botón más alto */
            cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            box-shadow: 0 8px 25px rgba(229,107,12,0.4); 
            display: flex; align-items: center; justify-content: center; gap: 10px; 
        }
        .btn-login:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 12px 30px rgba(229,107,12,0.5); 
            background: var(--primary-hover); 
        }
        .btn-login:active { transform: scale(0.98); }
        .btn-login i { font-size: 16px; transition: transform 0.3s; }
        .btn-login:hover i { transform: translateX(4px); }

        .alert-error { 
            background: rgba(220, 38, 38, 0.15); 
            backdrop-filter: blur(8px);
            border: 1px solid rgba(220, 38, 38, 0.3); 
            border-radius: 12px; 
            color: #fca5a5; 
            padding: 14px 16px; 
            font-size: 13px; 
            margin-bottom: 24px; 
            display: flex; align-items: center; gap: 10px; 
        }

        .footer-note { margin-top: 25px; text-align: center; font-size: 12.5px; color: rgba(255,255,255,0.5); font-weight: 300; }
        .footer-note a { color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .footer-note a:hover { color: #fff; }

        @media (max-width: 480px) {
            .form-card { padding: 35px 24px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-logo">
            <img src="{{ asset('assets/images/logo-blanco.png') }}" alt="Infortech Logo">
        </div>

        <div class="form-card">
            <div class="form-title">Bienvenido al CRM</div>
            <div class="form-subtitle">Ingresa tus credenciales para continuar</div>

            @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group-custom">
                    <label class="form-label-custom" for="email">Correo Electrónico</label>
                    <div class="input-wrap">
                        <input type="email" id="email" name="email" class="form-input"
                               value="{{ old('email') }}" placeholder="admin@infortech.com"
                               required autofocus autocomplete="email">
                        <i class="bi bi-envelope input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group-custom">
                    <label class="form-label-custom" for="password">Contraseña</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="••••••••" required autocomplete="current-password">
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePwd()">
                            <i class="bi bi-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <div class="remember-wrap">
                        <input type="checkbox" name="remember" id="remember" class="custom-check">
                        <label for="remember" class="remember-label">Recordarme</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Ingresar al Sistema <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} Infortech CRM. Todos los derechos reservados.<br>
            <a href="#">Soporte Técnico</a>
        </div>
    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    </script>
</body>
</html>
