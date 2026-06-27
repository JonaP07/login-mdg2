<?php
// Establecer la ruta de las sesiones y crearla si no existe
$session_path = __DIR__ . '/../sessions';
if (!is_dir($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;