
document.addEventListener("DOMContentLoaded", function () {
  const t2 = document.getElementById('toggleRegPass');
  const p2 = document.getElementById('regPass');

  if (t2 && p2) {
    t2.addEventListener('click', () => {
      p2.type = p2.type === 'password' ? 'text' : 'password';
    });
  }
});
