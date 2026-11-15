const params = new URLSearchParams(window.location.search);
function getErrores() {
  if (!params.has("error")) return [];

  return decodeURIComponent(params.get("error")).split("|");
}

function mostrarToast(mensaje) {
  const container = document.getElementById("toastContainer");

  const toastHTML = `
    <div class="toast align-items-center text-bg-danger border-0 mb-2" role="alert">
      <div class="d-flex">
        <div class="toast-body">
          ${mensaje}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  `;

  container.insertAdjacentHTML("beforeend", toastHTML);

  const toastEl = container.lastElementChild;
  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}

// Mostrar todos los errores
document.getElementById("formRegistro").addEventListener("submit", function(e) {
  const password = document.querySelector('input[name="password"]').value;
  let errores = [];

  if (password.length < 8) errores.push("Mínimo 8 caracteres");
  if (!/[a-z]/.test(password)) errores.push("Falta minúscula");
  if (!/[A-Z]/.test(password)) errores.push("Falta mayúscula");
  if (!/\d/.test(password)) errores.push("Falta número");
  if (!/[\W_]/.test(password)) errores.push("Falta carácter especial");

  if (errores.length > 0) {
    e.preventDefault(); // Evita que se envíe el formulario
    errores.forEach(err => mostrarToast(err));
  }
});

//Persistencia de datos aunque falle
if (params.has("data")) {
  const datos = JSON.parse(decodeURIComponent(params.get("data")));

  for (const key in datos) {
    const input = document.querySelector(`[name="${key}"]`);
    if (input && input.type !== "file") {
      input.value = datos[key];
    }
  }
}

//validaciones de los demas campos obligatorios
// ERRORES
// SOLO usar modal si viene del servidor
if (params.has("error")) {
  const errores = decodeURIComponent(params.get("error")).split("|");

  let lista = "<ul>";
  errores.forEach(e => lista += `<li>${e}</li>`);
  lista += "</ul>";

  document.getElementById("modalTexto").innerHTML = lista;

  const modal = new bootstrap.Modal(document.getElementById("modalMensaje"));
  modal.show();
}

// ÉXITO
if (params.has("success")) {
  const header = document.querySelector(".modal-header");

  header.classList.remove("bg-danger");
  header.classList.add("bg-success");

  document.querySelector(".modal-title").innerText = "Registro exitoso";
  document.getElementById("modalTexto").innerHTML = "Asegurado registrado correctamente";

  const modal = new bootstrap.Modal(document.getElementById("modalMensaje"));
  modal.show();
}