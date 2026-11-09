<?php
/**
 * CONEXÃO COM O BANCO DE DADOS
 * Altere usuário/senha caso sua instalação do MySQL seja diferente.
 */
$host = 'localhost';
$dbname = 'supermercado';
$user = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Não mostramos detalhes internos da conexão ao usuário.
    http_response_code(500);
    exit('Não foi possível conectar ao banco. Confira o arquivo config/database.php e importe database.sql.');
}
