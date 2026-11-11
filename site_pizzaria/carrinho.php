<?php
require 'conexao.php';
require 'partes.php';

$tamanhos_validos = ['media', 'grande', 'familia', 'unico'];
$formas_validas   = ['pix', 'cartao', 'dinheiro'];


if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}


function itens_do_carrinho(PDO $pdo): array
{
    $itens = [];
    $st = $pdo->prepare('SELECT nome, emoji, preco, categoria FROM produtos WHERE id = ?');
    foreach ($_SESSION['carrinho'] as $chave => $qtd) {
        [$id, $tam] = explode('|', (string) $chave);
        $st->execute([(int) $id]);
        $p = $st->fetch();
        if (!$p) {
            continue;
        }
        $itens[] = $p + ['chave' => (string) $chave, 'id' => (int) $id, 'tamanho' => $tam, 'qtd' => (int) $qtd];
    }
    return $itens;
}


if (isset($_GET['produto_id'], $_GET['tamanho'])) {
    $id  = (int) $_GET['produto_id'];
    $tam = (string) $_GET['tamanho'];

    $st = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE id = ?');
    $st->execute([$id]);

    if ($st->fetchColumn() > 0 && in_array($tam, $tamanhos_validos, true)) {
        $chave = $id . '|' . $tam;
        $_SESSION['carrinho'][$chave] = ($_SESSION['carrinho'][$chave] ?? 0) + 1;
    }
    header('Location: carrinho.php'); // evita adicionar de novo ao recarregar
    exit;
}


