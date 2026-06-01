<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_cliente'])) {
    $nombre_cli   = trim($_POST['nombre_cli'] ?? '');
    $email_cli    = trim($_POST['email_cli'] ?? '');

    if (empty($nombre_cli) || empty($email_cli)) {
        $mensaje = "Nombre y Email son obligatorios.";
        $tipo_mensaje = "error";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO clientes (nombre, email) VALUES (?, ?)");
            $stmt->execute([$nombre_cli, $email_cli]);
            $mensaje = "¡Registro exitoso!";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "El email ya existe.";
            $tipo_mensaje = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Wear - Simple & Classic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>MEN'S WEAR</h1>
        <p>Estilo clásico para el hombre moderno</p>
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
        <h2 style="margin-bottom: 20px; text-align: center;">Suscríbete para ofertas</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="nombre_cli" required placeholder="Nombre">
            </div>
            <div class="form-group">
                <input type="email" name="email_cli" required placeholder="Correo Electrónico">
            </div>
            <button type="submit" name="registrar_cliente" class="btn">REGISTRARME</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2024 Men's Wear. Diseño Simple.</p>
</footer>

</body>
</html>
