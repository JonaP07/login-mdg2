<?php
// Configuración de la base de datos
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Supabase a veces usa postgres://, lo normalizamos a postgresql:// para parse_url
    $database_url = str_replace('postgres://', 'postgresql://', $database_url);
    
    // Si existe DATABASE_URL (Formato de Render/Supabase)
    $db_config = parse_url($database_url);
    
    $host     = $db_config['host'];
    $port     = $db_config['port'] ?? '5432';
    $user     = $db_config['user'];
    $password = $db_config['pass'] ?? '';
    $dbname   = ltrim($db_config['path'], '/');
} else {
    // Configuración manual o por variables individuales (Local/Docker)
    $host     = getenv('DB_HOST') ?: 'localhost';
    $port     = getenv('DB_PORT') ?: '5432';
    $dbname   = getenv('DB_NAME') ?: 'login_db';
    $user     = getenv('DB_USER') ?: 'postgres';
    $password = getenv('DB_PASSWORD') ?: '';
}

try {
    // DSN para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $db = new PDO($dsn, $user, $password);
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Para depuración en Render: mostramos el error real (luego lo quitaremos por seguridad)
    error_log('Error de conexión: ' . $e->getMessage());
    die('Error de conexión con la base de datos: ' . $e->getMessage());
}