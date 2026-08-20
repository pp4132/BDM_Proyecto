<div class="card mb-4 shadow-sm">

    <div class="card-header">
        <strong>Reparaciones</strong>.
    </div>

    <div class="card-body">

    <?php if (!empty($reparacionesArray)): ?>

        <?php foreach($reparacionesArray as $rep): ?>

            <?php

            $badge = 'secondary';

            if ($rep['Estado'] === 'Pendiente') {
                $badge = 'warning';
            }
            elseif ($rep['Estado'] === 'En proceso') {
                $badge = 'primary';
            }
            elseif ($rep['Estado'] === 'Finalizada') {
                $badge = 'success';
            }

            ?>

            <div class="card mb-3 shadow-sm">

                <div class="card-body">

                    <h6 class="mb-2">
                        <?= htmlspecialchars($rep['Taller']) ?>
                    </h6>

                    <p class="mb-2">
                        <?= htmlspecialchars($rep['DescripcionTrabajo']) ?>
                    </p>

                    <p class="mb-1">
                        <strong>Estado:</strong>

                        <span class="badge bg-<?= $badge ?>">
                            <?= htmlspecialchars($rep['Estado']) ?>
                        </span>
                    </p>

                    <p class="mb-1">
                        <strong>Costo:</strong>
                        $<?= number_format($rep['CostoFinal'],2) ?>
                    </p>

                    <?php if (!empty($rep['FechaInicio'])): ?>
                        <p class="mb-1">
                            <strong>Inicio:</strong>
                            <?= $rep['FechaInicio'] ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($rep['FechaFin'])): ?>
                        <p class="mb-1">
                            <strong>Fin:</strong>
                            <?= $rep['FechaFin'] ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($usuario['Rol'] === 'Supervisor' || $usuario['Rol'] === 'Ajustador' && $rep['Estado'] !== 'Finalizada'): ?>

                        <form
                            action="actualizarReparacion.php"
                            method="POST"
                            class="mt-3"
                            onsubmit="return confirm('¿Actualizar reparación?')"
                        >

                            <input
                                type="hidden"
                                name="idReparacion"
                                value="<?= $rep['IdReparacion'] ?>"
                            >

                            <input
                                type="hidden"
                                name="idSiniestro"
                                value="<?= $siniestro['IdSiniestro'] ?>"
                            >

                            <?php if ($usuario['Rol'] === 'Supervisor'): ?> 
                            <select
                                name="estado"
                                class="form-select form-select-sm"
                            >

                                <option value="Pendiente"
                                    <?= $rep['Estado']=='Pendiente' ? 'selected' : '' ?>>
                                    Pendiente
                                </option>

                                <option value="En proceso"
                                    <?= $rep['Estado']=='En proceso' ? 'selected' : '' ?>>
                                    En proceso
                                </option>

                                <option value="Finalizada"
                                    <?= $rep['Estado']=='Finalizada' ? 'selected' : '' ?>>
                                    Finalizada
                                </option>

                            </select>

                            <button class="btn btn-sm btn-primary mt-2">
                                Actualizar
                            </button>
                             <?php endif; ?>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p class="text-muted">
            No hay reparaciones registradas.
        </p>

    <?php endif; ?>

    </div>

</div>