<?php
session_start();

// Proteger esta página — si no está logueado, regresar al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
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