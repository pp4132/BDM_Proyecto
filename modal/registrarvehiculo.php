<div class="modal fade" id="modalVehiculo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="registraVehiculo.php" method="POST">

      <div class="modal-content">
        
        <!-- HEADER -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            Registrar Vehículo
          </h5>

          <button type="button"
                  class="btn-close btn-close-white"
                  data-bs-dismiss="modal">
          </button>
        </div>

        <!-- BODY -->
        <div class="modal-body">

          <div class="row">

            <!-- Tipo -->
            <div class="col-md-6 mb-3">
              <label class="form-label">
                Tipo de vehículo
              </label>

              <select name="tipo"
                      class="form-select"
                      required>

                <option value="">Selecciona</option>
                <option value="Auto">Auto</option>
                <option value="Moto">Moto</option>
                <option value="Camioneta">Camioneta</option>
                <option value="Camión">Camión</option>
                <option value="Otro">Otro</option>

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
                     maxlength="50"
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
                     maxlength="50"
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
                     maxlength="20"
                     required>
            </div>

            <!-- Número de póliza -->
            <div class="col-md-6 mb-3">
              <label class="form-label">
                Número de póliza
              </label>

              <input type="text"
                     name="numeroPoliza"
                     class="form-control"
                     maxlength="50">
            </div>

            <!-- Número de serie -->
            <div class="col-md-12 mb-3">
              <label class="form-label">
                Número de serie (VIN)
              </label>

              <input type="text"
                     name="numeroSerie"
                     class="form-control"
                     maxlength="100">
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
                  class="btn btn-success">
            Registrar Vehículo
          </button>

        </div>
      </div>
    </form>

  </div>
</div>