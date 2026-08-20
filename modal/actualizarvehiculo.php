<div class="modal fade"
     id="modalEditarVehiculo<?= htmlspecialchars($vehiculo['IdVehiculo']) ?>"
     tabindex="-1"
     aria-hidden="true">

  <div class="modal-dialog modal-lg">

    <form action="actualizarVehiculo.php" method="POST">

      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header bg-warning">

          <h5 class="modal-title">
            Editar Vehículo
          </h5>

          <button type="button"
                  class="btn-close"
                  data-bs-dismiss="modal">
          </button>

        </div>

        <!-- BODY -->
        <div class="modal-body">

          <!-- ID oculto -->
          <input type="hidden"
                 name="idVehiculo"
                 value="<?= htmlspecialchars($vehiculo['IdVehiculo']) ?>">

          <div class="row">

            <!-- Tipo -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Tipo de vehículo
              </label>

              <select name="tipo"
                      class="form-select"
                      required>

                <option value="Auto"
                  <?php if($vehiculo['Tipo'] == 'Auto') echo 'selected'; ?>>
                  Auto
                </option>

                <option value="Moto"
                  <?php if($vehiculo['Tipo'] == 'Moto') echo 'selected'; ?>>
                  Moto
                </option>

                <option value="Camioneta"
                  <?php if($vehiculo['Tipo'] == 'Camioneta') echo 'selected'; ?>>
                  Camioneta
                </option>

                <option value="Camión"
                  <?php if($vehiculo['Tipo'] == 'Camión') echo 'selected'; ?>>
                  Camión
                </option>

                <option value="Otro"
                  <?php if($vehiculo['Tipo'] == 'Otro') echo 'selected'; ?>>
                  Otro
                </option>

              </select>

            </div>

            <!-- Marca -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Marca
              </label>

              <input type="text"
                     name="marca"
                     class="form-control"
                     value="<?= htmlspecialchars($vehiculo['Marca']) ?>"
                     required>

            </div>

            <!-- Modelo -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Modelo
              </label>

              <input type="text"
                     name="modelo"
                     class="form-control"
                     value="<?= htmlspecialchars($vehiculo['Modelo']) ?>"
                     required>

            </div>

            <!-- Año -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Año
              </label>

              <input type="number"
                     name="anio"
                     class="form-control"
                     min="1950"
                     max="2099"
                     value="<?= htmlspecialchars($vehiculo['Anio']) ?>"
                     required>

            </div>

            <!-- Placas -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Placas
              </label>

              <input type="text"
                     name="placas"
                     class="form-control"
                     value="<?= htmlspecialchars($vehiculo['Placas']) ?>"
                     required>

            </div>

            <!-- Número póliza -->
            <div class="col-md-6 mb-3">

              <label class="form-label">
                Número de póliza
              </label>

              <input type="text"
                     name="numeroPoliza"
                     class="form-control"
                     value="<?= htmlspecialchars($vehiculo['NumeroPoliza']) ?>">

            </div>

            <!-- Número serie -->
            <div class="col-md-12 mb-3">

              <label class="form-label">
                Número de serie (VIN)
              </label>

              <input type="text"
                     name="numeroSerie"
                     class="form-control"
                     value="<?= htmlspecialchars($vehiculo['NumeroSerie']) ?>">

            </div>

          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">

          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal">

            Cancelar

          </button>

          <button type="submit"
                  class="btn btn-warning">

            Guardar cambios

          </button>

        </div>

      </div>

    </form>

  </div>

</div>