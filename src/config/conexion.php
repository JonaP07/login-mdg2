<?php

function cargarVariablesLocales($rutaEnv) {
    $variables = [];

    if (!is_file($rutaEnv) || !is_readable($rutaEnv)) {
        return $variables;
    }

    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0 || strpos($linea, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $linea, 2);
        $variables[trim($key)] = trim($value);
    }

    return $variables;
}

function obtenerVariableEntorno($clave, $variablesLocales = [], $default = null) {
    $valor = getenv($clave);
    if ($valor !== false && $valor !== '') {
        return $valor;
    }

    if (isset($_ENV[$clave]) && $_ENV[$clave] !== '') {
        return $_ENV[$clave];
    }

    if (isset($_SERVER[$clave]) && $_SERVER[$clave] !== '') {
        return $_SERVER[$clave];
    }

    if (isset($variablesLocales[$clave]) && $variablesLocales[$clave] !== '') {
        return $variablesLocales[$clave];
    }

    return $default;
}

function crearConexionDesdeUrl($databaseUrl) {
    $partes = parse_url($databaseUrl);
    if ($partes === false || empty($partes['host']) || empty($partes['path'])) {
        throw new RuntimeException('La variable DATABASE_URL no tiene un formato valido.');
    }

    $host = $partes['host'];
    $port = $partes['port'] ?? '5432';
    $dbname = ltrim($partes['path'], '/');
    $user = urldecode($partes['user'] ?? '');
    $pass = urldecode($partes['pass'] ?? '');

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    if (!empty($partes['query'])) {
        parse_str($partes['query'], $queryParams);
        if (!empty($queryParams['sslmode'])) {
            $dsn .= ';sslmode=' . $queryParams['sslmode'];
        }
    }

    return new PDO($dsn, $user, $pass);
}

try {
    $variablesLocales = cargarVariablesLocales(__DIR__ . '/../../.env');
    $databaseUrl = obtenerVariableEntorno('DATABASE_URL', $variablesLocales);

    if ($databaseUrl) {
        $db = crearConexionDesdeUrl($databaseUrl);
    } else {
        $host = obtenerVariableEntorno('DB_HOST', $variablesLocales);
        $port = obtenerVariableEntorno('DB_PORT', $variablesLocales, '5432');
        $dbname = obtenerVariableEntorno('DB_NAME', $variablesLocales);
        $user = obtenerVariableEntorno('DB_USER', $variablesLocales);
        $pass = obtenerVariableEntorno('DB_PASSWORD', $variablesLocales);

        if (!$host || !$dbname || !$user) {
            throw new RuntimeException('Faltan variables de entorno para la conexion a PostgreSQL.');
        }

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $db = new PDO($dsn, $user, $pass);
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('Lo sentimos, hay un problema de conexion con la base de datos: ' . $e->getMessage());
}
