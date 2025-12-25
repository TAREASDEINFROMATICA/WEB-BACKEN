
function confirmarEliminacion(mensaje = "¿Estás seguro de eliminar este elemento?") {
  return confirm(mensaje);
}

function toggleElement(id) {
  const element = document.getElementById(id);
  if (element) {
    element.style.display = element.style.display === "none" ? "block" : "none";
  }
}

function formatearDinero(monto) {
  return new Intl.NumberFormat("es-PE", {
    style: "currency",
    currency: "PEN",
  }).format(monto);
}


function inicializarAnimacionesHome() {
  
  const heroTitle = document.querySelector('.hero-title');
  if (heroTitle) {
    heroTitle.style.opacity = '0';
    heroTitle.style.transform = 'translateY(20px)';

    setTimeout(() => {
      heroTitle.style.transition = 'all 0.8s ease';
      heroTitle.style.opacity = '1';
      heroTitle.style.transform = 'translateY(0)';
    }, 300);
  }
}

function inicializarEventosHome() {
  console.log('Eventos del home inicializados');
  const visitas = localStorage.getItem('visitas_home') || 0;
  localStorage.setItem('visitas_home', parseInt(visitas) + 1);
  console.log(`Visitas a la página home: ${parseInt(visitas) + 1}`);
}

function inicializarHome() {
  console.log('Inicializando página home...');

  
  inicializarAnimacionesHome();
  inicializarEventosHome();
}


document.addEventListener("DOMContentLoaded", function () {
  console.log("Sistema de Tienda TV cargado");

  // Confirmación para enlaces de eliminación
  const botonesEliminar = document.querySelectorAll('a[onclick*="confirm"]');
  botonesEliminar.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      if (!confirm("¿Estás seguro de realizar esta acción?")) {
        e.preventDefault();
      }
    });
  });
  if (document.querySelector('.hero-section')) {
    inicializarHome();
  }

  console.log('Funcionalidades generales inicializadas');
});
