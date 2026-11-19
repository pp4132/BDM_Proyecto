<?php
$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    die("No se encontró el archivo .env");
}

// Cargar variables
$variables = parse_ini_file($envFile);

if ($variables === false) {
    die("No se pudieron leer las variables del archivo .env");
}

foreach ($variables as $key => $value) {
    putenv("$key=$value");
}

$host = getenv('DB_HOST');
$port = (int) getenv('DB_PORT');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

if (!$host || !$user || !$database || !$port) {
    die("Faltan variables de configuración en el archivo .env");
}

$conexion = new mysqli(
    $host,
    $user,
    $password,
    $database,
    $port
);

if ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error);
    die("No se pudo conectar con la base de datos.");
}

$conexion->set_charset("utf8mb4");

?>
