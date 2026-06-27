<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Establecer la ruta de las sesiones y crearla si no existe
$session_path = __DIR__ . '/../sessions';
if (!is_dir($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);
// Check if user is authenticated
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/config/conexion.php';

$mensaje = '';
$tipo_mensaje = '';

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
            $mensaje = "¡Registro exitoso! Gracias por unirte.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "El email ya existe.";
            $tipo_mensaje = "error";
        }
    }
}

// Obtener lista de clientes registrados
$clientes = [];
try {
    $stmt = $db->query("SELECT * FROM clientes ORDER BY creado_en DESC LIMIT 5");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silencioso
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Wear - Colección 2026</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .client-list-box { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; text-align: left; }
        .client-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; margin-top: 10px; }
        .client-table th, .client-table td { padding: 8px; border-bottom: 1px solid #f0f0f0; text-align: left; }
        .client-table th { color: #888; font-weight: normal; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>MEN'S WEAR</h1>
        <p>Nueva Colección 2026 - Bienvenido <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>!</p>
        <p style="margin-top: 10px;">
            <a href="buscar.php" style="color: #fff; text-decoration: underline; margin-right: 20px;">→ Búsqueda de Productos (TC-03)</a>
            <a href="pedido.php" style="color: #fff; text-decoration: underline; margin-right: 20px;">→ Gestión de Pedidos (TC-02)</a>
            <a href="logout.php" style="color: #fff; text-decoration: underline;">Cerrar Sesión</a>
        </p>
    </div>
</header>

<div class="container">
    <div class="products-grid">
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1594932224828-b4b05a83ee0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" alt="Camisa">
            <h3>Camisa Oxford</h3>
            <p class="price">$35.00</p>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" alt="Chaqueta">
            <h3>Chaqueta Casual</h3>
            <p class="price">$75.00</p>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" alt="Jeans">
            <h3>Jeans Slim Fit</h3>
            <p class="price">$45.00</p>
        </div>
    </div>

    <div class="registration-area">
        <h2 style="margin-bottom: 20px; text-align: center;">Únete al Club VIP 2026</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="nombre_cli" required placeholder="Nombre completo">
            </div>
            <div class="form-group">
                <input type="email" name="email_cli" required placeholder="Correo Electrónico">
            </div>
            <div class="form-group">
                <input type="text" name="telefono_cli" placeholder="Teléfono / WhatsApp">
            </div>
            <button type="submit" name="registrar_cliente" class="btn">REGISTRARME</button>
        </form>

        <?php if (!empty($clientes)): ?>
        <div class="client-list-box">
            <h3 style="font-size: 1rem; margin-bottom: 10px;">Clientes Registrados:</h3>
            <table class="client-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2026 Men's Wear. Todos los derechos reservados.</p>
</footer>

</body>
</html>
