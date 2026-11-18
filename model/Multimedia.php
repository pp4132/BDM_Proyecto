
<?php

class Multimedia
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Guardar archivo
    public function guardar(
        $idSiniestro,
        $idUsuario,
        $nombreArchivo,
        $mime,
        $tipoDB,
        $contenido,
        $idComentario = null
    ) {

        $opcion = 1;
        $idArchivo = null;

        $stmt = $this->conexion->prepare(
            "CALL sp_multimedia(?,?,?,?,?,?,?,?,?)"
        );

        if (!$stmt) {
            throw new Exception(
                "Error al preparar archivo multimedia: " .
                $this->conexion->error
            );
        }

        // IMPORTANTE:
        // El parámetro 8 corresponde al contenido BLOB.
        // Se enlaza como binario y se envía mediante send_long_data.

        $null = null;

        $stmt->bind_param(
            "iiiisssbi",
            $opcion,
            $idArchivo,
            $idSiniestro,
            $idUsuario,
            $tipoDB,
            $nombreArchivo,
            $mime,
            $null,
            $idComentario
        );

        $stmt->send_long_data(7, $contenido);

        if (!$stmt->execute()) {

            $error = $stmt->error;

            $stmt->close();

            throw new Exception(
                "Error al guardar multimedia: " . $error
            );
        }

        $resultado = $stmt->get_result();

        $fila = $resultado
            ? $resultado->fetch_assoc()
            : null;

        if ($resultado) {
            $resultado->free();
        }

        $this->limpiarResultados($stmt);

        $stmt->close();

        return $fila['id'] ?? false;
    }


    // Listar archivos por siniestro
    public function listarPorSiniestro($idSiniestro)
    {
        $sql = "
            SELECT
                IdArchivo,
                IdSiniestro,
                IdSubidoPor,
                Tipo,
                NombreArchivo,
                TipoMime,
                FechaSubida,
                IdComentario
            FROM vw_multimedia_siniestro
            WHERE IdSiniestro = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar multimedia.");
        }

        $stmt->bind_param("i", $idSiniestro);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $archivos = $resultado->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $archivos;
    }

    // Actualizar archivo
    public function actualizar(
        $idArchivo,
        $nombreArchivo,
        $mime,
        $contenido
    ) {

        $opcion = 2;
        $idSiniestro = 0;
        $idUsuario = 0;
        $tipo = null;
        $idComentario = null;
        $null = null;

        $stmt = $this->conexion->prepare(
            "CALL sp_multimedia(?,?,?,?,?,?,?,?,?)"
        );

        if (!$stmt) {
            throw new Exception("Error al preparar actualización.");
        }

        $stmt->bind_param(
            "iiiissssi",
            $opcion,
            $idArchivo,
            $idSiniestro,
            $idUsuario,
            $tipo,
            $nombreArchivo,
            $mime,
            $null,
            $idComentario
        );

        $stmt->send_long_data(7, $contenido);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception($error);
        }

        $this->limpiarResultados($stmt);
        $stmt->close();

        return true;
    }

    // Eliminar archivo
    public function eliminar($idArchivo)
    {
        $opcion = 3;
        $idSiniestro = 0;
        $idUsuario = 0;
        $tipo = null;
        $nombre = null;
        $mime = null;
        $archivo = null;
        $idComentario = null;

        $stmt = $this->conexion->prepare(
            "CALL sp_multimedia(?,?,?,?,?,?,?,?,?)"
        );

        if (!$stmt) {
            throw new Exception("Error al preparar eliminación.");
        }

        $stmt->bind_param(
            "iiiissssi",
            $opcion,
            $idArchivo,
            $idSiniestro,
            $idUsuario,
            $tipo,
            $nombre,
            $mime,
            $archivo,
            $idComentario
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

    // Obtener archivo completo
    public function obtenerArchivo($idArchivo)
    {
        $sql = "
            SELECT
                IdArchivo,
                IdSiniestro,
                NombreArchivo,
                TipoMime,
                Archivo
            FROM multimedia
            WHERE IdArchivo = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta del archivo.");
        }

        $stmt->bind_param("i", $idArchivo);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $archivo = $resultado->fetch_assoc();

        $stmt->close();

        return $archivo;
    }

    // Limpiar resultados pendientes
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

    
    // Obtener archivo verificando que pertenezca al siniestro
    public function obtenerArchivoPorSiniestro(
        $idArchivo,
        $idSiniestro
    ) {

        $sql = "
            SELECT
                IdArchivo,
                IdSiniestro,
                NombreArchivo,
                TipoMime,
                Archivo
            FROM multimedia
            WHERE IdArchivo = ?
            AND IdSiniestro = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar descarga multimedia."
            );
        }

        $stmt->bind_param(
            "ii",
            $idArchivo,
            $idSiniestro
        );

        if (!$stmt->execute()) {
            $stmt->close();

            throw new Exception(
                "No se pudo consultar el archivo."
            );
        }

        $resultado = $stmt->get_result();

        $archivo = $resultado->fetch_assoc();

        $resultado->free();
        $stmt->close();

        return $archivo ?: null;
    }

}
?>
