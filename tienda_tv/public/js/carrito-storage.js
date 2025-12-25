class CarritoStorage {
    constructor() {
        this.key = 'carrito_temporal';
        this.carrito = this.obtenerCarrito();
        this.inicializar();
    }

    inicializar() {
        this.actualizarContador();
    }

    obtenerCarrito() {
        const carrito = localStorage.getItem(this.key);
        return carrito ? JSON.parse(carrito) : [];
    }

    guardarCarrito() {
        localStorage.setItem(this.key, JSON.stringify(this.carrito));
        this.actualizarContador();
    }

    agregarProducto(producto) {
        const productoExistente = this.carrito.find(item => item.id_producto == producto.id_producto);
        
        if (productoExistente) {
            // Verificar stock antes de sumar
            const nuevaCantidad = productoExistente.cantidad + (producto.cantidad || 1);
            if (nuevaCantidad > productoExistente.stock_actual) {
                this.mostrarMensaje('No hay suficiente stock disponible', 'error');
                return false;
            }
            productoExistente.cantidad = nuevaCantidad;
        } else {
            // Verificar stock para nuevo producto
            if ((producto.cantidad || 1) > producto.stock_actual) {
                this.mostrarMensaje('No hay suficiente stock disponible', 'error');
                return false;
            }
            this.carrito.push({
                id_producto: producto.id_producto,
                nombre: producto.nombre,
                precio: producto.precio,
                cantidad: producto.cantidad || 1,
                imagen_url: producto.imagen_url,
                stock_actual: producto.stock_actual
            });
        }
        
        this.guardarCarrito();
        this.mostrarMensaje('Producto agregado al carrito', 'success');
        return true;
    }

    eliminarProducto(idProducto) {
        this.carrito = this.carrito.filter(item => item.id_producto != idProducto);
        this.guardarCarrito();
        this.mostrarMensaje('Producto eliminado del carrito', 'success');
    }

    actualizarCantidad(idProducto, cantidad) {
        const producto = this.carrito.find(item => item.id_producto == idProducto);
        if (producto && cantidad > 0 && cantidad <= producto.stock_actual) {
            producto.cantidad = cantidad;
            this.guardarCarrito();
            return true;
        }
        return false;
    }

    obtenerProductos() {
        return this.carrito;
    }

    limpiarCarrito() {
        this.carrito = [];
        localStorage.removeItem(this.key);
        this.actualizarContador();
    }

    obtenerTotalProductos() {
        return this.carrito.reduce((total, item) => total + item.cantidad, 0);
    }

    obtenerTotalPrecio() {
        return this.carrito.reduce((total, item) => total + (item.precio * item.cantidad), 0);
    }

    actualizarContador() {
        const totalItems = this.obtenerTotalProductos();
        const contador = document.getElementById('carrito-contador');
        
        if (contador) {
            if (totalItems > 0) {
                contador.textContent = totalItems;
                contador.style.display = 'inline';
            } else {
                contador.style.display = 'none';
            }
        }
    }

    mostrarMensaje(mensaje, tipo = 'info') {
        // Crear toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        toast.style.cssText = `
            position: fixed; top: 80px; right: 20px; z-index: 10000;
            min-width: 300px; animation: slideIn 0.3s ease-out;
        `;
        toast.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }

    async sincronizarConBD() {
        if (this.carrito.length === 0) return;

        try {
            const response = await fetch('/tienda_tv/?c=CarritoController&a=sincronizar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ productos: this.carrito })
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    this.limpiarCarrito();
                    this.mostrarMensaje('Carrito sincronizado correctamente', 'success');
                    // Recargar después de 1 segundo para mostrar carrito actualizado
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.mostrarMensaje('Error al sincronizar carrito: ' + (result.errores?.join(', ') || 'Error desconocido'), 'error');
                }
            }
        } catch (error) {
            console.error('Error sincronizando carrito:', error);
            this.mostrarMensaje('Error de conexión al sincronizar carrito', 'error');
        }
    }
}

const carritoStorage = new CarritoStorage();

// Sincronizar automáticamente si el usuario está logueado
document.addEventListener('DOMContentLoaded', function() {
    if (document.body.classList.contains('usuario-logueado')) {
        
        setTimeout(() => {
            if (carritoStorage.obtenerCarrito().length > 0) {
                carritoStorage.sincronizarConBD();
            }
        }, 100);
    }
});