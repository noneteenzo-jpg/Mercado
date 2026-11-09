<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
validarCsrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$id = (int)($_POST['id'] ?? 0);
$ver = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE categoria_id=?');
$ver->execute([$id]);
if ($ver->fetchColumn() > 0) {
    flash('error', 'Não é possível excluir: esta categoria possui produtos vinculados.');
} else {
    $st = $pdo->prepare('DELETE FROM categorias WHERE id=?');
    $st->execute([$id]);
    flash($st->rowCount() ? 'success' : 'error', $st->rowCount() ? 'Categoria excluída com sucesso!' : 'Categoria não encontrada.');
}
header('Location: index.php');
exit;
