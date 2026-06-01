<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Forzamos una sesión de invitado para mostrar el dashboard sin login
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 999;
    $_SESSION['usuario_nombre'] = 'Invitado (Modo Vista)';
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <h1>✅ Bienvenido, <?= $nombre ?>!</h1>
            <p>Has iniciado sesión correctamente.</p>

            <div class="info-box">
                <h2>🐳 Información del despliegue</h2>
                <ul>
                    <li><strong>Contenedor:</strong> Docker + PHP 8.2 + Apache</li>
                    <li><strong>Base de datos:</strong> PostgreSQL en Supabase</li>
                    <li><strong>Plataforma:</strong> Render.com</li>
                    <li><strong>Despliegue:</strong> Zero-Downtime Deploy</li>
                </ul>
            </div>

            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>