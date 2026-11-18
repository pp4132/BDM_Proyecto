<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detalles del siniestro</title>

  <link rel="stylesheet" href="../assets/public/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/styleInicioSesion.css">
  <link rel="stylesheet" href="../assets/css/stylePanel.css">

    <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- NAVBAR -->
<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-corporate-dark fixed-top px-4">
    <a class="navbar-brand d-flex align-items-center" href="Index.html">
      <img src="../assets/Img/Logo.png" alt="Logo Empresa" height="100" class="me-2">
      <span>Sistema de Gestión de Siniestros</span>
    </a>
    <div class="ms-auto">
      <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </div>
  </nav>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
  <!-- Información del usuario -->
   <?php include("sidebar.php"); ?>
</div>

<!-- CONTENIDO PRINCIPAL -->
 <br><br>
<div class="content">

<?php if (!empty($_SESSION['reparacion_mensaje'])): ?>

    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['reparacion_mensaje']) ?>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>

    <?php unset($_SESSION['reparacion_mensaje']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['reparacion_error'])): ?>

    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['reparacion_error']) ?>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>

    <?php unset($_SESSION['reparacion_error']); ?>

<?php endif; ?>

    <?php if (!empty($_SESSION['siniestro_mensaje'])): ?>

    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['siniestro_mensaje']) ?>

        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>

    <?php unset($_SESSION['siniestro_mensaje']); ?>

<?php endif; ?>
<?php if (!empty($_SESSION['siniestro_error'])): ?>

    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['siniestro_error']) ?>

        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>

    <?php unset($_SESSION['siniestro_error']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['multimedia_mensaje'])): ?>

    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['multimedia_mensaje']) ?>

        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>

    <?php unset($_SESSION['multimedia_mensaje']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['multimedia_error'])): ?>

    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['multimedia_error']) ?>

        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>

    <?php unset($_SESSION['multimedia_error']); ?>

