<?php
require 'conexao.php';
require 'partes.php';


$secoes = [
    'mais-pedidos' => [
        'id_titulo' => 'titulo-mais-pedidos',
        'titulo'    => '♥ Mais pedidos',
        'subtitulo' => 'Os favoritos de quem já pediu com a gente.',
    ],
    'pizzas-salgadas' => [
        'id_titulo' => 'titulo-salgadas',
        'titulo'    => '🍕 Pizzas Salgadas',
        'subtitulo' => 'Massa artesanal de fermentação lenta, assada em forno a lenha.',
    ],
    'bebidas' => [
        'id_titulo' => 'titulo-bebidas',
        'titulo'    => '🥤 Bebidas',
        'subtitulo' => 'Geladinhas, direto da geladeira para a sua casa.',
    ],
];


$produtos = $pdo
    ->query('SELECT categoria, id, nome, descricao, preco, emoji FROM produtos ORDER BY id')
    ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);

cabecalho('index.php', 'Cardápio', 'Sabor & Massa - Peça sua pizza favorita com entrega rápida.');
?>


  <nav class="categorias" aria-label="Categorias do cardápio">
    <ul class="categorias__lista">
      <li><a class="categorias__link ativo" href="#mais-pedidos"><span aria-hidden="true">♥</span> Mais pedidos</a></li>
      <li><a class="categorias__link" href="#pizzas-salgadas"><span aria-hidden="true">🍕</span> Pizzas Salgadas</a></li>
      <li><a class="categorias__link" href="#pizzas-doces"><span aria-hidden="true">🍫</span> Pizzas Doces</a></li>
      <li><a class="categorias__link" href="#bebidas"><span aria-hidden="true">🥤</span> Bebidas</a></li>
    </ul>
  </nav>

  <main class="conteudo">


    <section class="banner" aria-labelledby="banner-titulo">
      <span class="banner__selo">Oferta do dia</span>
      <h1 class="banner__titulo" id="banner-titulo">Duas pizzas grandes por R$ 89,90</h1>
      <p class="banner__texto">
        Escolha dois sabores salgados e ganhe uma Coca-Cola 2L. Válido para pedidos feitos hoje, até as 23h.
      </p>
      <a class="banner__cta" href="#pizzas-salgadas">Ver pizzas salgadas</a>
    </section>

<?php foreach ($secoes as $categoria => $secao): ?>
    <!-- SEÇÃO: <?= e($secao['titulo']) ?> -->
    <section class="secao" id="<?= $categoria ?>" aria-labelledby="<?= $secao['id_titulo'] ?>">
      <h2 class="secao__titulo" id="<?= $secao['id_titulo'] ?>"><?= $secao['titulo'] ?></h2>
      <p class="secao__subtitulo"><?= $secao['subtitulo'] ?></p>

      <div class="produtos-grid">
<?php foreach ($produtos[$categoria] ?? [] as $p): ?>
<?php $bebida = ($categoria === 'bebidas'); ?>
        <article class="produto-card">
          <div class="produto-card__foto<?= $bebida ? ' produto-card__foto--bebida' : '' ?>" role="img" aria-label="Foto ilustrativa de <?= e($p['nome']) ?>">
            <span aria-hidden="true"><?= e($p['emoji']) ?></span>
<?php if ($categoria === 'mais-pedidos'): ?>
            <span class="produto-card__selo">Mais pedida</span>
<?php endif; ?>
          </div>
          <div class="produto-card__corpo">
            <h3 class="produto-card__nome"><?= e($p['nome']) ?></h3>
            <p class="produto-card__descricao"><?= e($p['descricao']) ?></p>

            <form class="produto-card__form" action="carrinho.php" method="get">
              <input type="hidden" name="produto_id" value="<?= (int) $p['id'] ?>">
<?php if ($bebida): ?>
              <input type="hidden" name="tamanho" value="unico">
              <p class="produto-card__preco"><?= reais($p['preco']) ?></p>
<?php else: ?>
              <div class="produto-card__linha">
                <p class="produto-card__preco"><small>a partir de</small><?= reais($p['preco']) ?></p>
                <label class="sr-only" for="tam-<?= (int) $p['id'] ?>">Tamanho</label>
                <select class="select-compacto" id="tam-<?= (int) $p['id'] ?>" name="tamanho">
                  <option value="media">Média</option>
                  <option value="grande">Grande</option>
                  <option value="familia">Família</option>
                </select>
              </div>
<?php endif; ?>
              <button type="submit" class="btn btn--primario btn--bloco">Adicionar ao carrinho</button>
            </form>
          </div>
        </article>
<?php endforeach; ?>
      </div>
    </section>

<?php endforeach; ?>
  </main>
<?php rodape(); ?>
