# Teste prático Sistema de Gestão de Ordens

Este projeto é um sistema de gestão de ordens que permite adicionar, editar e excluir ordens, bem como gerenciar clientes e produtos.

## Funcionalidades

- **Adicionar Ordem:** Permite adicionar uma nova ordem com data de abertura, cliente e produtos associados.
- **Editar Ordem:** Permite editar as informações de uma ordem existente.
- **Excluir Ordem:** Permite excluir uma ordem do sistema.
- **Gerenciamento de Clientes:** Permite adicionar novos clientes ou utilizar clientes já existentes.
- **Gerenciamento de Produtos:** Permite adicionar produtos a uma ordem.

## Tecnologias Utilizadas

- **PHP:** Linguagem de programação utilizada para desenvolver a lógica do sistema.
- **Postgresql:** Banco de dados utilizado para armazenar as informações das ordens, clientes e produtos.
- **HTML/CSS:** Utilizados para a construção da interface do usuário.
- **JavaScript/jQuery:** Utilizados para funcionalidades dinâmicas na interface do usuário.
- **Docker:** Utilizados para funcionalidades dinâmicas na interface do usuário.

## Estrutura do Projeto

- `ordem.php`: Página principal onde as ordens são gerenciadas.
- `classes/Ordem.php`: Classe responsável pelas operações relacionadas às ordens.
- `classes/Clientes.php`: Classe responsável pelas operações relacionadas aos clientes.
- `classes/Produtos.php`: Classe responsável pelas operações relacionadas aos produtos.
- `classes/Formatter.php`: Classe responsável pela formatação de dados.
- `pages/header.php`: Cabeçalho do projeto.
- `pages/footer.php`: Rodapé do projeto.
- `Dockerfile`: Arquivo de configuração para criação da imagem Docker do projeto.
- `docker-compose.yml`: Arquivo de configuração do Docker Compose para orquestrar os contêineres do projeto.

## Uso

### Adicionar uma Nova Ordem

1. Preencha a data de abertura.
2. Insira o CPF do cliente e clique em "Pesquisar Cliente".
3. Preencha ou verifique as informações do cliente.
4. Adicione os produtos associados à ordem.
5. Clique em "Cadastrar" para salvar a ordem.

### Editar uma Ordem

1. Na lista de ordens, clique no botão "Editar" da ordem que deseja modificar.
2. Faça as alterações necessárias.
3. Clique em "Salvar" para atualizar a ordem.

### Excluir uma Ordem

1. Na lista de ordens, clique no botão "Excluir" da ordem que deseja remover.
2. Confirme a exclusão quando solicitado.

## Contribuição

1. Faça um fork do projeto.
2. Crie um branch para a sua feature (`git checkout -b feature/nova-feature`).
3. Commit suas alterações (`git commit -am 'Adiciona nova feature'`).
4. Push para o branch (`git push origin feature/nova-feature`).
5. Abra um Pull Request.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

