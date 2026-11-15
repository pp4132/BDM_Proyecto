
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Crear Siniestro</title>

    <link rel="stylesheet" href="../assets/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/stylePanel.css">
    <link rel="stylesheet" href="../assets/css/styleInicioSesion.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

<!-- NAVBAR -->
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-corporate-dark fixed-top px-4">

        <a class="navbar-brand d-flex align-items-center"
           href="Index.html">

            <img src="../assets/img/Logo.png"
                 alt="Logo Empresa"
                 height="100"
                 class="me-2">

            <span>Sistema de Gestión de Siniestros</span>

        </a>

        <div class="ms-auto">
            <a href="../../auth/logout.php"
               class="btn btn-outline-light btn-sm">
                Cerrar Sesión
            </a>
        </div>

    </nav>
</header>


<!-- SIDEBAR -->
<div class="sidebar">

    <?php require __DIR__ . '/sidebar.php'; ?>

</div>


<!-- CONTENIDO -->
<div class="content">

    <h2 class="mt-5">Crear Nuevo Siniestro</h2>

    <div class="card shadow-sm p-4">

        <form id="formSiniestro"
            method="POST"
            action="../controllers/SiniestroController.php"
            enctype="multipart/form-data">

            <!-- TIPO -->
            <div class="mb-3">

                <label class="form-label">
                    Tipo de siniestro
                </label>

                <select class="form-select"
                        name="tipo"
                        required>

                    <option value="">Seleccionar...</option>
                    <option value="Daño material">Daño material</option>
                    <option value="Robo">Robo</option>
                    <option value="Choque">Choque</option>
                    <option value="Fallo de frenos">Fallo de frenos</option>

                </select>

            </div>


            <!-- VEHÍCULO -->
            <div class="mb-3">

                <label class="form-label">
                    Vehículo
                </label>

                <select class="form-select"
                        name="IdVehiculo"
                        required>

                    <option value="">Seleccionar...</option>

                    <?php foreach ($vehiculosArray as $v): ?>

                        <option value="<?= htmlspecialchars($v['IdVehiculo']) ?>">

                            <?= htmlspecialchars(
                                $v['Marca'] . " " .
                                $v['Modelo'] . " - " .
                                $v['Placas']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- DESCRIPCIÓN -->
            <div class="mb-3">

                <label class="form-label">
                    Descripción
                </label>

                <textarea class="form-control"
                          name="descripcion"
                          rows="4"
                          required></textarea>

            </div>


            <!-- FECHA -->
            <div class="mb-3">

                <label class="form-label">
                    Fecha del siniestro
                </label>

                <input type="date"
                       class="form-control"
                       name="fecha"
                       required>

            </div>


            <!-- UBICACIÓN -->
            <div class="mb-3">

                <label class="form-label">
                    Ubicación
                </label>

                <input type="text"
                       class="form-control"
                       name="ubicacion"
                       placeholder="Ej. Monterrey, NL">

            </div>


            <!-- EVIDENCIAS -->
            <div class="mb-3">

                <label class="form-label">
                    Evidencias (imágenes o videos)
                </label>

                <input type="file"
                       class="form-control"
                       id="archivos"
                       name="archivos[]"
                       multiple
                       accept="image/*,video/*">

            </div>


            <!-- PREVISUALIZACIÓN -->
            <div id="preview"
                 class="mb-3 d-flex flex-wrap gap-2">
            </div>


            <!-- BOTONES -->
            <div class="d-flex gap-2">

                <button type="submit"
                        class="btn btn-corporate">

                    <i class="bi bi-check-circle"></i>
                    Crear Siniestro

                </button>

                <a href="panel.php"
                   class="btn btn-secondary">

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</div>


<!-- FOOTER -->
<footer class="text-center">
    © 2026 Departamento de Siniestros
</footer>


<script src="../assets/public/js/bootstrap.bundle.min.js"></script>


<!-- PREVISUALIZACIÓN DE ARCHIVOS -->
<script>

const inputArchivos = document.getElementById("archivos");
const preview = document.getElementById("preview");

inputArchivos.addEventListener("change", () => {

    preview.innerHTML = "";

    const archivos = [...inputArchivos.files];

    if (archivos.length > 5) {

        alert("Máximo 5 archivos");

        inputArchivos.value = "";

        return;
    }

    archivos.forEach(file => {

        const url = URL.createObjectURL(file);

        if (file.type.startsWith("image/")) {

            const img = document.createElement("img");

            img.src = url;
            img.style.width = "100px";
            img.style.height = "100px";
            img.style.objectFit = "cover";
            img.classList.add("rounded");

            preview.appendChild(img);

        }

        else if (file.type.startsWith("video/")) {

            const video = document.createElement("video");

            video.src = url;
            video.width = 120;
            video.controls = true;

            preview.appendChild(video);

        }

    });

});

</script>

</body>
</html>
