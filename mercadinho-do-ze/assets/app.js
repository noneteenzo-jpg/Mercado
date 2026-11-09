// Abre e fecha o menu lateral em telas menores.
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
if (menuToggle && sidebar) {
  menuToggle.addEventListener('click', () => sidebar.classList.toggle('sidebar-open'));
}
// Fecha automaticamente os avisos depois de alguns segundos.
document.querySelectorAll('.alert:not(.alert-error):not(.alert-warning)').forEach((alerta) => {
  window.setTimeout(() => { alerta.classList.add('fade-out'); }, 4500);
});
