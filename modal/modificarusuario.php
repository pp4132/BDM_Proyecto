<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">

    <form action="actualizarUsuario.php" method="POST" enctype="multipart/form-data">
    
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Editar perfil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control"
              value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>
          </div>

          <div class="mb-3">
            <label>Apellidos</label>
            <input type="text" name="apellidos" class="form-control"
              value="<?= htmlspecialchars($usuario['Apellidos']) ?>" required>
          </div>

          <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control"
              value="<?= htmlspecialchars($usuario['Telefono']) ?>">
          </div>

          <div class="mb-3">
            <label>Foto</label>
            <input type="file" name="foto" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            Guardar cambios
          </button>
        </div>

      </div>

    </form>

  </div>
</div>