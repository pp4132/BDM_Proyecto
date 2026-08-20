<?php

include ("conexion.php");
session_start();

// Datos del formulario
$idUsuario = null;
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$alias = trim($_POST['alias'] ?? '');
$genero = trim($_POST['genero'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$password = trim($_POST['password'] ?? '');
$errores = [];
//Validaciones campos requisitos
// Nombre y apellidos (solo letras y espacios)
if (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ' -]+$/", $nombre)) {
    $errores[] = "El nombre solo debe contener letras";
}

if (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/", $apellidos)) {
    $errores[] = "Los apellidos solo deben contener letras";
}

// Fecha de nacimiento (mayor de edad, por ejemplo 18)
if (empty($fecha_nacimiento)) {
    $errores[] = "La fecha de nacimiento es obligatoria";
} else {
    try {
        $fechaNac = new DateTime($fecha_nacimiento);
        $fechaActual = new DateTime();
        $edad = $fechaActual->diff($fechaNac)->y;

        if ($edad < 18) {
            $errores[] = "Debes ser mayor de 18 años";
        }
    } catch (Exception $e) {
        $errores[] = "Fecha de nacimiento inválida";
    }
}


// Email válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Correo electrónico no válido";
}

// Validaciones contraseña
if (strlen($password) < 8) {
    $errores[] = "Debe tener al menos 8 caracteres";
}
if (!preg_match('/[a-z]/', $password)) {
    $errores[] = "Debe incluir al menos una letra minúscula";
}
if (!preg_match('/[A-Z]/', $password)) {
    $errores[] = "Debe incluir al menos una letra mayúscula";
}
if (!preg_match('/\d/', $password)) {
    $errores[] = "Debe incluir al menos un número";
}
if (!preg_match('/[\W_]/', $password)) {
    $errores[] = "Debe incluir al menos un carácter especial";
}


// Encriptación de la contraseña
$contrasena = password_hash($password, PASSWORD_DEFAULT);

// Rol fijo (Asegurado)
$idRol = 3; 

// Foto como binario
$foto = null;
$opcion=1;

if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name'] != '') {

    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        $errores[] = "La imagen es demasiado grande (máx 2MB)";
    }

    $tipo = mime_content_type($_FILES['foto']['tmp_name']);
    if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
        $errores[] = "Formato de imagen no válido";
    }

    $foto = file_get_contents($_FILES['foto']['tmp_name']);
}

// Muestra los errores
if (!empty($errores)) {
    $mensaje = urlencode(implode("|", $errores));
    $datos = urlencode(json_encode($_POST));

    header("Location: registro.html?error=$mensaje&data=$datos");
    exit;
}

// Llamada al SP
$stmt = $conexion->prepare("CALL sp_usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


if (!$stmt) {
    die("Error execute: " . $stmt->error);
}

$stmt->bind_param(
    "iiissssbssss",
    $opcion,
    $idUsuario,
    $idRol,
    $nombre,
    $apellidos,
    $alias,
    $fecha_nacimiento,
    $foto,
    $genero,
    $email,
    $contrasena,
    $telefono
);

// Solo si hay imagen
if ($foto !== null) {
    $stmt->send_long_data(7, $foto);
}



if ($stmt->execute()) {

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['email'] = $email;

    header("Location: PanelUsuario.php");
    exit;
}else {
    $error = urlencode("Ocurrió un error al registrar el usuario");
    header("Location: registro.html?error=" . $error);
}

$stmt->close();
$conexion->close();
exit;
?>