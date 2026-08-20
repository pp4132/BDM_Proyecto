<?php
include("sesion.php");

// =========================
// VALIDAR ROL
// =========================
if (!in_array($usuario['Rol'], ['Supervisor','Ajustador'])) {
    header("Location: PanelUsuario.php");
    exit();
}

// =========================
// QUERY DE BÚSQUEDA
// =========================
$query = trim($_GET['query'] ?? '');
$resultados = [];

if (!empty($query)) {

    // Usamos una vista global creada en MySQL que unifica todos los registros
    $stmt = $conexion->prepare("
        SELECT
            TipoRegistro,  -- Siniestro, Reparacion, HistorialSiniestro, HistorialReparacion
            Id,
            Ref,
            Estado,
            Tipo,
            Ubicacion,
            Fecha
        FROM vw_busqueda_global
        WHERE 
            Ref LIKE CONCAT('%', ?, '%')
            OR Estado LIKE CONCAT('%', ?, '%')
            OR Tipo LIKE CONCAT('%', ?, '%')
            OR Ubicacion LIKE CONCAT('%', ?, '%')
        ORDER BY Fecha DESC
        LIMIT 100
    ");

    $stmt->bind_param("ssss", $query, $query, $query, $query);
    $stmt->execute();

    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $resultados[] = $row;
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Búsqueda Global - Siniestros</title>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylePanel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-corporate-dark fixed-top px-4">
    <a class="navbar-brand d-flex align-items-center" href="Index.html">
      <img src="Img/Logo.png" alt="Logo Empresa" height="100" class="me-2">
      <span>Sistema de Gestión de Siniestros</span>
    </a>
    <div class="ms-auto">
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </div>
  </nav>
</header>

<div class="sidebar">
    <?php include("sidebar.php"); ?>
</div>

<div class="content">
    <br><br>
    <h2 class="mb-4">Buscar en el sistema</h2>

    <form method="GET" class="search-bar mb-4">
        <input type="text" class="form-control mb-2" name="query"
            placeholder="Folio, estado, tipo o taller..." value="<?= htmlspecialchars($query) ?>" required>
        <button type="submit" class="btn btn-corporate">Buscar</button>
    </form>

    <?php if (!empty($resultados)): ?>
        <?php foreach ($resultados as $r): ?>
            <div class="card mb-3">
                <div class="card-body">

                    <h5><?= htmlspecialchars($r['TipoRegistro']) ?>: <?= htmlspecialchars($r['Ref']) ?></h5>
                    <p><strong>Estado:</strong> <?= htmlspecialchars($r['Estado']) ?></p>
                    <p><strong>Tipo/Taller:</strong> <?= htmlspecialchars($r['Tipo']) ?></p>
                    <p><strong>Detalle:</strong> <?= htmlspecialchars($r['Ubicacion']) ?></p>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($r['Fecha'])) ?></p>

                    <?php if ($r['TipoRegistro'] === 'Siniestro' || $r['TipoRegistro'] === 'Reparacion'): ?>
                        <a href="Siniestros.php?id=<?= $r['Id'] ?>" class="btn btn-sm btn-outline-primary mt-1">
                            Ir al siniestro
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se encontraron resultados.</p>
    <?php endif; ?>
</div>

<footer class="text-center">
  © 2026 Departamento de Siniestros
</footer>

<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>