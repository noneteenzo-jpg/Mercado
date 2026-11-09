<?php
require_once __DIR__ . '/../config/database.php';
$tituloPagina='Produtos'; $base='../';
$busca=trim($_GET['busca'] ?? ''); $categoria=(int)($_GET['categoria'] ?? 0); $estoque=$_GET['estoque'] ?? ''; $validade=$_GET['validade'] ?? '';
$categorias=$pdo->query('SELECT id,nome FROM categorias ORDER BY nome')->fetchAll();
$sql='SELECT p.*, c.nome AS categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE 1=1'; $params=[];
if($busca!==''){ $sql.=' AND (p.nome LIKE :busca OR p.codigo LIKE :busca)'; $params['busca']="%{$busca}%"; }
if($categoria>0){$sql.=' AND p.categoria_id=:categoria';$params['categoria']=$categoria;}
if($estoque==='baixo') $sql.=' AND p.estoque <= p.estoque_minimo';
if($validade==='vencida') $sql.=' AND p.validade IS NOT NULL AND p.validade < CURDATE()';
$sql.=' ORDER BY p.nome'; $stmt=$pdo->prepare($sql);$stmt->execute($params);$produtos=$stmt->fetchAll();
require __DIR__.'/../includes/header.php';
?>
<div class="welcome-row"><div><div class="eyebrow">ESTOQUE</div><h1>Produtos</h1><p class="muted">Consulte, cadastre e mantenha seus produtos atualizados.</p></div><a class="btn btn-primary" href="form.php">＋ Novo produto</a></div>
<section class="panel"><form class="filters" method="get"><div class="search-box"><span>⌕</span><input type="search" name="busca" placeholder="Buscar por nome ou código..." value="<?= e($busca) ?>"></div><select name="categoria"><option value="0">Todas as categorias</option><?php foreach($categorias as $c): ?><option value="<?= $c['id'] ?>" <?= $categoria===$c['id']?'selected':'' ?>><?= e($c['nome']) ?></option><?php endforeach; ?></select><select name="estoque"><option value="">Todo estoque</option><option value="baixo" <?= $estoque==='baixo'?'selected':'' ?>>Estoque baixo</option></select><select name="validade"><option value="">Qualquer validade</option><option value="vencida" <?= $validade==='vencida'?'selected':'' ?>>Vencidos</option></select><button class="btn btn-dark" type="submit">Filtrar</button><a class="btn btn-light" href="index.php">Limpar</a></form>
<div class="panel-heading table-title"><div><h2>Lista de produtos</h2><p><?= count($produtos) ?> registro(s) encontrado(s)</p></div></div>
<?php if(!$produtos): ?><div class="empty-state"><span>⌕</span><strong>Nenhum resultado encontrado</strong><p>Tente alterar a busca ou limpar os filtros.</p></div><?php else: ?><div class="table-wrap"><table><thead><tr><th>CÓDIGO</th><th>PRODUTO</th><th>CATEGORIA</th><th>PREÇO</th><th>ESTOQUE</th><th>VALIDADE</th><th>AÇÕES</th></tr></thead><tbody>
<?php foreach($produtos as $p): $baixo=(int)$p['estoque'] <= (int)$p['estoque_minimo'];$vencido=$p['validade'] && $p['validade']<date('Y-m-d'); ?><tr><td><span class="code-tag"><?= e($p['codigo']) ?></span></td><td><strong><?= e($p['nome']) ?></strong><small class="cell-sub"><?= e(mb_strimwidth($p['descricao'] ?? '',0,48,'…','UTF-8')) ?></small></td><td><?= e($p['categoria']) ?></td><td class="price"><?= dinheiro($p['preco']) ?></td><td><span class="pill <?= $baixo?'pill-warning':'pill-success' ?>"><?= (int)$p['estoque'] ?> un.<?= $baixo?' · baixo':'' ?></span></td><td><?= $p['validade']?date('d/m/Y',strtotime($p['validade'])):'—' ?> <?= $vencido?'<span class="pill pill-danger">Vencido</span>':'' ?></td><td><div class="actions"><a class="action-btn" title="Detalhes" href="detalhes.php?id=<?= $p['id'] ?>">◉</a><a class="action-btn" title="Editar" href="form.php?id=<?= $p['id'] ?>">✎</a><form method="post" action="excluir.php" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="action-btn danger" title="Excluir">×</button></form></div></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<?php require __DIR__.'/../includes/footer.php'; ?>
