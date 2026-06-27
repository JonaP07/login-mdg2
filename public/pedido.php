<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$session_path = __DIR__ . '/../sessions';
if (!is_dir($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/controllers/PedidoController.php';

$controller = new PedidoController();

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

$action = $_GET['action'] ?? 'index';

if ($action === 'index') {
    $controller->index();
} elseif ($action === 'agregar') {
    header('Content-Type: application/json');
    $controller->agregar();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Página no encontrada";
}
