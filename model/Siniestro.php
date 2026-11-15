<?php

class Siniestro
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarPorRol($rol, $idUsuario)
    {
        if ($rol === 'Supervisor') {

            $sql = "
                SELECT
                    IdSiniestro,
                    Folio,
                    Estado,
                    FechaReporte
                FROM vw_siniestros_acceso
            ";

            $stmt = $this->conexion->prepare($sql);

        } elseif ($rol === 'Ajustador') {

            $sql = "
                SELECT
                    IdSiniestro,
                    Folio,
                    Estado,
                    FechaReporte
                FROM vw_siniestros_acceso
                WHERE IdAjustador = ?
            ";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);

        } elseif ($rol === 'Asegurado') {

            $sql = "
                SELECT
                    s.IdSiniestro,
                    s.Folio,
                    s.Estado,
                    s.FechaReporte
                FROM vw_siniestros_acceso s
                INNER JOIN vehiculos v
                    ON s.IdVehiculo = v.IdVehiculo
                WHERE v.IdAsegurado = ?
            ";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);

        } else {

            throw new Exception("Rol de usuario no válido.");

        }

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de siniestros.");
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar los siniestros.");
        }

        $resultado = $stmt->get_result();
        $siniestros = $resultado->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $siniestros;
    }

    public function validarVehiculo($idVehiculo)
{
    $sql = "
        SELECT IdVehiculo
        FROM vehiculos
        WHERE IdVehiculo = ?
    ";

    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar la validación del vehículo.");
    }

    $stmt->bind_param("i", $idVehiculo);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $valido = $resultado->num_rows > 0;

    $stmt->close();

    return $valido;
}


public function crear($datos)
{
    $sql = "CALL sp_siniestros(?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar el registro del siniestro.");
    }

    $stmt->bind_param(
        "iiiiisssssdd",
        $datos['opcion'],
        $datos['idSiniestro'],
        $datos['idVehiculo'],
        $datos['idAjustador'],
        $datos['idnoseusa'],
        $datos['tipo'],
        $datos['estado'],
        $datos['descripcion'],
        $datos['fecha'],
        $datos['ubicacion'],
        $datos['montoEstimado'],
        $datos['montoAprobado']
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        throw new Exception($error);
    }

    $resultado = $stmt->get_result();

    if (!$resultado) {
        $stmt->close();
        throw new Exception("El procedimiento no devolvió el identificador.");
    }

    $fila = $resultado->fetch_assoc();

    $resultado->free();

    while ($stmt->more_results()) {
        $stmt->next_result();

        $pendiente = $stmt->get_result();

        if ($pendiente) {
            $pendiente->free();
        }
    }

    $stmt->close();

    if (!$fila || !isset($fila['id'])) {
        throw new Exception("No se pudo recuperar el ID del siniestro.");
    }

    return (int) $fila['id'];
}



public function obtenerDetalle($idSiniestro, $rol, $idUsuario)
{
    if (!in_array($rol, ['Supervisor', 'Ajustador', 'Asegurado'], true)) {
        throw new Exception("Rol de usuario no válido.");
    }

    $sql = "
        SELECT
            IdSiniestro,
            IdVehiculo,
            IdAjustador,
            IdAsegurado,
            Folio,
            Estado,
            Descripcion,
            FechaReporte,
            FechaAsignacion,
            FechaCierre,
            Ubicacion,
            MontoEstimado,
            MontoAprobado,
            Tipo,
            Marca,
            Modelo,
            Placas,
            NumeroPoliza,
            NombreAsegurado
        FROM vw_siniestros_detalle
        WHERE IdSiniestro = ?
    ";

    if ($rol === 'Ajustador') {
        $sql .= " AND IdAjustador = ?";
    } elseif ($rol === 'Asegurado') {
        $sql .= " AND IdAsegurado = ?";
    }

    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar detalle del siniestro.");
    }

    if ($rol === 'Supervisor') {
        $stmt->bind_param("i", $idSiniestro);
    } else {
        $stmt->bind_param("ii", $idSiniestro, $idUsuario);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception("Error al consultar el siniestro.");
    }

    $resultado = $stmt->get_result();
    $siniestro = $resultado->fetch_assoc();

    $stmt->close();

    return $siniestro;
}



public function contarComentarios($idSiniestro)
{
    $sql = "
        SELECT fn_total_comentarios_siniestro(?) AS TotalComentarios
    ";

    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar el conteo de comentarios.");
    }

    $stmt->bind_param("i", $idSiniestro);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    $stmt->close();

    return (int) ($fila['TotalComentarios'] ?? 0);
}


public function actualizar($datos, $rol)
{
    if (!in_array($rol, ['Supervisor', 'Ajustador'], true)) {
        throw new Exception("Rol no autorizado.");
    }

    $opcion = ($rol === 'Supervisor') ? 3 : 2;

    $idSiniestro = (int)$datos['idSiniestro'];
    $idVehiculo = 0;
    $idAjustador = 0;
    $idUsuario = 0;

    $tipo = $datos['tipo'] ?? '';
    $estado = $datos['estado'] ?? '';
    $descripcion = $datos['descripcion'] ?? '';
    $fecha = null;
    $ubicacion = $datos['ubicacion'] ?? '';

    $montoEstimado = (float)($datos['montoEstimado'] ?? 0);
    $montoAprobado = (float)($datos['montoAprobado'] ?? 0);

    $sql = "CALL sp_siniestros(?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar actualización del siniestro.");
    }

    $stmt->bind_param(
        "iiiiisssssdd",
        $opcion,
        $idSiniestro,
        $idVehiculo,
        $idAjustador,
        $idUsuario,
        $tipo,
        $estado,
        $descripcion,
        $fecha,
        $ubicacion,
        $montoEstimado,
        $montoAprobado
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        throw new Exception("Error al actualizar siniestro: " . $error);
    }

    $resultado = $stmt->get_result();

    if ($resultado) {
        $resultado->free();
    }

    while ($stmt->more_results()) {
        $stmt->next_result();

        $pendiente = $stmt->get_result();

        if ($pendiente) {
            $pendiente->free();
        }
    }

    $stmt->close();

    return true;
}

    /**
     * Busca siniestros, reparaciones e historiales.
     */
    public function buscarGlobal(string $query): array
    {
        $sql = "
            SELECT
                TipoRegistro,
                Id,
                Ref,
                Estado,
                Tipo,
                Ubicacion,
                Fecha
            FROM vw_busqueda_global
            WHERE
                Ref LIKE CONCAT('%', ?, '%')
                OR Estado LIKE CONCAT('%', ?, '%')
                OR Tipo LIKE CONCAT('%', ?, '%')
                OR Ubicacion LIKE CONCAT('%', ?, '%')
            ORDER BY Fecha DESC
            LIMIT 100
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar búsqueda: " . $this->conexion->error);
            return [];
        }

        $stmt->bind_param(
            "ssss",
            $query,
            $query,
            $query,
            $query
        );

        if (!$stmt->execute()) {
            error_log("Error al ejecutar búsqueda: " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();

        $registros = [];

        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = $fila;
        }

        $stmt->close();

        return $registros;
    }

}