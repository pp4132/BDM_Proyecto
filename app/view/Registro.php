<?php

require_once __DIR__ . '/../../config/config.php';
session_start();

$errores = $_SESSION['errores_registro'] ?? [];
$datos = $_SESSION['datos_registro'] ?? [];

unset(
    $_SESSION['errores_registro'],
    $_SESSION['datos_registro']
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro de Asegurado</title>

    <link rel="stylesheet" href="../assets/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styleInicioSesion.css">

</head>

<body>

<header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-corporate-dark px-4">

        <a class="navbar-brand d-flex align-items-center"
           href="Index.html">

            <img
                src="../assets/img/Logo.png"
                alt="Logo Empresa"
                height="100"
                class="me-2"
            >

            <span class="fw-semibold">
                Sistema de Gestión de Siniestros
            </span>

        </a>

        <div class="ms-auto">

            <a href="Index.html"
               class="btn btn-outline-light btn-sm me-2">

                Página Principal

            </a>

            <a href="InicioSesion.html"
               class="btn btn-light btn-sm">

                Iniciar Sesión

            </a>

        </div>

    </nav>

</header>


<main class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-corporate">

    <section class="card corporate-card-register shadow-sm">

        <header class="card-header text-center bg-white border-0 pt-4">

            <h1 class="h4 fw-bold">
                Registro de Asegurado
            </h1>

            <p class="text-muted small mb-0">
                Complete la información solicitada
            </p>

        </header>


        <div class="card-body p-4">

            <?php if (!empty($errores)): ?>

                <div class="alert alert-danger">

                    <strong>No se pudo completar el registro:</strong>

                    <ul class="mb-0 mt-2">

                        <?php foreach ($errores as $error): ?>

                            <li>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <form
                id="formRegistro"
                action="../../auth/registro.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="<?= htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Apellidos
                        </label>

                        <input
                            type="text"
                            name="apellidos"
                            class="form-control"
                            value="<?= htmlspecialchars($datos['apellidos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >

                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Alias
                    </label>

                    <input
                        type="text"
                        name="alias"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['alias'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Fecha de Nacimiento
                    </label>

                    <input
                        type="date"
                        name="fecha_nacimiento"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['fecha_nacimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Género
                    </label>

                    <select name="genero" class="form-select" required>

                        <option value="">
                            Seleccione
                        </option>

                        <option
                            value="M"
                            <?= ($datos['genero'] ?? '') === 'M' ? 'selected' : '' ?>
                        >
                            Masculino
                        </option>

                        <option
                            value="F"
                            <?= ($datos['genero'] ?? '') === 'F' ? 'selected' : '' ?>
                        >
                            Femenino
                        </option>

                        <option
                            value="Otro"
                            <?= ($datos['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>
                        >
                            Otro
                        </option>

                    </select>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <input
                        type="tel"
                        name="telefono"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Correo Electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Foto
                    </label>

                    <input
                        type="file"
                        name="foto"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <small class="text-muted">
                        Formatos permitidos: JPG, PNG y WEBP. Máximo 2 MB.
                    </small>

                </div>


                <div class="d-grid">

                    <button
                        type="submit"
                        class="btn btn-corporate fw-bold"
                    >

                        Registrarse

                    </button>

                </div>

            </form>

        </div>


        <footer class="card-footer text-center small text-muted bg-white border-0 pb-3">

            © 2026 Departamento de Siniestros

        </footer>

    </section>

</main>


<script src="../assets/public/js/bootstrap.bundle.min.js"></script>

</body>
</html>