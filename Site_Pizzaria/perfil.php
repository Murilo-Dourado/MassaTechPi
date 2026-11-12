<?php
require 'conexao.php';
require 'partes.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'dados') {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($nome !== '' && $email !== '' && $telefone !== '') {
            $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?')
                ->execute([$nome, $email, $telefone, ID_USUARIO]);
        }
    }

    if ($acao === 'principal') {
       
        $pdo->prepare('UPDATE enderecos SET principal = (id = ?) WHERE id_usuario = ?')
            ->execute([(int) ($_POST['endereco'] ?? 0), ID_USUARIO]);
    }

    if ($acao === 'novo_endereco') {
        $apelido     = trim($_POST['apelido'] ?? '');
        $cep         = trim($_POST['cep'] ?? '');
        $numero      = trim($_POST['numero'] ?? '');
        $rua         = trim($_POST['rua'] ?? '');
        $bairro      = trim($_POST['bairro'] ?? '');
        $cidade      = trim($_POST['cidade'] ?? '');
        $complemento = trim($_POST['complemento'] ?? '');

        if ($cep !== '' && $numero !== '' && $rua !== '' && $bairro !== '' && $cidade !== '') {
           
            $st = $pdo->prepare('SELECT COUNT(*) FROM enderecos WHERE id_usuario = ?');
            $st->execute([ID_USUARIO]);
            $principal = ($st->fetchColumn() == 0) ? 1 : 0;

            $pdo->prepare(
                'INSERT INTO enderecos (id_usuario, apelido, cep, numero, rua, bairro, cidade, complemento, principal)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([
                ID_USUARIO,
                $apelido !== '' ? $apelido : null,
                $cep,
                $numero,
                $rua,
                $bairro,
                $cidade,
                $complemento !== '' ? $complemento : null,
                $principal,
            ]);
        }
    }

    if ($acao === 'preferencias') {
        $pdo->prepare('UPDATE usuarios SET pref_status = ?, pref_promo = ? WHERE id = ?')
            ->execute([isset($_POST['pref_status']) ? 1 : 0, isset($_POST['pref_promo']) ? 1 : 0, ID_USUARIO]);
    }

    header('Location: perfil.php'); 
    exit;
}


$st = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
$st->execute([ID_USUARIO]);
$u = $st->fetch();

$st = $pdo->prepare('SELECT * FROM enderecos WHERE id_usuario = ? ORDER BY id');
$st->execute([ID_USUARIO]);
$enderecos = $st->fetchAll();

$st = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE id_usuario = ?');
$st->execute([ID_USUARIO]);
$qtd_pedidos = (int) $st->fetchColumn();

$cliente_desde = date('Y', strtotime($u['criado_em']));


$partes = preg_split('/\s+/', trim($u['nome']));
$iniciais = mb_strtoupper(mb_substr($partes[0], 0, 1));
if (count($partes) > 1) {
    $iniciais .= mb_strtoupper(mb_substr(end($partes), 0, 1));
}

cabecalho('perfil.php', 'Perfil', 'Sabor & Massa - Gerencie seus dados, endereços e preferências.');
?>
<li class="menu__tema">
            <input type="checkbox" id="tema-escuro" class="tema__input sr-only">
            <label class="tema__botao" for="tema-escuro" title="Alternar tema claro/escuro">
              <span class="sr-only">Alternar tema escuro</span>
              <span aria-hidden="true">☀️</span>
              <span aria-hidden="true">🌙</span>
            </label>
          </li>
  <main class="conteudo">

    <div class="pagina-titulo">
      <h1>Meu Perfil</h1>
      <p>Mantenha seus dados e endereços atualizados para receber seus pedidos sem erro.</p>
    </div>

    <div class="perfil-layout">

      
      <div class="perfil-coluna">

        <section class="cartao" aria-labelledby="titulo-dados">
          <h2 class="subtitulo-bloco" id="titulo-dados">Dados pessoais</h2>

          <form action="perfil.php" method="post">
            <div class="form-grid">
              <div class="campo campo--cheio">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" value="<?= e($u['nome']) ?>" autocomplete="name" required>
              </div>

              <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= e($u['email']) ?>" autocomplete="email" required>
              </div>

              <div class="campo">
                <label for="telefone">Telefone / WhatsApp</label>
                <input type="tel" id="telefone" name="telefone" value="<?= e($u['telefone']) ?>" autocomplete="tel" required>
                <small>Usamos este número para avisar sobre a entrega.</small>
              </div>
            </div>

            <div class="form-acoes">
              <button type="submit" name="acao" value="dados" class="btn btn--primario">Salvar alterações</button>
            </div>
          </form>
        </section>

        <section class="cartao" id="enderecos" aria-labelledby="titulo-enderecos">
          <h2 class="subtitulo-bloco" id="titulo-enderecos">Endereços de entrega</h2>

          <form action="perfil.php" method="post">
            <fieldset class="enderecos-lista">
              <legend class="sr-only">Endereço principal</legend>

