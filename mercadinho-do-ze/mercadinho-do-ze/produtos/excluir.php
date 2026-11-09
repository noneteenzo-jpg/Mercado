<?php
require_once __DIR__.'/../config/database.php';require_once __DIR__.'/../includes/functions.php';validarCsrf();
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: index.php');exit;}
$id=(int)($_POST['id']??0);$st=$pdo->prepare('DELETE FROM produtos WHERE id=?');$st->execute([$id]);flash($st->rowCount()?'success':'error',$st->rowCount()?'Produto excluído com sucesso!':'Produto não encontrado.');header('Location: index.php');exit;
