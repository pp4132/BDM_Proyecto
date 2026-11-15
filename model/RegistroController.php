<?php

require_once __DIR__ . '/../model/Usuario.php';

class RegistroController
{
    private $usuarioModel;

    public function __construct($conexion)
    {
        $this->usuarioModel = new Usuario($conexion);
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../app/view/Registro.php");
            exit();
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $alias = trim($_POST['alias'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errores = [];

        // Validar nombre
        if (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ' -]+$/u", $nombre)) {
            $errores[] = "El nombre solo debe contener letras.";
        }

        // Validar apellidos
        if (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/u", $apellidos)) {
            $errores[] = "Los apellidos solo deben contener letras.";
        }

        // Validar fecha
        $fecha = DateTime::createFromFormat('!Y-m-d', $fechaNacimiento);

        if (
            !$fecha ||
            $fecha->format('Y-m-d') !== $fechaNacimiento
        ) {
            $errores[] = "Fecha de nacimiento inválida.";
        } else {
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha)->y;

            if ($fecha > $hoy || $edad < 18) {
                $errores[] = "Debes ser mayor de 18 años.";
            }
        }

        // Validar correo
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Correo electrónico no válido.";
        }

        // Validar contraseña
        if (strlen($password) < 8) {
            $errores[] = "Debe tener al menos 8 caracteres.";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = "Debe incluir una letra minúscula.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = "Debe incluir una letra mayúscula.";
        }

        if (!preg_match('/\d/', $password)) {
            $errores[] = "Debe incluir un número.";
        }

        if (!preg_match('/[\W_]/', $password)) {
            $errores[] = "Debe incluir un carácter especial.";
        }

        // Validar género
        if (!in_array($genero, ['M', 'F', 'Otro'], true)) {
            $errores[] = "Selecciona un género válido.";
        }

        // Validar fotografía
        $foto = null;

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $errores[] = "No se pudo cargar la fotografía.";
            } else {
                if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                    $errores[] = "La imagen supera el límite de 2 MB.";
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $tipo = $finfo->file($_FILES['foto']['tmp_name']);

                    $tiposPermitidos = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];

                    if (!in_array($tipo, $tiposPermitidos, true)) {
                        $errores[] = "Formato de imagen no válido.";
                    } else {
                        $foto = file_get_contents(
                            $_FILES['foto']['tmp_name']
                        );
                    }
                }
            }
        }

        // Guardar errores sin exponer datos sensibles
        if (!empty($errores)) {
            $_SESSION['errores_registro'] = $errores;

            $_SESSION['datos_registro'] = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'alias' => $alias,
                'genero' => $genero,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fechaNacimiento,
                'email' => $email
            ];

            header("Location: ../app/view/Registro.php");
            exit();
        }

        // Preparar datos para el modelo
        $datos = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'alias' => $alias,
            'genero' => $genero,
            'telefono' => $telefono,
            'fecha_nacimiento' => $fechaNacimiento,
            'email' => $email,
            'foto' => $foto,
            'contrasena' => password_hash(
                $password,
                PASSWORD_DEFAULT
            )
        ];

        try {

            $idUsuario = $this->usuarioModel->registrar($datos);

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $idUsuario;
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['Rol'] = 'Asegurado';

            header("Location: ../app/view/panel.php");
            exit();

        } catch (Throwable $e) {

            error_log("Error en registro: " . $e->getMessage());

            $_SESSION['errores_registro'] = [
                "No se pudo completar el registro. Comprueba los datos e inténtalo nuevamente."
            ];

            $_SESSION['datos_registro'] = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'alias' => $alias,
                'genero' => $genero,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fechaNacimiento,
                'email' => $email
            ];

            header("Location: ../app/view/Registro.php");
            exit();
        }
    }
}