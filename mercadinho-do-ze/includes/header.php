<?php
require_once __DIR__ . '/functions.php';
$paginaAtual = basename($_SERVER['PHP_SELF']);
$tituloPagina = $tituloPagina ?? 'Visão geral';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?> | Mercadinho do Zé</title>
    <link rel="stylesheet" href="<?= $base ?? '' ?>assets/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= $base ?? '' ?>index.php">
            <span class="brand-mark">MZ</span>
            <span><strong>Mercadinho do Zé</strong><small>PAINEL ADMINISTRATIVO</small></span>
        </a>
        <div class="nav-caption">MENU PRINCIPAL</div>
        <nav class="main-nav">
            <a class="nav-link <?= $paginaAtual === 'index.php' ? 'active' : '' ?>" href="<?= $base ?? '' ?>index.php"><span>⌂</span> Visão geral</a>
            <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/produtos/') ? 'active' : '' ?>" href="<?= $base ?? '' ?>produtos/index.php"><span>▦</span> Produtos</a>
            <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/categorias/') ? 'active' : '' ?>" href="<?= $base ?? '' ?>categorias/index.php"><span>▤</span> Categorias</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="help-card"><span class="help-icon">✦</span><strong>Organização em dia!</strong><p>Mantenha seus produtos e categorias sempre atualizados.</p></div>
            <div class="sidebar-foot">Sistema local · PHP + MySQL</div>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>
            <div class="breadcrumb">Mercadinho do Zé <span>/</span> <strong><?= e($tituloPagina) ?></strong></div>
            <div class="topbar-right"><span class="status-dot"></span><span>Ambiente administrativo</span><div class="avatar">Z</div></div>
        </header>
        <section class="page-content">
            <?php mostrarFlash(); ?>
