<?php
ob_start();
    require 'pages/header.php';
    require './classes/Clientes.php';
    require './classes/Formatter.php';

    $clientes = new Clientes($pdo);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        ##############################
            // Verifica se a ação é para excluir o cliente
    if (isset($_POST['action']) && $_POST['action'] == "excluirCliente") {
        // Obtém o ID do cliente a ser excluído
        $clienteId = $_POST['clienteId'];

        // Chama o método para excluir o cliente
        $excluiu = $clientes->deletarCliente($clienteId);

        // Retorna uma resposta adequada ao AJAX
        if ($excluiu) {
            // Responde com sucesso (status 200)
            http_response_code(200);
            echo json_encode(['success' => true]);
            exit; // Encerra a execução do script após enviar a resposta
        } else {
            // Responde com erro (status 500 ou outro código de erro apropriado)
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir o cliente.']);
            exit; // Encerra a execução do script após enviar a resposta
        }
    }
        ##############################


        // Verifica se o botão "Salvar" foi clicado
        if (isset($_POST['action']) && $_POST['action'] == "salvar") {
            $id = $_POST['edit_id'];
            $nome = $_POST['edit_nome'];
            $cpf = $_POST['edit_cpf'];
            $endereco = $_POST['edit_endereco'];

            // Chama o método para atualizar o cliente
            $clientes->atualizarCliente($id, $nome, $cpf, $endereco);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Cliente atualizado com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

            // Exibe mensagem de sucesso
            //echo '<div class="alert alert-success" role="alert">Cliente atualizado com sucesso!</div>';

        }
        // Se a ação for "adicionar", adiciona um novo cliente
        elseif (isset($_POST['action']) && $_POST['action'] == "adicionar") {
            $nome = $_POST['nome'];
            $cpf = $_POST['cpf'];
            $endereco = $_POST['endereco'];

            // Chama o método para adicionar o cliente
            $clientes->adicionarCliente($nome, $cpf, $endereco);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Cliente cadastrado com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
        }
    }

    // Obtém a lista de clientes atualizada após a atualização ou adição
    $listaClientes = $clientes->listarClientes();
?>

<div class="container">
    <form method="post" action="">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" class="form-control">
        <label for="cpf">CPF</label>
        <input type="text" name="cpf" id="cpf" class="form-control">
        <label for="endereco">Endereço</label>
        <input type="text" name="endereco" id="endereco" class="form-control">
        <!-- Adiciona um campo oculto para identificar a ação -->
        <input type="hidden" name="action" value="adicionar">
        <input type="submit" value="Cadastrar" class="btn btn-success">
    </form>
</div>

<div class="container">
    <h2>Clientes Cadastrados</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Endereço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaClientes as $cliente): ?>
                <tr>
                    <form method="post" action="">
                        <input type="hidden" name="edit_id" value="<?php echo $cliente['id']; ?>">
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $cliente['id']): ?>
                                <input type="text" name="edit_nome" value="<?php echo $cliente['nome']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $cliente['nome']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $cliente['id']): ?>
                                <input type="text" name="edit_cpf" value="<?php echo $cliente['cpf']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo Formatter::formatCPF($cliente['cpf']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $cliente['id']): ?>
                                <input type="text" name="edit_endereco" value="<?php echo $cliente['endereco']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $cliente['endereco']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $cliente['id']): ?>
                                <button type="submit" name="action" value="salvar" class="btn btn-success">Salvar</button>
                                <button type="submit" name="action" value="cancelar" class="btn btn-danger">Cancelar</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="editar" class="btn btn-primary">Editar</button>
                            <?php endif; ?>
                            <a href="#" class="btn btn-danger delete-client" data-client-id="<?php echo $cliente['id']?>">Excluir</a>                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'pages/footer.php' ?>

<script>
$(document).ready(function() {
    // Captura o evento de clique no botão de exclusão
    $('.delete-client').click(function(e) {
        e.preventDefault(); // Evita o comportamento padrão de seguir o link

        // Obtém o ID do cliente a ser excluído do atributo data-client-id
        var clienteId = $(this).data('client-id');

        // Confirmação antes de excluir (opcional)
        if (!confirm('Tem certeza que deseja excluir este cliente?')) {
            return false;
        }

        // Faz a requisição AJAX para excluir o cliente
        $.ajax({
            url: 'clientes.php', // Onde o servidor vai processar a requisição
            type: 'POST',
            data: {
                action: 'excluirCliente',
                clienteId: clienteId
            },
            success: function(response) {
                // Se a exclusão foi bem-sucedida, atualize a lista de clientes ou faça o que for necessário
                alert('Cliente excluído com sucesso!');
                // Recarrega a página para atualizar a lista de clientes
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se houver erro na requisição AJAX
                alert('Erro ao excluir o cliente.');
                console.error(xhr.responseText);
            }
        });
    });
});
</script>