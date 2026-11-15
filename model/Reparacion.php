
<?php

class Reparacion
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Registrar reparación
    public function registrar($datos)
    {
        $sql = "CALL sp_reparaciones(?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar registro de reparación.");
        }

        $opcion = 1;
        $idReparacion = null;

        $idSiniestro = (int)$datos['IdSiniestro'];
        $idUsuario = (int)$datos['IdUsuarioCreador'];

        $descripcion = $datos['DescripcionTrabajo'];
        $taller = $datos['Taller'] ?? null;
        $fechaInicio = $datos['FechaInicio'] ?? null;
        $fechaFin = $datos['FechaFin'] ?? null;
        $costo = $datos['CostoFinal'] ?? null;
        $estado = 'Pendiente';

        $stmt->bind_param(
            "iiiissssds",
            $opcion,
            $idReparacion,
            $idSiniestro,
            $idUsuario,
            $descripcion,
            $taller,
            $fechaInicio,
            $fechaFin,
            $costo,
            $estado
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception($error);
        }

        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;

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

        return $fila['id'] ?? false;
    }

    // Actualizar datos de reparación
    public function actualizar($idReparacion, $datos)
    {
        $sql = "CALL sp_reparaciones(?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar actualización.");
        }

        $opcion = 5;
        $idSiniestro = 0;
        $idUsuario = 0;

        $descripcion = $datos['DescripcionTrabajo'];
        $taller = $datos['Taller'];
        $fechaInicio = $datos['FechaInicio'] ?? null;
        $fechaFin = $datos['FechaFin'] ?? null;
        $costo = $datos['CostoFinal'] ?? null;
        $estado = null;

        $stmt->bind_param(
            "iiiissssds",
            $opcion,
            $idReparacion,
            $idSiniestro,
            $idUsuario,
            $descripcion,
            $taller,
            $fechaInicio,
            $fechaFin,
            $costo,
            $estado
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception($error);
        }

        $this->limpiarResultados($stmt);

        $stmt->close();

        return true;
    }

    // Actualizar estado
    public function actualizarEstado($idReparacion, $estado)
    {
        $sql = "CALL sp_reparaciones(?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar cambio de estado.");
        }

        $opcion = 2;
        $idSiniestro = 0;
        $idUsuario = 0;

        $descripcion = null;
        $taller = null;
        $fechaInicio = null;
        $fechaFin = null;
        $costo = null;

        $stmt->bind_param(
            "iiiissssds",
            $opcion,
            $idReparacion,
            $idSiniestro,
            $idUsuario,
            $descripcion,
            $taller,
            $fechaInicio,
            $fechaFin,
            $costo,
            $estado
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception($error);
        }

        $this->limpiarResultados($stmt);

        $stmt->close();

        return true;
    }

    // Listar por siniestro
    public function listarPorSiniestro($idSiniestro)
    {
        $sql = "
            SELECT
                IdReparacion,
                DescripcionTrabajo,
                Taller,
                FechaInicio,
                FechaFin,
                CostoFinal,
                Estado,
                FechaCreacion
            FROM vw_reparaciones_siniestro
            WHERE IdSiniestro = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar reparaciones.");
        }

        $stmt->bind_param("i", $idSiniestro);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $reparaciones = $resultado->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $reparaciones;
    }

    // Consultar una reparación
    public function obtenerPorId($idReparacion)
    {
        $sql = "CALL sp_reparaciones(?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta.");
        }

        $opcion = 4;
        $idSiniestro = 0;
        $idUsuario = 0;
        $descripcion = null;
        $taller = null;
        $fechaInicio = null;
        $fechaFin = null;
        $costo = null;
        $estado = null;

        $stmt->bind_param(
            "iiiissssds",
            $opcion,
            $idReparacion,
            $idSiniestro,
            $idUsuario,
            $descripcion,
            $taller,
            $fechaInicio,
            $fechaFin,
            $costo,
            $estado
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar reparación.");
        }

        $resultado = $stmt->get_result();
        $reparacion = $resultado
            ? $resultado->fetch_assoc()
            : null;

        if ($resultado) {
            $resultado->free();
        }

        $this->limpiarResultados($stmt);
        $stmt->close();

        return $reparacion;
    }

    // Historial de reparación
    public function obtenerHistorial($idReparacion)
    {
        $stmt = $this->conexion->prepare(
            "CALL sp_reparaciones_historial(?)"
        );

        if (!$stmt) {
            throw new Exception("Error al preparar historial.");
        }

        $stmt->bind_param("i", $idReparacion);

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar historial.");
        }

        $resultado = $stmt->get_result();
        $historial = $resultado->fetch_all(MYSQLI_ASSOC);

        $resultado->free();
        $this->limpiarResultados($stmt);
        $stmt->close();

        return $historial;
    }

    // Limpiar resultados de procedimientos almacenados
    private function limpiarResultados($stmt)
    {
        while ($stmt->more_results()) {
            $stmt->next_result();

            $pendiente = $stmt->get_result();

            if ($pendiente) {
                $pendiente->free();
            }
        }
    }
}
?>
