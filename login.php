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
                    $error = "Contraseña incorrecta.<br>";
                    $error .= "Recibido: $longitud_ingresada caracteres.<br>";
                    
                    if ($longitud_ingresada == 11) {
                        $error .= "<strong style='color:yellow'>⚠️ ¡Atención! Estás enviando 11 caracteres. Parece que tu navegador está poniendo tu clave de Supabase (mdg3login77) en lugar de 1234.</strong><br>";
                    }
                    
                    $error .= "Sugerencia: Limpia el cuadro de texto y escribe 1234.";
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
                    <label for="password_real">Contraseña</label>
                    <input
                        type="password"
                        id="password_real"
                        name="password_real" 
                        placeholder="••••••••"
                        autocomplete="new-password"
                        required
                    >
                    <p style="font-size: 0.8em; color: #666; margin-top: 5px;">Escribe <strong>1234</strong> manualmente.</p>
                </div>

                <button type="submit" class="btn-login">Entrar</button>
            </form>

            <p class="hint">Usuario de prueba: <strong>admin@test.com</strong> / <strong>1234</strong></p>
        </div>
    </div>
</body>
</html>