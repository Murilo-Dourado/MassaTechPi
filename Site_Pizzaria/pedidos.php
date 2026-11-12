<?php
require 'conexao.php';
require 'partes.php';


$etapas = [
    'recebido' => 'Pedido recebido',
    'preparo'  => 'Em preparo',
    'saiu'     => 'Saiu para entrega',
    'entregue' => 'Entregue',
];

$formas = ['pix' => 'Pix', 'cartao' => 'Cartão na entrega', 'dinheiro' => 'Dinheiro'];


function data_pedido(string $data_hora): string
{
    $t = strtotime($data_hora);
    $dia = (date('Y-m-d', $t) === date('Y-m-d')) ? 'Hoje' : date('d/m/Y', $t);
    return $dia . ', às ' . date('H:i', $t);
}

$st = $pdo->prepare(
    'SELECT p.id, p.data_hora, p.status, p.forma_pagamento, p.taxa_entrega, e.rua, e.numero
       FROM pedidos p
       JOIN enderecos e ON e.id = p.id_endereco
      WHERE p.id_usuario = ?
      ORDER BY p.data_hora DESC'
);
$st->execute([ID_USUARIO]);
$pedidos = $st->fetchAll();

$st_itens = $pdo->prepare(
    'SELECT i.quantidade, i.tamanho, i.preco_unitario, pr.nome
       FROM itens_pedido i
       JOIN produtos pr ON pr.id = i.id_produto
      WHERE i.id_pedido = ?
      ORDER BY i.id'
);

cabecalho('pedidos.php', 'Meus Pedidos', 'Sabor & Massa - Acompanhe e consulte seus pedidos.');
?>

  <main class="conteudo">

    <div class="pagina-titulo">
      <h1>Meus Pedidos</h1>
      <p>Acompanhe o pedido em andamento e veja seu histórico.</p>
    </div>

    <div class="pedidos">

     
      <fieldset class="pedidos__filtros">
        <legend class="sr-only">Filtrar pedidos por status</legend>

        <label class="filtro">
          <input class="sr-only" type="radio" name="filtro" id="filtro-todos" checked>
          Todos
        </label>
        <label class="filtro">
          <input class="sr-only" type="radio" name="filtro" id="filtro-andamento">
          Em andamento
        </label>
        <label class="filtro">
          <input class="sr-only" type="radio" name="filtro" id="filtro-entregue">
          Entregues
        </label>
        <label class="filtro">
          <input class="sr-only" type="radio" name="filtro" id="filtro-cancelado">
          Cancelados
        </label>
      </fieldset>

      <div class="pedidos__lista">
<?php foreach ($pedidos as $pedido): ?>
<?php
    $st_itens->execute([$pedido['id']]);
    $itens = $st_itens->fetchAll();

    $subtotal = 0;
    foreach ($itens as $i) {
        $subtotal += $i['preco_unitario'] * $i['quantidade'];
    }
    $total = $subtotal + $pedido['taxa_entrega'];

    $status = $pedido['status'];
    
    $grupo = ($status === 'entregue' || $status === 'cancelado') ? $status : 'andamento';
    $texto_selo = ($status === 'entregue') ? 'Entregue' : (($status === 'cancelado') ? 'Cancelado' : $etapas[$status]);
?>

        <article class="cartao pedido" data-status="<?= $grupo ?>" aria-labelledby="pedido-<?= (int) $pedido['id'] ?>">
          <header class="pedido__cabecalho">
            <h2 class="pedido__numero" id="pedido-<?= (int) $pedido['id'] ?>">
              Pedido #<?= (int) $pedido['id'] ?>
              <span class="pedido__data"><?= e(data_pedido($pedido['data_hora'])) ?></span>
            </h2>
            <span class="selo-status selo-status--<?= $grupo ?>"><?= e($texto_selo) ?></span>
          </header>

<?php if ($grupo === 'andamento'): ?>
          <ol class="etapas" aria-label="Etapas do pedido">
<?php $passou = true; ?>
<?php foreach ($etapas as $chave => $rotulo): ?>
<?php if ($chave === $status): $passou = false; ?>
            <li class="etapa--atual" aria-current="step"><?= $rotulo ?></li>
<?php elseif ($passou): ?>
            <li class="etapa--ok"><?= $rotulo ?></li>
<?php else: ?>
            <li><?= $rotulo ?></li>
<?php endif; ?>
<?php endforeach; ?>
          </ol>
<?php endif; ?>

<?php if ($status === 'cancelado'): ?>
          <p class="pedido__aviso">Este pedido foi cancelado antes do preparo e nenhum valor foi cobrado.</p>

<?php endif; ?>
          <div class="pedido__corpo">
            <ul class="pedido__itens">
<?php foreach ($itens as $i): ?>
              <li>
                <span><?= (int) $i['quantidade'] ?>x <?= e($i['nome']) ?><small>Tamanho: <?= e(nome_tamanho($i['tamanho'])) ?></small></span>
                <span><?= reais($i['preco_unitario'] * $i['quantidade']) ?></span>
              </li>
<?php endforeach; ?>
            </ul>

            <dl class="pedido__detalhes">
              <div><dt>Pagamento</dt><dd><?= e($formas[$pedido['forma_pagamento']]) ?></dd></div>
              <div><dt>Entrega</dt><dd><?= e($pedido['rua'] . ', ' . $pedido['numero']) ?></dd></div>
              <div><dt>Taxa de entrega</dt><dd><?= reais($pedido['taxa_entrega']) ?></dd></div>
              <div class="pedido__total"><dt>Total</dt><dd><?= reais($total) ?></dd></div>
            </dl>
          </div>

          <div class="pedido__acoes">
<?php if ($grupo === 'andamento'): ?>
            <a class="btn btn--primario btn--pequeno" href="#">Acompanhar entrega</a>
            <a class="btn btn--neutro btn--pequeno" href="#">Preciso de ajuda</a>
<?php elseif ($grupo === 'entregue'): ?>
            <a class="btn btn--secundario btn--pequeno" href="index.php">Pedir novamente</a>
            <a class="btn btn--neutro btn--pequeno" href="#">Preciso de ajuda</a>
<?php else: ?>
            <a class="btn btn--secundario btn--pequeno" href="index.php">Pedir novamente</a>
<?php endif; ?>
          </div>
        </article>
<?php endforeach; ?>

      </div>
    </div>

  </main>
<?php rodape(); ?>
