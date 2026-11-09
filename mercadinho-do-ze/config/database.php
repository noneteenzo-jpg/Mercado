<?php
/**
 * CONEXÃO E INSTALAÇÃO AUTOMÁTICA DO BANCO
 *
 * Este arquivo:
 * 1. Conecta ao servidor MySQL do XAMPP.
 * 2. Cria o banco "supermercado", caso ainda não exista.
 * 3. Cria as tabelas categorias e produtos.
 * 4. Insere dados de demonstração apenas se as tabelas estiverem vazias.
 *
 * Assim, não é obrigatório importar database.sql manualmente.
 * O arquivo database.sql continua disponível para instalação manual.
 *
 * Se seu MySQL tiver outra configuração, altere as variáveis abaixo.
 */

$host = '127.0.0.1';
$port = '3306';
$dbname = 'supermercado';
$user = 'root';
$password = '';

try {
    // Primeiro conecta ao SERVIDOR, sem selecionar um banco que talvez não exista.
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Cria o banco caso seja a primeira execução.
    $pdo->exec(
        "CREATE DATABASE IF NOT EXISTS `{$dbname}`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    // Seleciona o banco recém-criado ou já existente.
    $pdo->exec("USE `{$dbname}`");

    // Cria a tabela de categorias.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(80) NOT NULL UNIQUE,
            descricao VARCHAR(255) NULL
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci
    ");

    // Cria a tabela de produtos e seu relacionamento com categorias.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(30) NOT NULL UNIQUE,
            nome VARCHAR(120) NOT NULL,
            descricao TEXT NULL,
            categoria_id INT NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            estoque INT NOT NULL DEFAULT 0,
            estoque_minimo INT NOT NULL DEFAULT 0,
            validade DATE NULL,
            CONSTRAINT fk_produtos_categorias
                FOREIGN KEY (categoria_id)
                REFERENCES categorias(id)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci
    ");

    // Só adiciona categorias de exemplo se a tabela estiver vazia.
    $totalCategorias = (int) $pdo->query(
        "SELECT COUNT(*) FROM categorias"
    )->fetchColumn();

    if ($totalCategorias === 0) {
        $categorias = [
            ['Mercearia', 'Arroz, feijão, massas, farinhas e alimentos básicos.'],
            ['Hortifruti', 'Frutas, legumes e verduras fresquinhas.'],
            ['Limpeza', 'Produtos para limpeza e cuidados com a casa.'],
            ['Bebidas', 'Águas, sucos e refrigerantes.']
        ];

        $stmtCategoria = $pdo->prepare(
            "INSERT INTO categorias (nome, descricao) VALUES (?, ?)"
        );

        foreach ($categorias as $categoria) {
            $stmtCategoria->execute($categoria);
        }
    }

    // Só adiciona produtos de exemplo se a tabela estiver vazia.
    $totalProdutos = (int) $pdo->query(
        "SELECT COUNT(*) FROM produtos"
    )->fetchColumn();

    if ($totalProdutos === 0) {
        // Busca os IDs reais das categorias para não depender de IDs fixos.
        $idsCategorias = $pdo->query(
            "SELECT nome, id FROM categorias"
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $produtos = [
            ['000125', 'Arroz tipo 1 5kg', 'Pacote de arroz branco tipo 1.', 'Mercearia', 24.90, 28, 8, '2027-08-10'],
            ['000126', 'Feijão carioca 1kg', 'Feijão carioca selecionado.', 'Mercearia', 7.99, 3, 5, '2027-02-15'],
            ['000127', 'Macarrão espaguete 500g', 'Massa de sêmola.', 'Mercearia', 4.59, 35, 10, '2027-05-20'],
            ['000128', 'Farinha de trigo 1kg', 'Farinha de trigo tradicional.', 'Mercearia', 5.49, 0, 6, '2027-04-10'],
            ['000220', 'Banana prata kg', 'Banana prata fresca.', 'Hortifruti', 6.99, 18, 5, null],
            ['000221', 'Maçã gala kg', 'Maçã gala selecionada.', 'Hortifruti', 9.90, 12, 4, null],
            ['000330', 'Detergente neutro 500ml', 'Detergente líquido para louças.', 'Limpeza', 2.49, 22, 8, '2028-01-10'],
            ['000331', 'Água sanitária 1L', 'Alvejante à base de cloro.', 'Limpeza', 3.79, 14, 5, '2025-10-01'],
            ['000440', 'Suco de uva 1L', 'Suco integral de uva.', 'Bebidas', 12.90, 9, 4, '2027-01-15'],
            ['000441', 'Água mineral 500ml', 'Garrafa sem gás.', 'Bebidas', 2.00, 40, 12, null]
        ];

        $stmtProduto = $pdo->prepare("
            INSERT INTO produtos
                (codigo, nome, descricao, categoria_id, preco, estoque, estoque_minimo, validade)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($produtos as $produto) {
            $categoriaId = $idsCategorias[$produto[3]] ?? null;

            if ($categoriaId !== null) {
                $stmtProduto->execute([
                    $produto[0],
                    $produto[1],
                    $produto[2],
                    $categoriaId,
                    $produto[4],
                    $produto[5],
                    $produto[6],
                    $produto[7]
                ]);
            }
        }
    }

} catch (PDOException $e) {
    // Mensagem amigável para facilitar a configuração local no XAMPP.
    http_response_code(500);
    exit(
        '<div style="font-family:Arial,sans-serif;max-width:760px;margin:50px auto;padding:24px;'
        . 'border:1px solid #e5b8bf;border-radius:12px;background:#fff7f8;color:#352052">'
        . '<h2>Não foi possível iniciar o banco de dados</h2>'
        . '<p>Confira se o MySQL está iniciado no XAMPP e se o usuário e a senha estão corretos '
        . 'no arquivo <strong>config/database.php</strong>.</p>'
        . '<p><strong>Detalhe técnico:</strong> '
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
        . '</p><p>Configuração padrão esperada: servidor 127.0.0.1, porta 3306, '
        . 'usuário root e senha vazia.</p></div>'
    );
}
