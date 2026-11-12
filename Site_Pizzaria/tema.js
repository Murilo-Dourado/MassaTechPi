(function () {
  var caixa = document.getElementById('tema-escuro');
  if (!caixa) return;

  // Restaura a escolha salva
  try {
    caixa.checked = localStorage.getItem('tema') === 'escuro';
  } catch (e) {}

  // Salva sempre que o usuário alternar
  caixa.addEventListener('change', function () {
    try {
      localStorage.setItem('tema', caixa.checked ? 'escuro' : 'claro');
    } catch (e) {}
  });
})();
