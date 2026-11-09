# Mercadinho do Zé — Sistema administrativo

Sistema de gerenciamento de categorias e produtos desenvolvido em PHP, MySQL, HTML, CSS e JavaScript, para execução local com XAMPP.

## 1. Requisitos
- XAMPP com Apache e MySQL/MariaDB ativos.
- PHP 8.0 ou superior (recomendado).
- Navegador atualizado.

## 2. Instalação
1. Copie a pasta `mercadinho-do-ze` para `C:\xampp\htdocs\`.
2. Abra o XAMPP e inicie **Apache** e **MySQL**.
3. Acesse `http://localhost/phpmyadmin`.
4. Importe o arquivo `database.sql` (ele cria o banco `supermercado`, as tabelas e dados de demonstração).
5. Confira as configurações em `config/database.php`. O padrão do XAMPP costuma ser usuário `root` e senha vazia.
6. Abra `http://localhost/mercadinho-do-ze/`.

## 3. Estrutura de pastas
- `index.php`: painel inicial e indicadores do estoque.
- `config/database.php`: conexão PDO centralizada.
- `includes/functions.php`: escape HTML, flash, token CSRF e validação de data.
- `includes/header.php` / `footer.php`: estrutura compartilhada e navegação.
- `produtos/`: listagem, busca, filtros, cadastro, edição, detalhes e exclusão.
- `categorias/`: listagem, cadastro, edição e exclusão protegida.
- `assets/style.css`: identidade visual e responsividade.
- `assets/app.js`: menu mobile e fechamento de avisos.
- `database.sql`: estrutura e registros fictícios para demonstração.

## 4. Como personalizar
- **Cores:** altere as variáveis no começo de `assets/style.css` (`--roxo`, `--lilas`, `--verde`, `--amarelo`, `--rosa`, `--cinza`).
- **Nome do mercado:** procure por `Mercadinho do Zé` nos arquivos PHP e substitua pelo nome desejado.
- **Banco e credenciais:** altere `$host`, `$dbname`, `$user` e `$password` em `config/database.php`.
- **Campos:** ajuste o formulário em `produtos/form.php` e mantenha a tabela correspondente em `database.sql`.

## 5. Funcionalidades implementadas
- CRUD completo de categorias e produtos.
- Categoria única por nome; código de produto único.
- Relacionamento obrigatório produto → categoria; categoria com produtos vinculados não pode ser excluída.
- Busca por nome/código e filtro por categoria.
- Filtro para estoque baixo e produtos vencidos.
- Alertas quando estoque atual é menor ou igual ao mínimo.
- Aviso de validade vencida sem impedir o cadastro.
- Preço em reais com duas casas decimais.
- Código como texto para preservar zeros à esquerda.
- Consultas preparadas PDO, validação no servidor, escape com `htmlspecialchars` e formulários POST para alterações/exclusões.
- Token CSRF nos formulários de alteração e exclusão.
- Layout adaptável para celular e computador.

## 6. Testes sugeridos para a apresentação
1. Cadastre uma categoria e um produto.
2. Pesquise um produto pelo nome e depois pelo código `000125`.
3. Filtre por categoria.
4. Abra os filtros de estoque baixo e vencidos.
5. Edite preço e quantidade de um produto.
6. Tente excluir uma categoria que tenha produtos vinculados (a operação deve ser bloqueada).
7. Exclua um produto após confirmar.
8. Tente cadastrar código repetido ou preço zero (o sistema deve recusar).

## 7. Observação sobre o escopo
Este é um painel administrativo local, conforme a atividade fornecida. Não inclui carrinho, vendas, emissão fiscal, login ou pagamento, pois esses recursos não fazem parte do escopo solicitado.
