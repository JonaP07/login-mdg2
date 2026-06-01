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

require_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Lógica para guardar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_cliente'])) {
    $nombre_cli   = trim($_POST['nombre_cli'] ?? '');
    $email_cli    = trim($_POST['email_cli'] ?? '');
    $telefono_cli = trim($_POST['telefono_cli'] ?? '');

    if (empty($nombre_cli) || empty($email_cli)) {
        $mensaje = "Nombre y Email son obligatorios.";
        $tipo_mensaje = "error";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO clientes (nombre, email, telefono) VALUES (?, ?, ?)");
            $stmt->execute([$nombre_cli, $email_cli, $telefono_cli]);
            $mensaje = "Cliente registrado con éxito.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) { // Error de duplicado en Postgres
                $mensaje = "El email ya está registrado.";
            } else {
                $mensaje = "Error al guardar: " . $e->getMessage();
            }
            $tipo_mensaje = "error";
        }
    }
}

// Obtener lista de clientes
$clientes = [];
try {
    $stmt = $db->query("SELECT * FROM clientes ORDER BY creado_en DESC LIMIT 10");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    // Si la tabla no existe aún, la creamos (útil para el primer despliegue)
    if ($e->getCode() == '42P01') {
        $db->exec("CREATE TABLE IF NOT EXISTS clientes (
            id SERIAL PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            telefono VARCHAR(20),
            creado_en TIMESTAMP DEFAULT NOW()
        )");
    }
}

$nombre_usuario = htmlspecialchars($_SESSION['usuario_nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .client-form { margin-top: 20px; text-align: left; }
        .client-list { margin-top: 30px; width: 100%; border-collapse: collapse; color: white; }
        .client-list th, .client-list td { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: left; }
        .alert.success { background: #2ecc71; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .alert.error { background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <h1>👥 Gestión de Clientes</h1>
            <p>Hola, <?= $nombre_usuario ?>. Registra a tus clientes aquí.</p>

            <?php if ($mensaje): ?>
                <div class="alert <?= $tipo_mensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" class="client-form">
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre_cli" required placeholder="Ej. Juan Pérez">
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email_cli" required placeholder="juan@ejemplo.com">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono_cli" placeholder="987654321">
                </div>
                <button type="submit" name="registrar_cliente" class="btn-login">Guardar Cliente</button>
            </form>

            <div class="info-box" style="margin-top: 30px;">
                <h2>Lista de Clientes Recientes</h2>
                <?php if (empty($clientes)): ?>
                    <p style="font-size: 0.9em; opacity: 0.7;">No hay clientes registrados aún.</p>
                <?php else: ?>
                    <table class="client-list">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <a href="logout.php" class="btn-logout" style="display:inline-block; margin-top:20px; color:#aaa; text-decoration:none;">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
