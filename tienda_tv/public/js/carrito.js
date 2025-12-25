
class CarritoManager {
    constructor() {
        this.baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin + '/tienda_tv';
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.inicializarCarrito();
            this.inicializarAplicacion();
        });
    }
    
    inicializarAplicacion() {
        console.log('Inicializando aplicación...');
        
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });

        if (typeof carritoStorage !== 'undefined') {
            console.log('CarritoStorage encontrado');
            carritoStorage.actualizarContador();
            
            // Sincronizar automáticamente si hay productos en localStorage
            if (document.body.classList.contains('usuario-logueado')) {
                setTimeout(function() {
                    if (carritoStorage.obtenerCarrito().length > 0) {
                        console.log('Sincronizando carrito con BD...');
                        carritoStorage.sincronizarConBD();
                    }
                }, 1000);
            }
        } else {
            console.error('CarritoStorage NO encontrado');
        }
    }
    
    inicializarCarrito() {
        if (ES_CARRITO_TEMPORAL) {
            this.cargarCarritoTemporal();
        }
        
        this.inicializarEventosBD();
    
        this.inicializarEventosTemporal();
    }
    
    inicializarEventosBD() {
        const inputsCantidad = document.querySelectorAll('.quantity-input');
        inputsCantidad.forEach(input => {
            input.addEventListener('change', () => {
                const carritoId = input.dataset.carritoId;
                const cantidad = input.value;
                this.actualizarCantidadBD(carritoId, cantidad);
            });
        });
    }
    
    inicializarEventosTemporal() {
        const btnVaciarTemporal = document.getElementById('vaciar-temporal');
        if (btnVaciarTemporal) {
            btnVaciarTemporal.addEventListener('click', () => {
                if (confirm('¿Vaciar todo el carrito temporal?')) {
                    carritoStorage.limpiarCarrito();
                    this.cargarCarritoTemporal();
                }
            });
        }
    }
    
    cargarCarritoTemporal() {
        const productos = carritoStorage.obtenerProductos();
        const tbody = document.getElementById('carrito-temporal-body');
        const totalElement = document.getElementById('total-temporal');
        const acciones = document.getElementById('acciones-carrito');
        const vacio = document.getElementById('carrito-vacio');
        
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        if (productos.length === 0) {
            document.getElementById('carrito-temporal').style.display = 'none';
            if (vacio) vacio.style.display = 'block';
            if (acciones) acciones.style.display = 'none';
            return;
        }
        
        let total = 0;
        
        productos.forEach(producto => {
            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            
            const row = document.createElement('tr');
            row.innerHTML = this.generarFilaProductoTemporal(producto, subtotal);
            tbody.appendChild(row);
        });
        
        if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
        document.getElementById('carrito-temporal').style.display = 'block';
        if (vacio) vacio.style.display = 'none';
        if (acciones) acciones.style.display = 'flex';
        
        this.agregarEventosCarritoTemporal();
    }
    
    generarFilaProductoTemporal(producto, subtotal) {
        return `
            <td>
                <div class="d-flex align-items-center">
                    ${producto.imagen_url ? 
                        `<img src="${producto.imagen_url}" class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">` :
                        `<div class="bg-light me-3 d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                            <i class="fas fa-tv text-muted"></i>
                        </div>`
                    }
                    <div>
                        <strong>${producto.nombre}</strong><br>
                        <small class="text-muted">Carrito temporal</small>
                    </div>
                </div>
            </td>
            <td>$${producto.precio.toFixed(2)}</td>
            <td>
                <div class="input-group" style="width: 120px;">
                    <input type="number" 
                           class="form-control quantity-input-temporal" 
                           value="${producto.cantidad}" 
                           min="1" 
                           max="${producto.stock_actual}"
                           data-producto-id="${producto.id_producto}">
                </div>
            </td>
            <td><strong>$${subtotal.toFixed(2)}</strong></td>
            <td>
                <button class="btn btn-danger btn-sm btn-eliminar-temporal" 
                        data-producto-id="${producto.id_producto}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    }
    
    agregarEventosCarritoTemporal() {
        const inputsTemporal = document.querySelectorAll('.quantity-input-temporal');
        inputsTemporal.forEach(input => {
            input.addEventListener('change', () => {
                const productoId = input.dataset.productoId;
                const cantidad = parseInt(input.value);
                
                if (cantidad > 0) {
                    const success = carritoStorage.actualizarCantidad(productoId, cantidad);
                    if (success) {
                        this.cargarCarritoTemporal();
                        if (typeof carritoStorage !== 'undefined') {
                            carritoStorage.mostrarMensaje('Cantidad actualizada', 'success');
                        }
                    } else {
                        const producto = carritoStorage.obtenerProductos().find(p => p.id_producto == productoId);
                        if (producto) {
                            input.value = producto.cantidad;
                        }
                        if (typeof carritoStorage !== 'undefined') {
                            carritoStorage.mostrarMensaje('No hay suficiente stock', 'error');
                        }
                    }
                }
            });
        });
        
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-temporal');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', () => {
                const productoId = boton.dataset.productoId;
                
                if (confirm('¿Eliminar este producto del carrito?')) {
                    carritoStorage.eliminarProducto(productoId);
                    this.cargarCarritoTemporal();
                }
            });
        });
    }
    
    actualizarCantidadBD(carritoId, cantidad) {
        const formData = new FormData();
        formData.append('carrito_id', carritoId);
        formData.append('cantidad', cantidad);

        fetch(`${this.baseUrl}/?c=CarritoController&a=actualizar`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof carritoStorage !== 'undefined') {
                    carritoStorage.mostrarMensaje('Cantidad actualizada', 'success');
                }
                setTimeout(() => location.reload(), 1000);
            } else {
                if (typeof carritoStorage !== 'undefined') {
                    carritoStorage.mostrarMensaje('Error al actualizar cantidad', 'error');
                }
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof carritoStorage !== 'undefined') {
                carritoStorage.mostrarMensaje('Error de conexión', 'error');
            }
        });
    }
}

const carritoManager = new CarritoManager();