<?php foreach ($enderecos as $end): ?>
              <label class="opcao-pagamento" for="end-<?= (int) $end['id'] ?>">
                <input type="radio" id="end-<?= (int) $end['id'] ?>" name="endereco" value="<?= (int) $end['id'] ?>"<?= $end['principal'] ? ' checked' : '' ?>>
                <div>
                  <?= e($end['apelido']) ?>

                  <small><?= e(endereco_completo($end)) ?></small>
                </div>
                <a class="endereco__acoes" href="#">Editar</a>
              </label>

<?php endforeach; ?>
            </fieldset>

            <div class="form-acoes">
              <button type="submit" name="acao" value="principal" class="btn btn--primario">Definir como principal</button>
            </div>
          </form>

          
          <details class="novo-endereco">
            <summary>+ Adicionar novo endereço</summary>

            <form action="perfil.php" method="post">
              <div class="form-grid">
                <div class="campo campo--cheio">
                  <label for="apelido">Nome do endereço</label>
                  <input type="text" id="apelido" name="apelido" placeholder="Ex: Casa da vó">
                </div>

                <div class="campo">
                  <label for="cep">CEP</label>
                  <input type="text" id="cep" name="cep" inputmode="numeric" placeholder="00000-000" autocomplete="postal-code" required>
                </div>

                <div class="campo">
                  <label for="numero">Número</label>
                  <input type="text" id="numero" name="numero" placeholder="Ex: 123" required>
                </div>

                <div class="campo campo--cheio">
                  <label for="rua">Rua</label>
                  <input type="text" id="rua" name="rua" autocomplete="address-line1" required>
                </div>

                <div class="campo">
                  <label for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" required>
                </div>

                <div class="campo">
                  <label for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" value="Santa Bárbara d'Oeste" required>
                </div>

                <div class="campo campo--cheio">
                  <label for="complemento">Complemento</label>
                  <input type="text" id="complemento" name="complemento" placeholder="Apto, bloco, ponto de referência">
                </div>
              </div>

              <div class="form-acoes">
                <button type="submit" name="acao" value="novo_endereco" class="btn btn--primario">Salvar endereço</button>
              </div>
            </form>
          </details>
        </section>

      </div>

      
      <div class="perfil-coluna perfil-lateral">

        <section class="cartao perfil-usuario" aria-label="Resumo da conta">
          <div class="avatar" role="img" aria-label="Iniciais de <?= e($u['nome']) ?>"><?= e($iniciais) ?></div>
          <h2><?= e($u['nome']) ?></h2>
          <p><?= e($u['email']) ?></p>

          <div class="perfil-estatisticas">
            <div>
              <strong><?= $qtd_pedidos ?></strong>
              <span>Pedidos feitos</span>
            </div>
            <div>
              <strong><?= e($cliente_desde) ?></strong>
              <span>Cliente desde</span>
            </div>
          </div>
        </section>

        <section class="cartao" aria-labelledby="titulo-preferencias">
          <h2 class="subtitulo-bloco" id="titulo-preferencias">Preferências</h2>

          <form action="perfil.php" method="post">
            <label class="opcao-check" for="pref-status">
              <input type="checkbox" id="pref-status" name="pref_status"<?= $u['pref_status'] ? ' checked' : '' ?>>
              <div>
                Avisos do pedido
                <small>Receba o status do pedido por WhatsApp.</small>
              </div>
            </label>

            <label class="opcao-check" for="pref-promo">
              <input type="checkbox" id="pref-promo" name="pref_promo"<?= $u['pref_promo'] ? ' checked' : '' ?>>
              <div>
                Ofertas e promoções
                <small>Receba as ofertas do dia por e-mail.</small>
              </div>
            </label>

            <div class="form-acoes">
              <button type="submit" name="acao" value="preferencias" class="btn btn--secundario btn--bloco">Salvar preferências</button>
            </div>
          </form>
        </section>

        <section class="cartao" aria-labelledby="titulo-conta">
          <h2 class="subtitulo-bloco" id="titulo-conta">Conta</h2>

          <div class="conta-acoes">
            <a class="btn btn--neutro btn--bloco" href="#">Alterar senha</a>
            <a class="btn btn--neutro btn--bloco" href="#">Sair da conta</a>
          </div>
        </section>

      </div>

    </div>

  </main>
<?php rodape(); ?>