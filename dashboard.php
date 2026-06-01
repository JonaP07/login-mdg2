<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            $mensaje = "¡Gracias! Te hemos registrado con éxito.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
                $mensaje = "Este correo ya está registrado en nuestro club VIP.";
            } else {
                $mensaje = "Error al guardar: " . $e->getMessage();
            }
            $tipo_mensaje = "error";
        }
    }
}

// Obtener lista de clientes (Solo para demo)
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
    <title>Fashion Store - Nueva Colección</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="hero-content">
            <h1>Nueva Colección 2024</h1>
            <p>Descubre las tendencias que definen tu estilo.</p>
        </div>
    </header>

    <div class="container">
        <h2 class="section-title">Nuestros Favoritos</h2>
        
        <div class="products-grid">
            <!-- Producto 1 -->
            <div class="product-card">
                <div class="product-img" style="background-image: url('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80')"></div>
                <div class="product-info">
                    <h3>Vestido de Verano</h3>
                    <p>Ligero y elegante</p>
                    <p class="product-price">$45.00</p>
                </div>
            </div>

            <!-- Producto 2 -->
            <div class="product-card">
                <div class="product-img" style="background-image: url('https://images.unsplash.com/photo-1539109136881-3be0616acf4b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80')"></div>
                <div class="product-info">
                    <h3>Chaqueta de Cuero</h3>
                    <p>Estilo urbano premium</p>
                    <p class="product-price">$89.00</p>
                </div>
            </div>

            <!-- Producto 3 -->
            <div class="product-card">
                <div class="product-img" style="background-image: url('https://images.unsplash.com/photo-1554412930-c74f637c98ce?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80')"></div>
                <div class="product-info">
                    <h3>Camisa Casual</h3>
                    <p>100% Algodón orgánico</p>
                    <p class="product-price">$32.00</p>
                </div>
            </div>
        </div>
    </div>

    <section class="registration-section">
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; margin-bottom: 20px;">Únete a nuestro Club VIP</h2>
                <p style="text-align: center; margin-bottom: 30px; color: #666;">Registrate para recibir ofertas exclusivas y lanzamientos anticipados.</p>

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Tu Nombre</label>
                        <input type="text" name="nombre_cli" required placeholder="Ej. Ana García">
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email_cli" required placeholder="ana@ejemplo.com">
                    </div>
                    <div class="form-group">
                        <label>Teléfono (Opcional)</label>
                        <input type="text" name="telefono_cli" placeholder="+51 999 888 777">
                    </div>
                    <button type="submit" name="registrar_cliente" class="btn-primary">Suscribirme Ahora</button>
                </form>

                <?php if (!empty($clientes)): ?>
                <div style="margin-top: 40px;">
                    <h3 style="font-size: 1rem; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Últimos suscriptores:</h3>
                    <ul style="list-style: none; font-size: 0.85rem; color: #555;">
                        <?php foreach ($clientes as $c): ?>
                            <li style="margin-bottom: 5px;">✨ <?= htmlspecialchars($c['nombre']) ?> se unió recientemente.</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer style="text-align: center; padding: 40px; color: #888; font-size: 0.9rem;">
        <p>&copy; 2024 Fashion Store. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
