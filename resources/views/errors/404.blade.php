<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 No Encontrado - Infortech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e2f 0%, #151521 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            color: #fff;
            text-align: center;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            padding: 50px;
            max-width: 500px;
            position: relative;
        }
        .error-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        h1 {
            font-size: 6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0;
            line-height: 1;
        }
        .btn-home {
            background: linear-gradient(135deg, #e56b0c 0%, #1f3a6f 100%);
            border: none;
            color: #fff;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
            margin-top: 30px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(229, 107, 12, 0.3);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <h1>404</h1>
        <h3 class="mt-3">Página No Encontrada</h3>
        <p class="text-muted mt-2">La ruta que intentas buscar no existe en el sistema corporativo o fue movida.</p>
        <a href="{{ url('/') }}" class="btn-home"><i class="fa fa-arrow-left me-2"></i> Volver al Inicio</a>
    </div>
</body>
</html>
