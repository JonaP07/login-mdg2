<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Cuando el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos.';
    } else {
        
        // Conectarse a la base de datos usando la variable de entorno
        $db_url = getenv('DATABASE_URL');
        
        try {
            $db = new PDO($db_url);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Buscar el usuario por email
            $stmt = $db->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si existe y si la contraseña es correcta
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Guardar datos en sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Email o contraseña incorrectos.';
            }
            
        } catch (PDOException $e) {
            $error = 'Error de conexión con la base de datos.';
            // En producción nunca mostrar: $e->getMessage()
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
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
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