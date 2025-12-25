
(function(){
  const clk = document.getElementById('clock');
  const horaInput = document.getElementById('horaActual');
  if(!clk) return;

  function pad(n){ return n < 10 ? '0'+n : n; }

  function tick(){
    const d = new Date();
    const h = pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds());
    clk.textContent = ' ' + h;
    if(horaInput) horaInput.value = h; // si existe input en entrada/salida, lo actualiza
  }

  setInterval(tick, 1000);
  tick();
})();

(function(){
  const btnE = document.getElementById('btnEntrada');
  if (!btnE) return;

  btnE.addEventListener('click', function(e){
    const ya   = this.dataset.ya === '1';
    const hora = this.dataset.hora || '';
    if (ya) {
      e.preventDefault();
      alert(`Ya registraste tu entrada hoy${hora ? ' ('+hora+')' : ''}.`);
    }
  });
})();

(function(){
  const btnS = document.getElementById('btnSalida');
  if (!btnS) return;

  btnS.addEventListener('click', function(e){
    const ya = this.dataset.ya === '1';
    const hora = this.dataset.hora || '';
    const noEntrada = this.dataset.noentrada === '1';

    if (noEntrada) {
      e.preventDefault();
      alert("No puedes registrar salida sin haber registrado entrada hoy.");
      return;
    }
    if (ya) {
      e.preventDefault();
      alert(` Ya registraste tu salida hoy${hora ? ' ('+hora+')' : ''}.`);
      return;
    }
  });
})();
