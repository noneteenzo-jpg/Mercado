<?php
/** Funções compartilhadas: segurança, mensagens e formatação. */
if (session_status() === PHP_SESSION_NONE) session_start();

function e($valor): string {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
function dinheiro($valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
function flash(string $tipo, string $mensagem): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}
function mostrarFlash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['tipo']) . '">' . e($f['mensagem']) . '</div>';
        unset($_SESSION['flash']);
    }
}
function csrfToken(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}
function validarCsrf(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $tokenSessao = $_SESSION['csrf'] ?? '';
    $tokenFormulario = $_POST['csrf_token'] ?? '';

    if (
        $tokenSessao === '' ||
        $tokenFormulario === '' ||
        !hash_equals($tokenSessao, $tokenFormulario)
    ) {
        http_response_code(403);
        exit('Solicitação inválida ou expirada. Atualize a página e tente novamente.');
    }
}
function dataValida(?string $data): bool {
    if ($data === null || $data === '') return true;
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}
