
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/Usuario.php';

class AuthController {

    private $modelo;

    public function __construct($conexion) {
        $this->modelo = new Usuario($conexion);
    }

    // Iniciar sesión
    public function login($email, $password) {

        $usuario = $this->modelo->buscarParaLogin($email);

        if (!$usuario) {
            return false;
        }

        // Comprobar si el usuario está activo
        if ((int)$usuario['Activo'] !== 1) {
            return false;
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['Contrasena'])) {
            return false;
        }

        // Renovar identificador de sesión
        if (!session_regenerate_id(true)) {
            return false;
        }

        // Guardar información en sesión
        $_SESSION['usuario_id'] = $usuario['IdUsuario'];
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['Rol'] = $usuario['Rol'];
        $_SESSION['Foto']= $usuario['Foto'];

        return true;
    }

    // Obtener información del usuario autenticado
    public function obtenerUsuarioSesion() {

        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        $idUsuario = (int)$_SESSION['usuario_id'];

        $usuario = $this->modelo->obtenerPorId($idUsuario);

        if (!$usuario) {
            return false;
        }

        // Conservar el comportamiento del sistema actual
        $this->modelo->establecerUsuarioActual(
            $usuario['IdUsuario']
        );

        return $usuario;
    }

    // Cerrar sesión
    public function logout() {

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
    }

    // Validar acceso por roles
    public function validarRol($rolesPermitidos, $usuario) {

        if (
            !$usuario ||
            !in_array($usuario['Rol'], $rolesPermitidos, true)
        ) {
            header("Location: ../view/panel.php");
            
            exit();
        }
    }
}
?>
