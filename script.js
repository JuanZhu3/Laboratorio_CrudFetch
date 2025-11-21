// crudfetch/script.js — switch-only branching
const frm = document.getElementById('frm');
const registrar = document.getElementById('registrar');
const resultado = document.getElementById('resultado');
const buscar = document.getElementById('buscar');
const idp = document.getElementById('idp');
const codigo = document.getElementById('codigo');
const producto = document.getElementById('producto');
const precio = document.getElementById('precio');
const cantidad = document.getElementById('cantidad');

let SNAP = null;

document.addEventListener('DOMContentLoaded', () => {
  ListarProductos();
});

function ListarProductos(busqueda) {
  fetch("listar.php", { method: "POST", body: busqueda ?? "" })
    .then(r => r.text())
    .then(html => { resultado.innerHTML = html; })
    .catch(() => errorSwal("No se pudo listar"));
}

registrar.addEventListener("click", (e) => {
  e.preventDefault();
  const data = getData();

  switch (true) {
    case isEmpty(data):
    case hasMissing(data):
      frm.classList.add('was-validated');
      return errorSwal("Completa todos los campos requeridos.");
    case !frm.checkValidity():
      frm.classList.add('was-validated');
      return errorSwal("Hay campos inválidos.");
    case data.codigo.length > 64:
      return errorSwal("Código demasiado largo (máx 64).");
    case data.producto.length > 255:
      return errorSwal("Producto demasiado largo (máx 255).");
    case !isFinite(Number(data.precio)) || Number(data.precio) <= 0 || Number(data.precio) > 999999999.99:
      return errorSwal("Precio inválido.");
    case !Number.isInteger(Number(data.cantidad)) || Number(data.cantidad) < 0 || Number(data.cantidad) > 999999999:
      return errorSwal("Cantidad inválida.");
    case !!data.idp && !!SNAP && sameData(data, SNAP):
      return infoSwal("Sin cambios que guardar");
    default:
      fetch("registrar.php", { method: "POST", body: toFD(data) })
        .then(r => r.text())
        .then(resp => {
          const out = resp.trim();
          switch (true) {
            case out.startsWith("error:"):
              return errorSwal(out.replace("error:", "").trim());
            case out === "ok":
              okSwal("Producto creado"); resetForm(); return ListarProductos(buscar.value.trim());
            case out === "modificado":
              okSwal("Producto actualizado"); resetForm(); return ListarProductos(buscar.value.trim());
            case out === "sin_cambios":
              return infoSwal("Sin cambios que guardar");
            default:
              return errorSwal("Respuesta inesperada");
          }
        })
        .catch(() => errorSwal("No se pudo guardar"));
  }
});

function Editar(id) {
  fetch("editar.php", { method: "POST", body: id })
    .then(r => r.json())
    .then(p => {
      switch (true) {
        case !!p && !!p.id:
          idp.value = p.id;
          codigo.value = (p.codigo || "");
          producto.value = (p.producto || "");
          precio.value = (p.precio || "");
          cantidad.value = (p.cantidad || "");
          registrar.value = "Actualizar";
          SNAP = snapshot();
          break;
        default:
          errorSwal("No encontrado");
      }
    })
    .catch(() => errorSwal("No se pudo obtener el producto"));
}

function Eliminar(id) {
  const idNum = parseInt(id, 10);
  switch (true) {
    case !Number.isInteger(idNum) || idNum <= 0:
      return errorSwal("ID inválido");
    default:
      Swal.fire({
        title: '¿Seguro de eliminar?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
      }).then((r) => {
        switch (r.isConfirmed) {
          case false:
            return;
          default:
            const fd = new FormData();
            fd.append('id', String(idNum));
            fetch("eliminar.php", { method: "POST", body: fd })
              .then(r => r.text())
              .then(resp => {
                const out = resp.trim();
                switch (out) {
                  case "ok":
                    okSwal("Eliminado");
                    ListarProductos(buscar.value.trim());
                    break;
                  default:
                    errorSwal(out.startsWith("error:") ? out.replace("error:", "").trim() : "No se pudo eliminar");
                }
              })
              .catch(() => errorSwal("No se pudo eliminar"));
        }
      });
  }
}

buscar.addEventListener("keyup", () => {
  const v = buscar.value.trim();
  switch (v) {
    case "":
      ListarProductos("");
      break;
    default:
      ListarProductos(v);
      break;
  }
});

// Helpers (sin if/else)
function snapshot(){ return { idp:(idp.value||"").trim(), codigo:(codigo.value||"").trim(), producto:(producto.value||"").trim(), precio:(precio.value||"").trim(), cantidad:(cantidad.value||"").trim() }; }
function getData(){ return snapshot(); }
function isEmpty(d){ return d.codigo==="" && d.producto==="" && d.precio==="" && d.cantidad===""; }
function hasMissing(d){ return d.codigo==="" || d.producto==="" || d.precio==="" || d.cantidad===""; }
function sameData(a,b){ return a.codigo===b.codigo && a.producto===b.producto && Number(a.precio)==Number(b.precio) && Number(a.cantidad)==Number(b.cantidad); }
function toFD(d){ const fd=new FormData(); for(const k in d) fd.append(k,d[k]); return fd; }
function resetForm(){ frm.reset(); idp.value=""; registrar.value="Registrar"; SNAP=null; frm.classList.remove('was-validated'); }

// SweetAlerts
function okSwal(text){ Swal.fire({ icon:'success', title:'Listo', text, timer:1300, showConfirmButton:false }); }
function errorSwal(text){ Swal.fire({ icon:'error', title:'Error', text }); }
function infoSwal(text){ Swal.fire({ icon:'info', title:'Información', text, timer:1500, showConfirmButton:false }); }
