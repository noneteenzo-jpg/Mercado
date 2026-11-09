<?php
require_once __DIR__ . '/../config/database.php';
$tituloPagina = 'Cadastro de produto';
$base = '../';
$id = (int)($_GET['id'] ?? 0);
$erros = [];
$p = ['codigo' => '', 'nome' => '', 'descricao' => '', 'categoria_id' => '', 'preco' => '', 'estoque' => 0, 'estoque_minimo' => 0, 'validade' => ''];
if ($id) {
    $st = $pdo->prepare('SELECT * FROM produtos WHERE id=?');
    $st->execute([$id]);
    $atual = $st->fetch();
    if (!$atual) {
        flash('error', 'Produto não encontrado.');
        header('Location: index.php');
        exit;
    }
    $p = $atual;
    $tituloPagina = 'Editar produto';
}
$categorias = $pdo->query('SELECT id,nome FROM categorias ORDER BY nome')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    foreach ($p as $campo => $valor) {
        $p[$campo] = trim((string)($_POST[$campo] ?? ''));
    }
    if ($p['codigo'] === '') $erros[] = 'Informe o código do produto.';
    if ($p['nome'] === '') $erros[] = 'Informe o nome do produto.';
    if (!filter_var($p['categoria_id'], FILTER_VALIDATE_INT) || (int)$p['categoria_id'] < 1) $erros[] = 'Selecione uma categoria válida.';
    if (!is_numeric($p['preco']) || (float)$p['preco'] <= 0) $erros[] = 'O preço deve ser numérico e maior que zero.';
    foreach (['estoque' => 'Estoque', 'estoque_minimo' => 'Estoque mínimo'] as $campo => $rotulo) {
        if (filter_var($p[$campo], FILTER_VALIDATE_INT) === false || (int)$p[$campo] < 0) $erros[] = "$rotulo deve ser um número inteiro maior ou igual a zero.";
    }
    if (!dataValida($p['validade'])) $erros[] = 'Informe uma data de validade válida.';
    $cat = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id=?');
    $cat->execute([(int)$p['categoria_id']]);
    if (!$cat->fetchColumn()) $erros[] = 'A categoria selecionada não existe.';
    $dup = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE codigo=? AND id<>?');
    $dup->execute([$p['codigo'], $id]);
    if ($dup->fetchColumn()) $erros[] = 'Já existe um produto com esse código.';
    if (!$erros) {
        $dados = [$p['codigo'], $p['nome'], $p['descricao'] ?: null, (int)$p['categoria_id'], number_format((float)$p['preco'], 2, '.', ''), (int)$p['estoque'], (int)$p['estoque_minimo'], $p['validade'] ?: null];
        if ($id) {
            $dados[] = $id;
            $sql = 'UPDATE produtos SET codigo=?,nome=?,descricao=?,categoria_id=?,preco=?,estoque=?,estoque_minimo=?,validade=? WHERE id=?';
        } else {
            $sql = 'INSERT INTO produtos (codigo,nome,descricao,categoria_id,preco,estoque,estoque_minimo,validade) VALUES (?,?,?,?,?,?,?,?)';
        }
        $st = $pdo->prepare($sql);
        $st->execute($dados);
        flash('success', $id ? 'Produto atualizado com sucesso!' : 'Produto cadastrado com sucesso!');
        header('Location: index.php');
        exit;
    }
}
require __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row">
    <div>
        <div class="eyebrow">ESTOQUE / PRODUTOS</div>
        <h1><?= $id ? 'Editar produto' : 'Novo produto' ?></h1>
        <p class="muted">Preencha os campos para <?= $id ? 'atualizar' : 'adicionar' ?> um item ao estoque.</p>
    </div><a class="btn btn-light" href="index.php">← Voltar para produtos</a>
</div>
<section class="panel form-panel">
    <div class="panel-heading">
        <div>
            <h2>Informações do produto</h2>
            <p>Campos marcados com * são obrigatórios.</p>
        </div>
    </div>
    <?php if ($erros): ?><div class="alert alert-error"><strong>Verifique os campos:</strong>
            <ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
        </div><?php endif; ?>
    <?php if (!$categorias): ?><div class="alert alert-warning">Cadastre uma categoria antes de cadastrar produtos. <a href="../categorias/form.php">Criar categoria</a></div><?php endif; ?>
    <form method="post" class="data-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-grid">
            <div class="field"><label>Código do produto *</label><input name="codigo" maxlength="30" required value="<?= e($p['codigo']) ?>" placeholder="Ex.: 000125"></div>
            <div class="field"><label>Nome do produto *</label><input name="nome" maxlength="120" required value="<?= e($p['nome']) ?>" placeholder="Ex.: Arroz tipo 1 5kg"></div>
            <div class="field full"><label>Descrição</label><textarea name="descricao" rows="3" placeholder="Informações adicionais sobre o produto..."><?= e($p['descricao']) ?></textarea></div>
            <div class="field"><label>Categoria *</label><select name="categoria_id" required>
                    <option value="">Selecione uma categoria</option><?php foreach ($categorias as $c): ?><option value="<?= $c['id'] ?>" <?= (int)$p['categoria_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option><?php endforeach; ?>
                </select></div>
            <div class="field"><label>Preço (R$) *</label><input type="number" name="preco" min="0.01" step="0.01" required value="<?= e($p['preco']) ?>" placeholder="0,00"></div>
            <div class="field"><label>Quantidade em estoque *</label><input type="number" name="estoque" min="0" step="1" required value="<?= e($p['estoque']) ?>"></div>
            <div class="field"><label>Estoque mínimo *</label><input type="number" name="estoque_minimo" min="0" step="1" required value="<?= e($p['estoque_minimo']) ?>"></div>
            <div class="field"><label>Data de validade</label><input type="date" name="validade" value="<?= e($p['validade']) ?>"><small>Deixe em branco se não houver validade.</small></div>
        </div>
        <div class="form-actions"><a class="btn btn-light" href="index.php">Cancelar</a><button class="btn btn-primary" type="submit" <?= !$categorias ? 'disabled' : '' ?>><?= $id ? 'Salvar alterações' : 'Cadastrar produto' ?></button></div>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>