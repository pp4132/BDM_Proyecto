<?php

class Vehiculo
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }



   /*
     * Ejecutar procedimiento almacenado sp_vehiculos
     */
    private function ejecutarSP($opcion, $idVehiculo, $idUsuario, $datos = [])
    {
        $sql = "CALL sp_vehiculos(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar sp_vehiculos: " . $this->conexion->error
            );
        }

        $tipo = $datos['tipo'] ?? '';
        $marca = $datos['marca'] ?? '';
        $modelo = $datos['modelo'] ?? '';
        $anio = (int) ($datos['anio'] ?? 0);
        $placas = $datos['placas'] ?? '';
        $numeroPoliza = $datos['numeroPoliza'] ?? '';
        $numeroSerie = $datos['numeroSerie'] ?? '';

        $stmt->bind_param(
            "iiisssisss",
            $opcion,
            $idVehiculo,
            $idUsuario,
            $tipo,
            $marca,
            $modelo,
            $anio,
            $placas,
            $numeroPoliza,
            $numeroSerie
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception("Error en sp_vehiculos: " . $error);
        }

        // Recuperar resultado del procedimiento
        $filas = [];

        do {

            if ($resultado = $stmt->get_result()) {

                $filas = $resultado->fetch_all(MYSQLI_ASSOC);

                $resultado->free();
            }

        } while ($stmt->more_results() && $stmt->next_result());

        $stmt->close();

        return $filas;
    }

    /*
     * 1. REGISTRAR VEHÍCULO
     */
    public function registrar($datos, $idUsuario)
    {
        $resultado = $this->ejecutarSP(
            1,
            0,
            $idUsuario,
            $datos
        );

        return $resultado[0]['id'] ?? null;
    }

    /*
     * 2. ACTUALIZAR VEHÍCULO
     */
    public function actualizar($datos, $idUsuario)
    {
        $resultado = $this->ejecutarSP(
            2,
            $datos['idVehiculo'],
            $idUsuario,
            $datos
        );

        if (!isset($resultado[0])) {
            throw new Exception("No se recibió respuesta del procedimiento.");
        }

        return [
            'filas' => (int) $resultado[0]['filas'],
            'pertenece' => (int) $resultado[0]['pertenece']
        ];
    }

    /*
     * 4. CONSULTAR VEHÍCULO POR ID
     */
    public function obtenerPorId($idVehiculo, $idUsuario)
    {
        $resultado = $this->ejecutarSP(
            4,
            $idVehiculo,
            $idUsuario
        );

        return $resultado[0] ?? null;
    }

    /*
     * 5. LISTAR VEHÍCULOS DEL ASEGURADO
     */
    public function listarPorAsegurado($idUsuario)
    {
        return $this->ejecutarSP(
            5,
            0,
            $idUsuario
        );
    }

    // LISTAR VEHÍCULOS DISPONIBLES PARA CREAR SINIESTROS
    public function listarDisponiblesParaSiniestro()
    {
        $sql = "
            SELECT
                IdVehiculo,
                Marca,
                Modelo,
                Placas
            FROM vw_vehiculos_panel
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de vehículos.");
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar los vehículos.");
        }

        $resultado = $stmt->get_result();

        $vehiculos = $resultado->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $vehiculos;
    }

}