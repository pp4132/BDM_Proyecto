
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$controlador = new AuthController($conexion);

// Rutas de redireccionamiento
$panel = BASE_URL . '/app/view/panel.php';
$inicio = BASE_URL . '/app/view/InicioSesion.html';

// Si ya inició sesión
if (isset($_SESSION['usuario_id'])) {
    header("Location: $panel");
    exit();
}

// Recibir datos
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validar campos
if ($email === '' || $password === '') {
    header("Location: $inicio?error=1");
    exit();
}

try {

    $resultado = $controlador->login($email, $password);

    if ($resultado) {
        header("Location: $panel");
        exit();
    }

} catch (Throwable $e) {

    error_log("Error en login: " . $e->getMessage());

}

header("Location: $inicio?error=1");
exit();

?>
