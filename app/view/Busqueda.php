<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Búsqueda Global - Siniestros</title>

    <link rel="stylesheet"
          href="../assets/public/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="../assets/css/stylePanel.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

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

            <a href="../auth/logout.php"
               class="btn btn-outline-light btn-sm">

                Cerrar Sesión

            </a>

        </div>

    </nav>

</header>


<div class="sidebar">

    <?php
    require_once __DIR__ . '/sidebar.php';
    ?>

</div>


<div class="content">

    <br><br>

    <h2 class="mb-4">Buscar en el sistema</h2>


    <!-- FORMULARIO DE BÚSQUEDA -->

    <form method="GET"
          action="BusquedaController.php"
          class="search-bar mb-4">

        <input
            type="text"
            class="form-control mb-2"
            name="query"
            placeholder="Folio, estado, tipo o taller..."
            value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit"
                class="btn btn-corporate">

            <i class="bi bi-search"></i>
            Buscar

        </button>

    </form>


    <!-- RESULTADOS -->

    <?php if ($query !== ''): ?>

        <?php if (!empty($resultados)): ?>

            <?php foreach ($resultados as $r): ?>

                <div class="card mb-3">

                    <div class="card-body">

                        <h5>
                            <?= htmlspecialchars($r['TipoRegistro']) ?>:
                            <?= htmlspecialchars($r['Ref']) ?>
                        </h5>

                        <p>
                            <strong>Estado:</strong>
                            <?= htmlspecialchars($r['Estado'] ?? '') ?>
                        </p>

                        <p>
                            <strong>Tipo/Taller:</strong>
                            <?= htmlspecialchars($r['Tipo'] ?? '') ?>
                        </p>

                        <p>
                            <strong>Detalle:</strong>
                            <?= htmlspecialchars($r['Ubicacion'] ?? '') ?>
                        </p>

                        <p>
                            <strong>Fecha:</strong>

                            <?php if (!empty($r['Fecha'])): ?>

                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($r['Fecha'])
                                ) ?>

                            <?php else: ?>

                                Sin fecha

                            <?php endif; ?>

                        </p>


                        <?php if ($r['TipoRegistro'] === 'Siniestro'): ?>

                            <a
                                href="Siniestros.php?id=<?= urlencode($r['Id']) ?>"
                                class="btn btn-sm btn-outline-primary mt-1">

                                Ir al siniestro

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="alert alert-warning">

                No se encontraron resultados para:

                <strong>
                    <?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>
                </strong>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>


<footer class="text-center">

    © 2026 Departamento de Siniestros

</footer>


<script src="../assets/public/js/bootstrap.bundle.min.js"></script>

</body>
</html>