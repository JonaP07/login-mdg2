<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    // Cambiamos el nombre del campo para engañar al autocompletado del navegador
    $password = trim($_POST['password_real'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos.';
    } else {

        require_once 'conexion.php';

        try {
            // Buscamos al usuario por email
            $stmt = $db->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Limpiamos la contraseña ingresada por si hay espacios invisibles
                $password_ingresada = trim($password);
                
                if (password_verify($password_ingresada, $usuario['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    // DIAGNÓSTICO DETALLADO
                    $longitud_ingresada = strlen($password_ingresada);
                    $longitud_db = strlen($usuario['password_hash']);
                    $error = "Contraseña incorrecta.<br>";
                    $error .= "Enviado: $longitud_ingresada caracteres.<br>";
                    $error .= "En DB: $longitud_db caracteres.<br>";
                    $error .= "Sugerencia: Escribe '1234' manualmente, sin copiar/pegar.";
                }
            } else {
                $error = 'El correo electrónico no existe en nuestro sistema.';
            }

        } catch (PDOException $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>🔐 Iniciar Sesión</h1>
            <p class="subtitle">Sistema de Publicación Web con Docker</p>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="admin@test.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_fake">Contraseña</label>
                    <input
                        type="password"
                        id="password_fake"
                        name="password_real"
                        placeholder="••••••••"
                        autocomplete="off"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">Entrar</button>
            </form>

            <p class="hint">Usuario de prueba: <strong>admin@test.com</strong> / <strong>1234</strong></p>
        </div>
    </div>
</body>
</html>