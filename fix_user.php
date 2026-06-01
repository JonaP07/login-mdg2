<?php
// Script temporal para arreglar el usuario de prueba
require_once 'conexion.php';

$email = 'admin@test.com';
$nombre = 'Administrador';
$password = '1234';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Borrar usuario si existe
    $stmt = $db->prepare('DELETE FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);

    // Insertar con el nuevo hash generado en este servidor
    $stmt = $db->prepare('INSERT INTO usuarios (nombre, email, password_hash) VALUES (:nombre, :email, :hash)');
    $stmt->execute([
        ':nombre' => $nombre,
        ':email'  => $email,
        ':hash'   => $hash
    ]);

    echo "<h1>✅ Usuario actualizado con éxito</h1>";
    echo "<p>Email: <strong>$email</strong></p>";
    echo "<p>Password: <strong>$password</strong></p>";
    echo "<p>Hash generado: <code>$hash</code></p>";
    echo "<br><a href='login.php'>Ir al Login</a>";

} catch (PDOException $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
