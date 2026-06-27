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
        .dashboard-page {
            background: #f5f7fb;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #121212, #263445);
            color: #fff;
            padding: 56px 0 42px;
            margin-bottom: 36px;
            box-shadow: 0 14px 32px rgba(18, 18, 18, 0.16);
        }
        .dashboard-header h1 {
            margin-bottom: 10px;
            font-size: 2.2rem;
            letter-spacing: 0.08em;
        }
        .dashboard-subtitle {
            color: rgba(255, 255, 255, 0.8);
        }
        .dashboard-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 22px;
            flex-wrap: wrap;
        }
        .dashboard-link {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 999px;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .dashboard-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        .dashboard-link.secondary {
            background: transparent;
        }
        .vip-title {
            margin-bottom: 20px;
            text-align: center;
        }
        .client-list-box {
            margin-top: 30px;
            border-top: 1px solid #e6ebf2;
            padding-top: 20px;
            text-align: left;
        }
        .client-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            margin-top: 10px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        .client-table th, .client-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
        }
        .client-table th {
            color: #6b7280;
            font-weight: 600;
            background: #f8fafc;
        }
    </style>
</head>
<body class="dashboard-page">

<header class="dashboard-header">
    <div class="container">
        <h1>MEN'S WEAR</h1>
        <p class="dashboard-subtitle">Nueva Colección 2026 - Bienvenido <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>!</p>
        <div class="dashboard-actions">
            <a href="pedido.php" class="dashboard-link">Gestion de Pedidos</a>
            <a href="logout.php" class="dashboard-link secondary">Cerrar Sesion</a>
        </div>
    </div>
</header>

<div class="container">
    <div class="products-grid">
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80" alt="Camisa Oxford">
            <h3>Camisa Oxford</h3>
            <p class="price">$35.00</p>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=900&q=80" alt="Chaqueta Casual">
            <h3>Chaqueta Casual</h3>
            <p class="price">$75.00</p>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=900&q=80" alt="Jeans Slim Fit">
            <h3>Jeans Slim Fit</h3>
            <p class="price">$45.00</p>
        </div>
    </div>

    <div class="registration-area">
        <h2 class="vip-title">Unete al Club VIP 2026</h2>
        
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
            <h3 style="font-size: 1rem; margin-bottom: 10px;">Clientes Registrados</h3>
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
