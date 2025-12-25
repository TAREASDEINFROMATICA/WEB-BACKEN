
document.addEventListener("DOMContentLoaded", function () {
  const t1 = document.getElementById('toggleLoginPass');
  const p1 = document.getElementById('loginPass');
  
  if (t1 && p1) {
    t1.addEventListener('click', () => {
      p1.type = p1.type === 'password' ? 'text' : 'password';
    });
  }
});
