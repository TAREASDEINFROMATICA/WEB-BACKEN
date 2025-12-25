// GESTIÓN DE INVENTARIO - FUNCIONES UNIFICADAS
class InventarioManager {
    constructor() {
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.inicializarPreviewInventario();
        });
    }
    
    inicializarPreviewInventario() {
        const tipoSelect = document.getElementById('tipo');
        const cantidadInput = document.getElementById('cantidad');
        const preview = document.getElementById('preview');
        
        if (!preview) return;
        
        // Usar la función más completa para el preview
        const actualizarPreview = () => {
            this.actualizarPreviewInventario();
        };
        
        if (tipoSelect) {
            tipoSelect.addEventListener('change', actualizarPreview);
        }
        
        if (cantidadInput) {
            cantidadInput.addEventListener('input', actualizarPreview);
        }
        
        // Preview inicial
        actualizarPreview();
    }
    
    actualizarPreviewInventario() {
        const tipo = document.getElementById('tipo').value;
        const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
        const preview = document.getElementById('preview');
        
        if (!preview) return;
        
        // Obtener stock actual de diferentes formas
        let stockActual;
        
        // Intentar obtener del atributo data-stock-actual
        const stockData = preview.getAttribute('data-stock-actual');
        if (stockData) {
            stockActual = parseInt(stockData) || 0;
        } 
        // Intentar obtener del input stock_actual
        else {
            const stockInput = document.getElementById('stock_actual');
            stockActual = stockInput ? parseInt(stockInput.value) || 0 : 0;
        }
        
        let nuevoStock;
        let html = '';

        if (tipo === 'agregar') {
            nuevoStock = stockActual + cantidad;
            html = `
                <div class="alert alert-info">
                    <strong>Resumen del ajuste:</strong><br>
                    Stock actual: <strong>${stockActual}</strong> unidades<br>
                    + ${cantidad} unidades a agregar<br>
                    Nuevo stock: <strong class="text-success">${nuevoStock}</strong> unidades
                </div>
            `;
        } else {
            nuevoStock = Math.max(0, stockActual - cantidad);
            const alertClass = nuevoStock <= 5 ? 'alert-danger' : 'alert-warning';
            html = `
                <div class="alert ${alertClass}">
                    <strong>Resumen del ajuste:</strong><br>
                    Stock actual: <strong>${stockActual}</strong> unidades<br>
                    - ${cantidad} unidades a retirar<br>
                    Nuevo stock: <strong class="${nuevoStock <= 5 ? 'text-danger' : 'text-warning'}">${nuevoStock}</strong> unidades
                    ${nuevoStock <= 5 ? '<br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> ¡Alerta! Stock bajo</small>' : ''}
                </div>
            `;
        }
        
        preview.innerHTML = html;
    }
    
    // Función alternativa simple (compatibilidad)
    actualizarPreviewSimple() {
        const tipo = document.getElementById('tipo').value;
        const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
        const preview = document.getElementById('preview');
        
        if (!preview) return;
        
        const stockActual = parseInt(preview.getAttribute('data-stock-actual') || 0);
        let nuevoStock;

        if (tipo === 'agregar') {
            nuevoStock = stockActual + cantidad;
        } else {
            nuevoStock = Math.max(0, stockActual - cantidad);
        }

        preview.innerHTML = `Stock actual: <strong>${stockActual}</strong> unidades<br>
                        Nuevo stock: <strong>${nuevoStock}</strong> unidades`;
    }
}

// Inicializar el manager de inventario
const inventarioManager = new InventarioManager();

// Funciones globales para compatibilidad (opcional)
function actualizarPreviewInventario() {
    inventarioManager.actualizarPreviewInventario();
}