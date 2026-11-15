<?php

session_start();

// Evitar acceso sin iniciar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/InicioSesion.html");
    exit();
}

// Conexión
require_once __DIR__ . '/../../config/conexion.php';

// Modelo
require_once __DIR__ . '/../../model/Vehiculo.php';

// Verificar conexión
if (!isset($conexion) || !($conexion instanceof mysqli)) {
    die("Error: conexión a la base de datos no disponible.");
}

$idUsuario = (int) $_SESSION['usuario_id'];

// Solo asegurados pueden gestionar sus vehículos
if (($_SESSION['Rol'] ?? '') !== 'Asegurado') {
    die("No tienes permisos para realizar esta operación.");
}/**/

// Instanciar modelo
$vehiculoModel = new Vehiculo($conexion);

// Obtener acción
$accion = $_POST['accion'] ?? '';

try {

    // Recibir datos del formulario
    $datos = [
        'tipo' => trim($_POST['tipo'] ?? ''),
        'marca' => trim($_POST['marca'] ?? ''),
        'modelo' => trim($_POST['modelo'] ?? ''),
        'anio' => (int) ($_POST['anio'] ?? 0),
        'placas' => trim($_POST['placas'] ?? ''),
        'numeroPoliza' => trim($_POST['numeroPoliza'] ?? ''),
        'numeroSerie' => trim($_POST['numeroSerie'] ?? '')
    ];

    // Validaciones generales
    if (
        $datos['tipo'] === '' ||
        $datos['marca'] === '' ||
        $datos['modelo'] === '' ||
        $datos['placas'] === '' ||
        $datos['anio'] < 1950 ||
        $datos['anio'] > 2099
    ) {
        throw new Exception("Verifica los datos del vehículo.");
    }

    // Registrar
    if ($accion === 'registrar') {

        $resultado = $vehiculoModel->registrar($datos, $idUsuario);

        if (!$resultado) {
            throw new Exception("No fue posible registrar el vehículo.");
        }

        $_SESSION['vehiculo_mensaje'] =
            "Vehículo registrado correctamente.";
    }

    // Actualizar
    elseif ($accion === 'actualizar') {

        $datos['idVehiculo'] =
            (int) ($_POST['idVehiculo'] ?? 0);

        if ($datos['idVehiculo'] <= 0) {
            throw new Exception("Identificador de vehículo inválido.");
        }

        $resultado = $vehiculoModel->actualizar(
            $datos,
            $idUsuario
        );

        if ($resultado['pertenece'] === 0) {

            throw new Exception(
                "El vehículo no existe o no pertenece a tu cuenta."
            );

        }

        $_SESSION['vehiculo_mensaje'] =
            "Vehículo actualizado correctamente.";

    }

    else {
        throw new Exception("Acción no válida.");
    }

} catch (Exception $e) {

    $_SESSION['vehiculo_error'] = $e->getMessage();

}

// Regresar al panel
header("Location: ../view/panel.php");
exit();

?>
