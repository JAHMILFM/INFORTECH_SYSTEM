<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Infortech</title>
    <!-- Bootstrap 5 CDN para independizar el login de los assets que fallan -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1e1e2f 0%, #151521 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
        }
        
        /* Efecto Glassmorphism */
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        /* Luz sutil superior */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-logo h2 {
            font-weight: 800;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            font-size: 2.2rem;
        }

        .form-control {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: rgba(0,0,0,0.4);
            border-color: #4facfe;
            box-shadow: 0 0 0 0.25rem rgba(79, 172, 254, 0.25);
            color: #fff;
        }
        .form-label {
            font-size: 0.85rem;
            color: #a0a0b0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .btn-login {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 172, 254, 0.3);
            color: #fff;
        }

        .input-group-text {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
            border-right: none;
            color: #a0a0b0;
        }
        .form-control-icon {
            border-left: none;
        }
        
        .form-check-label {
            color: #a0a0b0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">
            <h2>INFORTECH</h2>
            <p class="text-muted mt-2" style="font-size: 0.9rem;">Acceso al Panel de Control</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.2); border-color: rgba(220, 53, 69, 0.5); color: #ff8e99; font-size: 0.9rem; border-radius: 10px;">
                <i class="fa fa-exclamation-circle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-icon" value="{{ old('email') }}" placeholder="admin@infortech.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" id="dz-password" class="form-control form-control-icon" placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: rgba(255,255,255,0.1); color: #a0a0b0;">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label" for="rememberMe">
                    Mantener sesión iniciada
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa fa-sign-in-alt me-2"></i> Ingresar
            </button>
        </form>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('dz-password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