<?php endif; ?>



    <h2>Siniestro #<?= $siniestro['IdSiniestro'] ?></h2>

        <!-- INFO GENERAL -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <p><strong>Tipo:</strong> <?= htmlspecialchars($siniestro['Tipo']) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($siniestro['Estado']) ?></p>
                <p><strong>Fecha Reporte:</strong> <?= $siniestro['FechaReporte'] ?></p>
                <p><strong>Ubicación:</strong> <?= htmlspecialchars($siniestro['Ubicacion']) ?></p>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($siniestro['Descripcion']) ?></p>
                <p><strong>Monto Estimado:</strong> $<?= number_format($siniestro['MontoEstimado'],2) ?></p>
                <p><strong>Monto Aprobado:</strong> $<?= number_format($siniestro['MontoAprobado'],2) ?></p>
            </div>
        </div>



        <!-- =====================================
            COMENTARIOS DEL SINIESTRO
        ===================================== -->

        <div class="card mb-4 shadow-sm" id="comentarios">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    <i class="bi bi-chat-dots"></i>
                    Conversación del siniestro
                </h5>

            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">

                    <strong>Comentarios y respuestas</strong>

                    <span class="badge bg-secondary">
                        Total: <span id="totalComentarios">
                            <?= (int)$totalComentarios ?>
                        </span>
                    </span>

                </div>

                <!-- MENSAJES -->
                <div id="comentariosContainer">

                    <div class="text-center text-muted">
                        Cargando comentarios...
                    </div>

                </div>

                <hr>

                <!-- RESPONDER COMENTARIO -->
                <div id="respuestaSeleccionada"
                    class="alert alert-info d-none">

                    Respondiendo a un comentario.

                    <button type="button"
                            class="btn-close float-end"
                            id="cancelarRespuesta">
                    </button>

                </div>

                <!-- FORMULARIO -->
                <form id="formComentario">

                    <input type="hidden"
                        name="comentario_csrf"
                        value="<?= htmlspecialchars($_SESSION['comentario_csrf'] ?? '') ?>">

                    <input type="hidden"
                        name="idComentarioPadre"
                        id="idComentarioPadre">

                    <textarea
                        name="comentario"
                        id="textoComentario"
                        class="form-control mb-2"
                        rows="3"
                        maxlength="10000"
                        placeholder="Escribe un comentario..."
                        required></textarea>

                    <div class="text-end">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-send"></i>
                            Enviar comentario

                        </button>

                    </div>

                </form>

                <div id="mensajeComentario" class="mt-3"></div>

            </div>

        </div>

        <div class="card mb-4 shadow-sm">

        <div class="card-header">
            <strong>Información del vehículo y asegurado</strong>
        </div>

        <div class="card-body">

            <p>
                <strong>Asegurado:</strong>
                <?= htmlspecialchars($siniestro['NombreAsegurado']) ?>
            </p>

            <p>
                <strong>Vehículo:</strong>
                <?= htmlspecialchars($siniestro['Marca']) ?>
                <?= htmlspecialchars($siniestro['Modelo']) ?>
            </p>

            <p>
                <strong>Placas:</strong>
                <?= htmlspecialchars($siniestro['Placas']) ?>
            </p>

            <p>
                <strong>Póliza:</strong>
                <?= htmlspecialchars($siniestro['NumeroPoliza']) ?>
            </p>

        </div>

    </div>




    
    <!--Actualizar siniestros-->
    <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
        <?php include("modal/Actualizar_siniestros.php"); ?>
        <?php endif; ?>

        <!--REPARACIONES DEL SINIESTRO-->
        <?php include("modal/Actualizar_reparaciones.php"); ?>

        <!--CREAR REPARACIONES-->
        <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'], true)): ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong>Agregar reparación</strong>
            </div>

            <div class="card-body">

                <form action="../controllers/ReparacionController.php"
                    method="POST"
                    class="mt-2"
                    onsubmit="return confirm('¿Crear reparación?')">

                    <input type="hidden" name="accion" value="crear">

                    <input type="hidden"
                        name="idSiniestro"
                        value="<?= (int)$siniestro['IdSiniestro'] ?>">

                    <input type="text"
                        name="taller"
                        class="form-control mb-2"
                        placeholder="Taller"
                        required>

                    <textarea name="descripcion"
                            class="form-control mb-2"
                            placeholder="Trabajo realizado"
                            required></textarea>

                    <input type="number"
                        name="costo"
                        class="form-control mb-2"
                        placeholder="Costo"
                        min="0"
                        step="0.01"
                        required>

                    <button class="btn btn-success">
                        Crear reparación
                    </button>

                </form>

            </div>
        </div>

        <?php endif; ?>



    <!-- SUBIR MULTIMEDIA -->
     <?php if (in_array($usuario['Rol'], ['Supervisor', 'Ajustador'])): ?>
    <div class="card mb-4 shadow-sm">
       <div class="card-header">
            <strong>Subir multimedia</strong>
        </div>
        <div class="card-body">
            <form action="../controllers/MultimediaController.php"
                method="POST"
                enctype="multipart/form-data">
                <input type="hidden" name="opcion" value="1">
                <input type="hidden" name="idSiniestro" value="<?= $siniestro['IdSiniestro'] ?>">
                <input type="file" name="archivos[]" multiple required class="form-control mb-2">
                <button type="submit" class="btn btn-corporate">Subir Archivos</button>
            </form>
        </div>
    </div>
    <?php endif; ?>


    <!-- LISTAR, DESCARGAR Y ELIMINAR MULTIMEDIA -->
    <div class="card mb-4 shadow-sm">

        <div class="card-header">
            <strong>
                <i class="bi bi-folder2-open"></i>
                Archivos multimedia del siniestro
            </strong>
        </div>

        <div class="card-body">

            <?php if (!empty($archivos)): ?>

                <ul class="list-group">

                    <?php foreach ($archivos as $file): ?>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">

                            <div>
                                <i class="bi bi-file-earmark"></i>

                                <strong>
                                    <?= htmlspecialchars(
                                        $file['NombreArchivo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <br>

                                <small class="text-muted">
                                    <?= htmlspecialchars(
                                        $file['TipoMime'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </small>
                            </div>

                            <div class="d-flex gap-2">
                                <!-- DESCARGAR -->
                                <a href="../controllers/MultimediaController.php?opcion=4&idArchivo=<?= (int)$file['IdArchivo'] ?>&idSiniestro=<?= (int)$siniestro['IdSiniestro'] ?>"
                                class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-download"></i>
                                    Descargar

                                </a>

                                <!-- ELIMINAR -->
                                <?php if (in_array(
                                    $usuario['Rol'],
                                    ['Supervisor', 'Ajustador'],
                                    true
                                )): ?>

                                    <form action="../controllers/MultimediaController.php"
                                        method="POST">

                                        <input type="hidden"
                                            name="opcion"
                                            value="3">

                                        <input type="hidden"
                                            name="idArchivo"
                                            value="<?= (int)$file['IdArchivo'] ?>">

                                        <input type="hidden"
                                            name="idSiniestro"
                                            value="<?= (int)$siniestro['IdSiniestro'] ?>">

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('¿Eliminar archivo?')">

                                            <i class="bi bi-trash"></i>
                                            Eliminar

                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php else: ?>

                <div class="alert alert-info mb-0">
                    No hay archivos multimedia asociados a este siniestro.
                </div>

            <?php endif; ?>

        </div>

    </div>

    <a href="../view/panel.php" class="btn btn-outline-dark mb-4">Volver al Panel</a>
</div>

<script src="../assets/public/js/bootstrap.bundle.min.js"></script>
<script>

const idSiniestro = <?= (int)$siniestro['IdSiniestro'] ?>;

const urlComentarios =
    "../controllers/ComentarioController.php?id=" + idSiniestro;

const formulario = document.getElementById("formComentario");
const contenedor = document.getElementById("comentariosContainer");

const campoPadre = document.getElementById("idComentarioPadre");
const campoTexto = document.getElementById("textoComentario");

const mensaje = document.getElementById("mensajeComentario");
const total = document.getElementById("totalComentarios");

const respuestaSeleccionada =
    document.getElementById("respuestaSeleccionada");

let ultimoContenido = "";

// CREAR ELEMENTO SEGURO
function crearElemento(tag, texto, clase = "") {

    const elemento = document.createElement(tag);

    elemento.textContent = texto;

    if (clase) {
        elemento.className = clase;
    }

    return elemento;
}

// CARGAR COMENTARIOS
async function cargarComentarios() {

    try {

        const respuesta = await fetch(urlComentarios, {
            headers: {
                "Accept": "application/json"
            }
        });

        const resultado = await respuesta.json();

        if (!resultado.success) {
            throw new Error(resultado.mensaje);
        }

        const comentarios = resultado.datos.comentarios;

        total.textContent = resultado.datos.totalComentarios;

        const nuevoContenido = JSON.stringify(comentarios);

        if (nuevoContenido === ultimoContenido) {
            return;
        }

        ultimoContenido = nuevoContenido;

        contenedor.replaceChildren();

        if (comentarios.length === 0) {

            contenedor.appendChild(
                crearElemento(
                    "div",
                    "Todavía no hay comentarios.",
                    "alert alert-info"
                )
            );

            return;
        }

        // ORGANIZAR COMENTARIOS
        const principales = comentarios.filter(
            c => !c.IdComentarioPadre
        );

        const respuestas = comentarios.filter(
            c => c.IdComentarioPadre
        );

        principales.forEach(comentario => {

            const tarjeta = crearTarjeta(comentario);

            // BUSCAR RESPUESTAS
            const hijas = respuestas.filter(
                r => Number(r.IdComentarioPadre) ===
                     Number(comentario.IdComentario)
            );

            hijas.forEach(respuesta => {

                const respuestaDiv = crearTarjeta(respuesta);

                respuestaDiv.classList.add(
                    "ms-4",
                    "border-start",
                    "border-primary"
                );

                tarjeta.appendChild(respuestaDiv);

            });

            contenedor.appendChild(tarjeta);

        });

    } catch (error) {

        console.error(error);

    }
}

// CREAR TARJETA
function crearTarjeta(comentario) {

    const tarjeta = document.createElement("div");

    tarjeta.className = "border rounded p-3 mb-3";

    const encabezado = document.createElement("div");

    encabezado.className =
        "d-flex justify-content-between flex-wrap";

    const nombre = crearElemento(
        "strong",
        comentario.Nombre + " " + comentario.Apellidos
    );

    const fecha = crearElemento(
        "small",
        comentario.Fecha,
        "text-muted"
    );

    encabezado.append(nombre, fecha);

    const rol = crearElemento(
        "small",
        comentario.Rol,
        "text-primary d-block mt-1"
    );

    const texto = crearElemento(
        "p",
        comentario.Comentario,
        "mt-2 mb-2"
    );

    texto.style.whiteSpace = "pre-wrap";

    tarjeta.append(encabezado, rol, texto);

    const boton = crearElemento(
        "button",
        "Responder",
        "btn btn-sm btn-outline-primary"
    );

    boton.type = "button";

    boton.addEventListener("click", () => {

        campoPadre.value = comentario.IdComentario;

        respuestaSeleccionada.classList.remove("d-none");

        campoTexto.focus();

        respuestaSeleccionada.firstChild.textContent =
            "Respondiendo a " +
            comentario.Nombre +
            " " +
            comentario.Apellidos + " ";

    });

    tarjeta.appendChild(boton);

    return tarjeta;
}

// CANCELAR RESPUESTA
document.getElementById("cancelarRespuesta")
    .addEventListener("click", () => {

        campoPadre.value = "";

        respuestaSeleccionada.classList.add("d-none");

    });

// ENVIAR COMENTARIO
formulario.addEventListener("submit", async function(e) {

    e.preventDefault();

    const boton = formulario.querySelector(
        'button[type="submit"]'
    );

    boton.disabled = true;

    mensaje.textContent = "";

    try {

        const datos = new FormData(formulario);

        const respuesta = await fetch(urlComentarios, {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (!resultado.success) {
            throw new Error(resultado.mensaje);
        }

        formulario.reset();

        campoPadre.value = "";

        respuestaSeleccionada.classList.add("d-none");

        mensaje.textContent = resultado.mensaje;

        mensaje.className = "alert alert-success mt-3";

        ultimoContenido = "";

        await cargarComentarios();

    } catch (error) {

        mensaje.textContent = error.message;

        mensaje.className = "alert alert-danger mt-3";

    } finally {

        boton.disabled = false;

    }

});

// CARGA INICIAL
cargarComentarios();

// ACTUALIZAR CADA 4 SEGUNDOS
setInterval(cargarComentarios, 4000);

</script>

</body>
</html>