if (isset($_GET['remover'])) {
    unset($_SESSION['carrinho'][(string) $_GET['remover']]);
    header('Location: carrinho.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (($_POST['qtd'] ?? []) as $chave => $q) {
        if (isset($_SESSION['carrinho'][(string) $chave])) {
            $_SESSION['carrinho'][(string) $chave] = max(1, (int) $q);
        }
    }

    $itens     = itens_do_carrinho($pdo);
    $pagamento = $_POST['pagamento'] ?? '';

    if (isset($_POST['finalizar']) && $itens && in_array($pagamento, $formas_validas, true)) {
        $st = $pdo->prepare('SELECT id FROM enderecos WHERE id_usuario = ? AND principal = 1 LIMIT 1');
        $st->execute([ID_USUARIO]);
        $id_endereco = $st->fetchColumn();

        if ($id_endereco) {
            $troco = ($pagamento === 'dinheiro') ? trim($_POST['troco'] ?? '') : '';
            $obs   = trim($_POST['observacoes'] ?? '');

            $pdo->beginTransaction();
            try {
                $pdo->prepare(
                    'INSERT INTO pedidos (id_usuario, id_endereco, data_hora, status, forma_pagamento, troco, observacoes, taxa_entrega)
                     VALUES (?, ?, ?, \'recebido\', ?, ?, ?, ?)'
                )->execute([
                    ID_USUARIO,
                    $id_endereco,
                    date('Y-m-d H:i:s'),
                    $pagamento,
                    $troco !== '' ? $troco : null,
                    $obs !== '' ? $obs : null,
                    TAXA_ENTREGA,
                ]);
                $id_pedido = $pdo->lastInsertId();

                $ins = $pdo->prepare(
                    'INSERT INTO itens_pedido (id_pedido, id_produto, tamanho, quantidade, preco_unitario)
                     VALUES (?, ?, ?, ?, ?)'
                );
                foreach ($itens as $item) {
                    $ins->execute([$id_pedido, $item['id'], $item['tamanho'], $item['qtd'], $item['preco']]);
                }

                $pdo->commit();
                $_SESSION['carrinho'] = [];
                header('Location: pedidos.php');
                exit;
            } catch (Throwable $erro) {
                $pdo->rollBack();
                throw $erro;
            }
        }
    }

    header('Location: carrinho.php');
    exit;
}


$itens = itens_do_carrinho($pdo);

$subtotal = 0;
foreach ($itens as $item) {
    $subtotal += $item['preco'] * $item['qtd'];
}
$total = $subtotal + TAXA_ENTREGA;

$st = $pdo->prepare('SELECT rua, numero, bairro, cidade FROM enderecos WHERE id_usuario = ? AND principal = 1 LIMIT 1');
$st->execute([ID_USUARIO]);
$endereco = $st->fetch();

cabecalho('carrinho.php', 'Carrinho', 'Sabor & Massa - Carrinho de compras e finalização de pedido.');
?>

  <form id="form-carrinho" method="post" action="carrinho.php"></form>

  <main class="conteudo">

    <div class="pagina-titulo">
      <h1>Seu Carrinho</h1>
      <p>Confira os itens selecionados e escolha a forma de pagamento.</p>
    </div>

    <div class="carrinho-layout">

      <div class="carrinho-coluna">

        <section class="cartao entrega-info" aria-label="Endereço de entrega">
          <div>
            <strong>Endereço de entrega</strong>
            <span><?= $endereco ? e(endereco_completo($endereco)) : '' ?></span>
          </div>
          <a href="perfil.php">Alterar</a>
        </section>

        <section class="cartao" aria-labelledby="titulo-itens">
          <h2 class="subtitulo-bloco" id="titulo-itens">Itens do pedido (<?= count($itens) ?>)</h2>

          <ul class="lista-itens">
<?php foreach ($itens as $n => $item): ?>
<?php $bebida = ($item['categoria'] === 'bebidas'); ?>
            <li class="item-carrinho">
              <div class="item-carrinho__foto<?= $bebida ? ' item-carrinho__foto--bebida' : '' ?>" role="img" aria-label="Foto de <?= e($item['nome']) ?>">
                <span aria-hidden="true"><?= e($item['emoji']) ?></span>
              </div>
              <div class="item-carrinho__info">
                <h3 class="item-carrinho__nome"><?= e($item['nome']) ?></h3>
                <p class="item-carrinho__tamanho">Tamanho: <?= e(nome_tamanho($item['tamanho'])) ?></p>
                <a class="item-carrinho__remover" href="carrinho.php?remover=<?= urlencode($item['chave']) ?>">Remover</a>
              </div>
              <div class="item-carrinho__qtd">
                <label class="sr-only" for="qtd-<?= $n + 1 ?>">Quantidade</label>
                <input id="qtd-<?= $n + 1 ?>" type="number" name="qtd[<?= e($item['chave']) ?>]" form="form-carrinho" value="<?= $item['qtd'] ?>" min="1">
              </div>
              <div class="item-carrinho__subtotal">
                <?= reais($item['preco'] * $item['qtd']) ?>
                <small><?= reais($item['preco']) ?> un.</small>
              </div>
            </li>
<?php endforeach; ?>
          </ul>

          <a class="link-continuar" href="index.php">&larr; Continuar comprando no cardápio</a>
        </section>

        <section class="cartao">
          <div class="campo">
            <label for="obs-pedido">Observações do pedido</label>
            <textarea id="obs-pedido" name="observacoes" form="form-carrinho" placeholder="Ex: Por favor, caprichar no orégano e mandar troco para 50 reais se for dinheiro."></textarea>
          </div>
        </section>

      </div>

      <div class="carrinho-coluna resumo-lateral">

        <section class="cartao pagamento">
          <fieldset class="pagamento__opcoes">
            <legend>Forma de Pagamento</legend>

            <label class="opcao-pagamento" for="pg-pix">
              <input type="radio" id="pg-pix" name="pagamento" form="form-carrinho" value="pix" checked>
              <div>
                Pix
                <small>Aprovação imediata</small>
              </div>
            </label>

            <label class="opcao-pagamento" for="pg-cartao">
              <input type="radio" id="pg-cartao" name="pagamento" form="form-carrinho" value="cartao">
              <div>
                Cartão na Entrega
                <small>Débito ou Crédito (Maquininha)</small>
              </div>
            </label>

            <label class="opcao-pagamento" for="pg-dinheiro">
              <input type="radio" id="pg-dinheiro" name="pagamento" form="form-carrinho" value="dinheiro">
              <div>
                Dinheiro
                <small>Pagamento em espécie</small>
              </div>
            </label>
          </fieldset>

          <div class="campo campo-troco">
            <label for="troco">Troco para quanto?</label>
            <input type="text" id="troco" name="troco" form="form-carrinho" placeholder="Ex: R$ 100,00">
          </div>
        </section>

        <section class="cartao">
          <h2 class="subtitulo-bloco">Resumo dos Valores</h2>

          <dl>
            <div class="resumo__linha">
              <dt>Subtotal</dt>
              <dd><?= reais($subtotal) ?></dd>
            </div>
            <div class="resumo__linha">
              <dt>Taxa de entrega</dt>
              <dd><?= reais(TAXA_ENTREGA) ?></dd>
            </div>
            <div class="resumo__linha resumo__total">
              <dt>Total</dt>
              <dd><?= reais($total) ?></dd>
            </div>
          </dl>

          <button type="submit" name="finalizar" value="1" form="form-carrinho" class="btn btn--primario btn--bloco btn--grande" style="margin-top: 1.25rem;">
            Finalizar Pedido
          </button>

          <p class="resumo__nota">Ao finalizar, você poderá acompanhar o status em "Meus Pedidos".</p>
        </section>

      </div>

    </div>

  </main>
<?php rodape(); ?>
