
function actualizarVistaPrevia() {
  const select = document.getElementById("imagen_url");
  const vistaPrevia = document.getElementById("vista-previa");
  const imagenUrl = select ? select.value : "";

  if (imagenUrl) {
    vistaPrevia.innerHTML = `<img src="${imagenUrl}" alt="Vista previa" style="max-height: 140px; max-width: 100%;">`;
  } else if (vistaPrevia) {
    vistaPrevia.innerHTML =
      '<small class="text-muted">Selecciona una imagen para ver la vista previa</small>';
  }
}

function actualizarResoluciones() {
  const pulgadasEl = document.getElementById("pulgadas");
  const resolucionSelect = document.getElementById("resolucion");
  if (!pulgadasEl || !resolucionSelect) return;

  const pulgadas = Number(pulgadasEl.value || 0);

  if (pulgadas >= 70) {
    resolucionSelect.innerHTML = `
      <option value="">Seleccionar Resolución</option>
      <option value="4K">4K UHD</option>
      <option value="8K">8K UHD</option>
    `;
  } else if (pulgadas >= 50) {
    resolucionSelect.innerHTML = `
      <option value="">Seleccionar Resolución</option>
      <option value="FULL HD">FULL HD</option>
      <option value="4K">4K UHD</option>
    `;
  } else {
    resolucionSelect.innerHTML = `
      <option value="">Seleccionar Resolución</option>
      <option value="HD">HD</option>
      <option value="FULL HD">FULL HD</option>
    `;
  }
}

function agregarAlCarrito(productoId, cantidad) {
  const formData = new FormData();
  formData.append('producto_id', productoId);
  formData.append('cantidad', cantidad);
  formData.append('ajax', 'true');

  fetch('/tienda_tv/?c=CarritoController&a=agregar', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      if (data.para_localstorage) {
        // Usuario no logueado guardar en localStorage
        carritoStorage.agregarProducto(data.producto);
      } else {
        // logueado
        carritoStorage.actualizarContador();
        carritoStorage.mostrarMensaje(data.message, 'success');
      }
    } else {
      carritoStorage.mostrarMensaje(data.message, 'error');
    }
  })
  .catch(() => {
    carritoStorage.mostrarMensaje('Error al agregar al carrito', 'error');
  });
}

function inicializarIndexProductos() {
  
  const imagenSelect = document.getElementById("imagen_url");
  if (imagenSelect) {
    imagenSelect.addEventListener("change", actualizarVistaPrevia);
    actualizarVistaPrevia();
  }

  const pulgadasInput = document.getElementById("pulgadas");
  if (pulgadasInput) {
    pulgadasInput.addEventListener("input", actualizarResoluciones);
  }

  
  const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');

  botonesAgregar.forEach(boton => {
    boton.addEventListener('click', function(e) {
      e.preventDefault();
      const productoId = this.dataset.productoId;
      const cantidad = 1;
      agregarAlCarrito(productoId, cantidad);
    });
  });

  
}

function inicializarDetalleProductos() {
  // Manejar clic en botón de agregar al carrito en detalle
  const botonAgregarDetalle = document.querySelector('.btn-agregar-carrito-detalle');
  if (botonAgregarDetalle) {
    botonAgregarDetalle.addEventListener('click', function(e) {
      e.preventDefault();
      const productoId = this.dataset.productoId;
      const cantidad = 1;
      agregarAlCarrito(productoId, cantidad);
    });
  }

  
}

document.addEventListener("DOMContentLoaded", function () {
  console.log('Productos.js inicializado');

  // Detectar si estamos en index o detalle
  if (document.querySelector('.btn-agregar-carrito-detalle')) {
    inicializarDetalleProductos();
  } else if (document.querySelector('.btn-agregar-carrito')) {
    inicializarIndexProductos();
  }

  // Siempre inicializar funcionalidades de admin si existen
  const imagenSelect = document.getElementById("imagen_url");
  if (imagenSelect) {
    imagenSelect.addEventListener("change", actualizarVistaPrevia);
    actualizarVistaPrevia();
  }

  const pulgadasInput = document.getElementById("pulgadas");
  if (pulgadasInput) {
    pulgadasInput.addEventListener("input", actualizarResoluciones);
  }
});
