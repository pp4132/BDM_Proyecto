
<div class="card mb-4 shadow-sm">

    <div class="card-header">
        <strong>Editar siniestro</strong>.
    </div>

    <div class="card-body">

        <form action="actualizar_siniestros.php" method="POST">

            <input type="hidden"
                   name="idSiniestro"
                   value="<?= $siniestro['IdSiniestro'] ?>">

            <div class="mb-2">
                <label>Tipo</label>

                <input type="text"
                       name="tipo"
                       class="form-control"
                       value="<?= htmlspecialchars($siniestro['Tipo']) ?>"
                       required>
            </div>

            <div class="mb-2">
                <label>Estado</label>

                <select name="estado" class="form-select" required>
                     <option value="Pendiente" <?= $siniestro['Estado']=='Pendiente'?'selected':'' ?>> Pendiente</option>
                    <option value="Rechazado">Rechazado</option>
                    <option value="Aceptado">Aceptado</option>
                    <option value="Aceptado con pago de deducible">Aceptado con pago de deducible</option>
                    <option value="Aceptado sin pago de deducible">Aceptado sin pago de deducible</option>
                    <option value="Aplica pago para reparación de la unidad">Aplica pago para reparación de la unidad</option>
                    <option value="Pérdida total">Pérdida total</option>
                </select>
            </div>

            <div class="mb-2">
                <label>Descripción</label>

                <textarea name="descripcion"
                          class="form-control"
                          required><?= htmlspecialchars($siniestro['Descripcion']) ?></textarea>
            </div>

            <div class="mb-2">
                <label>Ubicación</label>

                <input type="text"
                       name="ubicacion"
                       class="form-control"
                       value="<?= htmlspecialchars($siniestro['Ubicacion']) ?>">
            </div>

            <div class="row">

                <div class="col-md-6">
                    <label>Monto estimado</label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="montoEstimado"
                           class="form-control"
                           value="<?= $siniestro['MontoEstimado'] ?>">
                </div>

                <div class="col-md-6">
                    <label>Monto aprobado</label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="montoAprobado"
                           class="form-control"
                           value="<?= $siniestro['MontoAprobado'] ?>">
                </div>

            </div>

            <button class="btn btn-primary mt-3">
                Guardar cambios
            </button>

        </form>

    </div>

</div>
