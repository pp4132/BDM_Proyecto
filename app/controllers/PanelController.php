<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../model/Usuario.php';
require_once __DIR__ . '/../../model/Siniestro.php';
require_once __DIR__ . '/../../model/Vehiculo.php';

class PanelController
{
    private $usuarioModel;
    private $siniestroModel;
    private $vehiculoModel;

    public function __construct($conexion)
    {
        $this->usuarioModel = new Usuario($conexion);
        $this->siniestroModel = new Siniestro($conexion);
        $this->vehiculoModel = new Vehiculo($conexion);
    }

    public function obtenerPanel()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../view/InicioSesion.html");
            exit();
        }

        $idUsuario = (int) $_SESSION['usuario_id'];

        // Obtener usuario
        $usuario = $this->usuarioModel->obtenerPorId($idUsuario);

        if (!$usuario) {
            $_SESSION = [];
            session_destroy();

            header("Location: ../view/InicioSesion.html");
            exit();
        }

        $rol = $usuario['Rol'];

        $_SESSION['Rol'] = $rol;

        // Obtener siniestros
        $siniestrosArray = $this->siniestroModel->listarPorRol(
            $rol,
            $idUsuario
        );

        // Obtener vehículos
        $vehiculosArray = $this->vehiculoModel->listarPorAsegurado(
            $idUsuario
        );

        // Mensaje de bienvenida
        $mensaje = $this->usuarioModel->obtenerMensajeBienvenida(
            $idUsuario
        );

        // Contadores
        $pendientes = 0;
        $proceso = 0;
        $cerrados = 0;

        foreach ($siniestrosArray as $siniestro) {

            $estado = mb_strtolower(
                trim($siniestro['Estado'] ?? ''),
                'UTF-8'
            );

            if ($estado === 'pendiente') {
                $pendientes++;
            } elseif ($estado === 'en proceso') {
                $proceso++;
            } elseif ($estado === 'cerrado') {
                $cerrados++;
            }
        }

        return compact(
            'usuario',
            'siniestrosArray',
            'vehiculosArray',
            'mensaje',
            'pendientes',
            'proceso',
            'cerrados'
        );
    }
}