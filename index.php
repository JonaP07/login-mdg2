<?php
session_start();

// Si ya está logueado, ir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Si no, ir al login
header('Location: login.php');
exit;