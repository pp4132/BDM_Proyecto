<?php
session_start();
include("conexion.php");

if (isset($_SESSION['usuario_id'])) {
    header("Location: PanelUsuario.php");
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: InicioSesion.html?error=1");
    exit();
}

$sql = "
    SELECT
        IdUsuario,
        Nombre,
        Email,
        Contrasena,
        Rol,
        Activo
    FROM vw_usuario_login
    WHERE Email = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $usuario = $result->fetch_assoc();

    // validar si está activo
    if ((int)$usuario['Activo'] !== 1) {
        loginError();
    }

    if (password_verify($password, $usuario['Contrasena'])) {

        $_SESSION['usuario_id'] = $usuario['IdUsuario'];
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['Rol'] = $usuario['Rol'];

        session_regenerate_id(true);

        header("Location: PanelUsuario.php");
        exit();

    } else {
        loginError();
    }

} else {
    loginError();
}

function loginError() {
    header("Location: InicioSesion.html?error=1");
    exit();
}

$stmt->close();
$conexion->close();
?>