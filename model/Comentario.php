
<?php

class Comentario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // =====================================
    // REGISTRAR COMENTARIO O RESPUESTA
    // =====================================

    public function guardar(
        $idSiniestro,
        $idUsuario,
        $comentario,
        $idComentarioPadre = null
    ) {

        $sql = "CALL sp_comentarios(?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        $opcion = 1;
        $idComentario = 0;

        $stmt->bind_param(
            "iiiisi",
            $opcion,
            $idComentario,
            $idSiniestro,
            $idUsuario,
            $comentario,
            $idComentarioPadre
        );

        try {

            $stmt->execute();

            $resultado = $stmt->get_result();

            $fila = $resultado
                ? $resultado->fetch_assoc()
                : null;

            if ($resultado) {
                $resultado->free();
            }

            $this->limpiarResultados($stmt);

            if (!$fila || !isset($fila['idComentario'])) {
                throw new Exception(
                    "No se pudo registrar el comentario."
                );
            }

            return (int)$fila['idComentario'];

        } finally {

            $stmt->close();

        }
    }

    // =====================================
    // LISTAR COMENTARIOS DE UN SINIESTRO
    // =====================================

    public function listarPorSiniestro($idSiniestro)
    {
        $sql = "
            SELECT
                c.IdComentario,
                c.IdSiniestro,
                c.IdUsuario,
                c.Comentario,
                c.Fecha,
                c.IdComentarioPadre,
                u.Nombre,
                u.Apellidos,
                r.Nombre AS Rol
            FROM comentarios c
            INNER JOIN usuarios u
                ON u.IdUsuario = c.IdUsuario
            INNER JOIN roles r
                ON r.IdRol = u.IdRol
            WHERE c.IdSiniestro = ?
            ORDER BY c.Fecha ASC, c.IdComentario ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bind_param("i", $idSiniestro);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $comentarios = $resultado->fetch_all(
            MYSQLI_ASSOC
        );

        $stmt->close();

        return $comentarios;
    }

    // =====================================
    // VALIDAR COMENTARIO PADRE
    // =====================================

    public function validarPadre(
        $idComentario,
        $idSiniestro
    ) {

        $sql = "
            SELECT IdComentario
            FROM comentarios
            WHERE IdComentario = ?
            AND IdSiniestro = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bind_param(
            "ii",
            $idComentario,
            $idSiniestro
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $valido = $resultado->num_rows > 0;

        $stmt->close();

        return $valido;
    }

    // =====================================
    // LIMPIAR RESULTADOS DE PROCEDIMIENTOS
    // =====================================

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
