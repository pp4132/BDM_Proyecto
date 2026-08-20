<?php
include("sesion.php");

// =========================
// VALIDAR SESION Y ROL
// =========================
if (!in_array($usuario['Rol'], ['Supervisor','Ajustador'])) {

    header("Location: PanelUsuario.php");
    exit();
}

// =========================
// DATOS
// =========================
$opcion = $_POST['opcion'] ?? 0;

$idSiniestro = $_POST['idSiniestro'] ?? 0;
$idArchivo = $_POST['idArchivo'] ?? 0;

$idUsuario = $usuario['IdUsuario'];

// =========================
// VALIDAR SINIESTRO
// =========================
if (!is_numeric($idSiniestro) || $idSiniestro <= 0) {

    die("Siniestro inválido");
}

try {

    $conexion->begin_transaction();

    // =========================
    // SUBIR ARCHIVOS
    // =========================
    if ($opcion == 1) {

        if (empty($_FILES['archivos']['name'][0])) {

            die("No se seleccionaron archivos");
        }

        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {

            // validar upload
            if ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_OK) {

                continue;
            }

            // validar tamaño
            $tamano = $_FILES['archivos']['size'][$key];

            if ($tamano > 25 * 1024 * 1024) {

                continue;
            }

            $contenido = file_get_contents($tmp_name);

            $mime = mime_content_type($tmp_name);

            $nombreArchivo = $_FILES['archivos']['name'][$key];

            // validar tipo
            if (str_starts_with($mime, 'image/')) {

                $tipoDB = 'imagen';

            }
            elseif (str_starts_with($mime, 'video/')) {

                $tipoDB = 'video';

            }
            else {

                continue;
            }

            $stmt = $conexion->prepare("
                CALL sp_multimedia(
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");

            $idArchivoNull = 0;
            $idComentario = 0;
            $null = NULL;

            $stmt->bind_param(
                "iiiisssbi",
                $opcion,
                $idArchivoNull,
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

                throw new Exception($stmt->error);
            }

            $stmt->close();

            // limpiar results
            while (
                $conexion->more_results()
                && $conexion->next_result()
            ) {

                $extra = $conexion->store_result();

                if ($extra instanceof mysqli_result) {
                    $extra->free();
                }
            }
        }
    }

    // =========================
    // ELIMINAR ARCHIVO
    // =========================
    elseif ($opcion == 3) {

        if (!is_numeric($idArchivo) || $idArchivo <= 0) {

            die("Archivo inválido");
        }

        $stmt = $conexion->prepare("
            CALL sp_multimedia(
                ?, ?, 0, 0,
                '', '', '',
                NULL, 0
            )
        ");

        $stmt->bind_param(
            "ii",
            $opcion,
            $idArchivo
        );

        if (!$stmt->execute()) {

            throw new Exception($stmt->error);
        }

        $stmt->close();

        while (
            $conexion->more_results()
            && $conexion->next_result()
        ) {

            $extra = $conexion->store_result();

            if ($extra instanceof mysqli_result) {
                $extra->free();
            }
        }
    }

    // =========================
    // COMMIT
    // =========================
    $conexion->commit();

    header("Location: Siniestros.php?id=" . $idSiniestro);

} catch (Exception $e) {

    $conexion->rollback();

    die("Error multimedia: " . $e->getMessage());
}
?>