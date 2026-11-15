<?php

class Usuario {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Buscar usuario para iniciar sesión
    public function buscarParaLogin($email) {

        $sql = "
            SELECT
                IdUsuario,
                Nombre,
                Email,
                Contrasena,
                Rol,
                Activo,
                Foto
            FROM vw_usuario_login
            WHERE Email = ?
            LIMIT 1
        ";
        

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta de usuario.");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        return $usuario;
    }

    public function obtenerPorId($idUsuario)
    {
        $sql = "
            SELECT
                IdUsuario,
                Nombre,
                Apellidos,
                Email,
                Telefono,
                Rol
            FROM vw_usuarios_sesion
            WHERE IdUsuario = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta del usuario.");
        }

        $stmt->bind_param("i", $idUsuario);

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar el usuario.");
        }

        $resultado = $stmt->get_result();

        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        return $usuario;
    }

    // Establecer variable de usuario en MySQL
    public function establecerUsuarioActual($idUsuario) {

        $stmt = $this->conexion->prepare(
            "SET @idUsuario = ?"
        );

        if (!$stmt) {
            throw new Exception("Error al establecer usuario actual.");
        }

        $stmt->bind_param("i", $idUsuario);
        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    
    // Registrar nuevo usuario mediante procedimiento almacenado
   public function registrar($datos)
    {
        $sql = "CALL sp_usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar el registro.");
        }

        $opcion = 1;
        $idUsuario = null;
        $idRol = 3;

        $nombre = $datos['nombre'];
        $apellidos = $datos['apellidos'];
        $alias = $datos['alias'];
        $fechaNacimiento = $datos['fecha_nacimiento'];
        $foto = $datos['foto'];
        $genero = $datos['genero'];
        $email = $datos['email'];
        $contrasena = $datos['contrasena'];
        $telefono = $datos['telefono'];

        $stmt->bind_param(
            "iiisssssssss",
            $opcion,
            $idUsuario,
            $idRol,
            $nombre,
            $apellidos,
            $alias,
            $fechaNacimiento,
            $foto,
            $genero,
            $email,
            $contrasena,
            $telefono
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception($error);
        }

        $resultado = $stmt->get_result();

        if (!$resultado) {
            $stmt->close();
            throw new Exception("No se recibió el identificador del usuario.");
        }

        $usuario = $resultado->fetch_assoc();

        $resultado->free();

        // Limpiar resultados pendientes del procedimiento almacenado.
        while ($stmt->more_results()) {
            $stmt->next_result();

            $pendiente = $stmt->get_result();

            if ($pendiente) {
                $pendiente->free();
            }
        }

        $stmt->close();

        if (!$usuario || !isset($usuario['id'])) {
            throw new Exception("No se pudo recuperar el ID del usuario.");
        }

        return $usuario['id'];
    }

    public function obtenerMensajeBienvenida($idUsuario)
    {
        $sql = "
            SELECT fn_mensaje_bienvenida(?) AS Mensaje
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar el mensaje de bienvenida.");
        }

        $stmt->bind_param("i", $idUsuario);

        if (!$stmt->execute()) {
            throw new Exception("Error al consultar el mensaje.");
        }

        $resultado = $stmt->get_result();

        $fila = $resultado->fetch_assoc();

        $stmt->close();

        return $fila['Mensaje'] ?? '';
    }

    public function obtenerDatosActuales($idUsuario)
    {
        $sql = "
            SELECT
                IdRol,
                Alias,
                FechaNacimiento,
                Genero,
                Email,
                Contrasena,
                Foto
            FROM usuarios
            WHERE IdUsuario = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta del usuario.");
        }

        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();

        $stmt->close();

        return $datos;
    }


    public function actualizar($idUsuario, $datos)
    {
        $sql = "CALL sp_usuarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar actualización.");
        }

        $opcion = 2;

        $stmt->bind_param(
            "iiisssssssss",
            $opcion,
            $idUsuario,
            $datos['IdRol'],
            $datos['Nombre'],
            $datos['Apellidos'],
            $datos['Alias'],
            $datos['FechaNacimiento'],
            $datos['Foto'],
            $datos['Genero'],
            $datos['Email'],
            $datos['Contrasena'],
            $datos['Telefono']
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception($error);
        }

        // Limpiar resultados pendientes del procedimiento
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


}
?>
