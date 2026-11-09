<?php
require_once __DIR__ . '/config/database.php';
$tituloPagina = 'Visão geral'; $base = '';
$totalProdutos = (int)$pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$totalCategorias = (int)$pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$baixoEstoque = (int)$pdo->query('SELECT COUNT(*) FROM produtos WHERE estoque <= estoque_minimo')->fetchColumn();
$vencidos = (int)$pdo->query('SELECT COUNT(*) FROM produtos WHERE validade IS NOT NULL AND validade < CURDATE()')->fetchColumn();
$recentes = $pdo->query('SELECT p.id,p.codigo,p.nome,p.preco,p.estoque,p.estoque_minimo,c.nome AS categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id ORDER BY p.id DESC LIMIT 6')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="welcome-row"><div><div class="eyebrow">PAINEL DE CONTROLE</div><h1>Bom dia! 👋</h1><p class="muted">Aqui está o resumo do seu mercadinho. Vamos manter tudo organizado?</p></div><a class="btn btn-primary" href="produtos/form.php">＋ Cadastrar produto</a></div>
<div class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span class="stat-icon purple">▦</span><span class="stat-label">CADASTRADOS</span></div><strong><?= $totalProdutos ?></strong><p>Produtos no sistema</p><a href="produtos/index.php">Ver produtos →</a></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon teal">▤</span><span class="stat-label">ORGANIZAÇÃO</span></div><strong><?= $totalCategorias ?></strong><p>Categorias cadastradas</p><a href="categorias/index.php">Ver categorias →</a></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon yellow">⚑</span><span class="stat-label">ATENÇÃO</span></div><strong><?= $baixoEstoque ?></strong><p>Produtos com estoque baixo</p><a href="produtos/index.php?estoque=baixo">Conferir estoque →</a></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon pink">◷</span><span class="stat-label">VALIDADE</span></div><strong><?= $vencidos ?></strong><p>Produtos vencidos</p><a href="produtos/index.php?validade=vencida">Ver vencidos →</a></article>
</div>
<div class="content-grid">
    <section class="panel recent-panel"><div class="panel-heading"><div><h2>Produtos recentes</h2><p>Últimos itens adicionados ao estoque</p></div><a class="text-link" href="produtos/index.php">Ver todos →</a></div>
    <?php if (!$recentes): ?><div class="empty-state"><span>▦</span><strong>Nenhum produto cadastrado</strong><p>Comece adicionando o primeiro item ao seu mercadinho.</p><a class="btn btn-primary" href="produtos/form.php">Cadastrar produto</a></div><?php else: ?>
    <div class="table-wrap"><table><thead><tr><th>PRODUTO</th><th>CATEGORIA</th><th>PREÇO</th><th>ESTOQUE</th><th>SITUAÇÃO</th></tr></thead><tbody>
    <?php foreach ($recentes as $p): $baixo = (int)$p['estoque'] <= (int)$p['estoque_minimo']; ?><tr><td><strong><?= e($p['nome']) ?></strong><small class="cell-sub">Cód. <?= e($p['codigo']) ?></small></td><td><?= e($p['categoria']) ?></td><td class="price"><?= dinheiro($p['preco']) ?></td><td><?= (int)$p['estoque'] ?> un.</td><td><span class="pill <?= $baixo ? 'pill-warning' : 'pill-success' ?>"><?= $baixo ? 'Estoque baixo' : 'Normal' ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?></section>
    <aside class="panel quick-panel"><div class="panel-heading"><div><h2>Acesso rápido</h2><p>O que deseja fazer agora?</p></div></div><a class="quick-action" href="produtos/form.php"><span class="quick-icon purple">＋</span><span><strong>Novo produto</strong><small>Adicionar item ao estoque</small></span><b>›</b></a><a class="quick-action" href="categorias/form.php"><span class="quick-icon teal">＋</span><span><strong>Nova categoria</strong><small>Organizar seus produtos</small></span><b>›</b></a><a class="quick-action" href="produtos/index.php"><span class="quick-icon yellow">⌕</span><span><strong>Consultar estoque</strong><small>Buscar e filtrar produtos</small></span><b>›</b></a><div class="tip-box"><strong>💡 Dica do Zé</strong><p>Produtos com estoque igual ou abaixo do mínimo aparecem destacados para facilitar a reposição.</p></div></aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
