-- Banco de dados do Mercadinho do Zé
-- Importe este arquivo pelo phpMyAdmin ou pelo terminal MySQL.
CREATE DATABASE IF NOT EXISTS supermercado CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE supermercado;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE,
    descricao VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    CONSTRAINT fk_produtos_categorias FOREIGN KEY (categoria_id)
        REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_preco_positivo CHECK (preco > 0),
    CONSTRAINT chk_estoque_nao_negativo CHECK (estoque >= 0),
    CONSTRAINT chk_estoque_min_nao_negativo CHECK (estoque_minimo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados iniciais para testar a aplicação (4 categorias e 10 produtos).
INSERT IGNORE INTO categorias (id,nome,descricao) VALUES
(1,'Mercearia','Arroz, feijão, massas, farinhas e alimentos básicos.'),
(2,'Hortifruti','Frutas, legumes e verduras fresquinhas.'),
(3,'Limpeza','Produtos para limpeza e cuidados com a casa.'),
(4,'Bebidas','Águas, sucos e refrigerantes.');

INSERT IGNORE INTO produtos (codigo,nome,descricao,categoria_id,preco,estoque,estoque_minimo,validade) VALUES
('000125','Arroz tipo 1 5kg','Pacote de arroz branco tipo 1.',1,24.90,28,8,'2027-08-10'),
('000126','Feijão carioca 1kg','Feijão carioca selecionado.',1,7.99,3,5,'2027-02-15'),
('000127','Macarrão espaguete 500g','Massa de sêmola.',1,4.59,35,10,'2027-05-20'),
('000128','Farinha de trigo 1kg','Farinha de trigo tradicional.',1,5.49,0,6,'2027-04-10'),
('000220','Banana prata kg','Banana prata fresca.',2,6.99,18,5,NULL),
('000221','Maçã gala kg','Maçã gala selecionada.',2,9.90,12,4,NULL),
('000330','Detergente neutro 500ml','Detergente líquido para louças.',3,2.49,22,8,'2028-01-10'),
('000331','Água sanitária 1L','Alvejante à base de cloro.',3,3.79,14,5,'2025-10-01'),
('000440','Suco de uva 1L','Suco integral de uva.',4,12.90,9,4,'2027-01-15'),
('000441','Água mineral 500ml','Garrafa sem gás.',4,2.00,40,12,NULL);
