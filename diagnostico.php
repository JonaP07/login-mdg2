<?php
// SCRIPT DE DIAGNÓSTICO DE BASE DE DATOS
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexion.php';

echo "<h1>🛠️ Diagnóstico de Sistema</h1>";

// 1. Verificar Extensiones
echo "<h2>1. Extensiones PHP</h2>";
$extensions = ['pdo', 'pdo_pgsql', 'pgsql'];
foreach ($extensions as $ext) {
    echo "Extensión <strong>$ext</strong>: " . (extension_loaded($ext) ? "✅ Instalada" : "❌ NO INSTALADA") . "<br>";
}

// 2. Verificar Conexión
echo "<h2>2. Estado de Conexión</h2>";
if ($db) {
    echo "Conexión a base de datos: ✅ Exitosa<br>";
    try {
        $version = $db->query('SELECT version()')->fetchColumn();
        echo "Versión de DB: <code>$version</code><br>";
    } catch (Exception $e) {
        echo "Error al obtener versión: " . $e->getMessage() . "<br>";
    }
}

// 3. Verificar Usuario admin@test.com
echo "<h2>3. Usuario de Prueba</h2>";
try {
    $stmt = $db->prepare("SELECT id, email, password_hash FROM usuarios WHERE email = 'admin@test.com'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        echo "Usuario <code>admin@test.com</code>: ✅ ENCONTRADO<br>";
        $hash = $user['password_hash'];
        echo "Longitud del Hash en DB: <strong>" . strlen($hash) . "</strong><br>";
        
        // Test de verificación local
        $pass_test = "1234";
        $verify = password_verify($pass_test, $hash);
        echo "Prueba con '1234': " . ($verify ? "✅ COINCIDE" : "❌ NO COINCIDE") . "<br>";
        
        if (!$verify) {
            echo "<p style='color:red'>⚠️ El hash en la base de datos no corresponde a '1234'.</p>";
            echo "<p>Vamos a generar uno nuevo ahora mismo...</p>";
            $new_hash = password_hash('1234', PASSWORD_BCRYPT);
            $update = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE email = 'admin@test.com'");
            $update->execute([$new_hash]);
            echo "✅ Hash actualizado. Por favor intenta el login ahora.";
        }
    } else {
        echo "Usuario <code>admin@test.com</code>: ❌ NO ENCONTRADO<br>";
        echo "Creando usuario de prueba...<br>";
        $new_hash = password_hash('1234', PASSWORD_BCRYPT);
        $insert = $db->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES ('Admin', 'admin@test.com', ?)");
        $insert->execute([$new_hash]);
        echo "✅ Usuario creado con éxito.";
    }
} catch (Exception $e) {
    echo "Error al verificar usuario: " . $e->getMessage() . "<br>";
}

echo "<br><br><a href='login.php'>Ir al Login</a>";
