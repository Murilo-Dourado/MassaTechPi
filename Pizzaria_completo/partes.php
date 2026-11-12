<?php


function cabecalho(string $pagina, string $titulo, string $descricao): void
{
    $menu = [
        'index.php'    => 'Início',
        'carrinho.php' => 'Carrinho',
        'pedidos.php'  => 'Meus Pedidos',
        'perfil.php'   => 'Perfil',
    ];
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= $descricao ?>">
  <title>Sabor &amp; Massa | <?= e($titulo) ?></title>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">


  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="cabecalho">
    <div class="cabecalho__conteudo">
      <a class="logo" href="index.php">🍕 Sabor &amp; Massa</a>

      <nav aria-label="Menu principal">
        <ul class="menu__lista">
<?php foreach ($menu as $arquivo => $rotulo): ?>
<?php if ($arquivo === $pagina): ?>
          <li><a class="menu__link ativo" href="<?= $arquivo ?>" aria-current="page"><?= $rotulo ?></a></li>
<?php else: ?>
          <li><a class="menu__link" href="<?= $arquivo ?>"><?= $rotulo ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
          <li class="menu__tema">
            <input type="checkbox" id="tema-escuro" class="tema__input sr-only">
            <label class="tema__botao" for="tema-escuro" title="Alternar tema claro/escuro">
              <span class="sr-only">Alternar tema escuro</span>
              <span aria-hidden="true">☀️</span>
              <span aria-hidden="true">🌙</span>
            </label>
          </li>
        </ul>
      </nav>
    </div>
  </header>
<?php
}

function rodape(): void
{
    ?>

  <footer class="rodape">
    <div class="rodape__conteudo">
      <div>
        <p class="rodape__marca">🍕 Sabor &amp; Massa</p>
        <p>Pizzas artesanais com entrega rápida. Massa fresca todos os dias.</p>
      </div>
      <div>
        <h3>Atendimento</h3>
        <ul>
          <li>Ter a dom, das 18h às 23h30</li>
          <li>WhatsApp: (19) 99999-0000</li>
        </ul>
      </div>
      <div>
        <h3>Navegue</h3>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="carrinho.php">Carrinho</a></li>
          <li><a href="pedidos.php">Meus Pedidos</a></li>
          <li><a href="perfil.php">Perfil</a></li>
        </ul>
      </div>
    </div>
    <p class="rodape__direitos">&copy; 2026 Sabor &amp; Massa. Projeto acadêmico.</p>
  </footer>

</body>
</html>
<?php
}
