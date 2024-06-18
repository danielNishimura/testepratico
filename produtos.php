<?php
ob_start();
    require 'pages/header.php';
    require './classes/Produtos.php';

    $produtos = new Produtos($pdo);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                ##############################
            // Verifica se a ação é para excluir o cliente
        if (isset($_POST['action']) && $_POST['action'] == "excluirProduto") {
            // Obtém o ID do cliente a ser excluído
            $produtoId = $_POST['produtoId'];

            // Chama o método para excluir o cliente
            $excluiu = $produtos->deletarProduto($produtoId);

            // Retorna uma resposta adequada ao AJAX
            if ($excluiu) {
                // Responde com sucesso (status 200)
                http_response_code(200);
                echo json_encode(['success' => true]);
                exit; // Encerra a execução do script após enviar a resposta
            } else {
                // Responde com erro (status 500 ou outro código de erro apropriado)
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao excluir o produto.']);
                exit; // Encerra a execução do script após enviar a resposta
            }
        }
        ##############################

        // Verifica se o botão "Salvar" foi clicado
        if (isset($_POST['action']) && $_POST['action'] == "salvar") {
            $id = $_POST['edit_id'];
            $descricao = $_POST['edit_descricao'];
            $status = $_POST['edit_status'];
            $tempoGarantia = $_POST['edit_tempoGarantia'];

            // Chama o método para atualizar o Produto
            $produtos->atualizarProduto($id, $descricao, $status, $tempoGarantia);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Produto atualizado com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

            // Exibe mensagem de sucesso
            //echo '<div class="alert alert-success" role="alert">Produto atualizado com sucesso!</div>';

        }
        // Se a ação for "adicionar", adiciona um novo Produto
        elseif (isset($_POST['action']) && $_POST['action'] == "adicionar") {
            $descricao = $_POST['descricao'];
            $status = $_POST['status'];
            $tempoGarantia = $_POST['tempoGarantia'];

            // Chama o método para adicionar o Produto
            var_dump ($_POST);
           // die;
            $produtos->adicionarProduto($descricao, $status, $tempoGarantia);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Produto cadastrado com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
        }
    }

    // Obtém a lista de Produtos atualizada após a atualização ou adição
    $listaProdutos = $produtos->listarProdutos();
?>

<div class="container">
    <form method="post" action="">
        <label for="descricao">Descrição</label>
        <input type="text" name="descricao" id="descricao" class="form-control">
        <label for="status">Status</label>
        <input type="text" name="status" id="status" class="form-control">
        <label for="tempoGarantia">Tempo de Garantia ( em meses )</label>
        <input type="text" name="tempoGarantia" id="tempoGarantia" class="form-control">
        <!-- Adiciona um campo oculto para identificar a ação -->
        <input type="hidden" name="action" value="adicionar">
        <input type="submit" value="Cadastrar" class="btn btn-success">
    </form>
</div>

<div class="container">
    <h2>Produtos Cadastrados</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Descricao</th>
                <th>Status</th>
                <th>Tempo de Garantia ( em meses )</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaProdutos as $produto): ?>
                <tr>
                    <form method="post" action="">
                        <input type="hidden" name="edit_id" value="<?php echo $produto['id']; ?>">
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_descricao" value="<?php echo $produto['descricao']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $produto['descricao']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_status" value="<?php echo $produto['status']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $produto['status']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_tempoGarantia" value="<?php echo $produto['tempoGarantia']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $produto['tempoGarantia']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <button type="submit" name="action" value="salvar" class="btn btn-success">Salvar</button>
                                <button type="submit" name="action" value="cancelar" class="btn btn-danger">Cancelar</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="editar" class="btn btn-primary">Editar</button>
                            <?php endif; ?>
                            <a href="#" class="btn btn-danger delete-product" data-product-id="<?php echo $produto['id']?>">Excluir</a>
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
    $('.delete-product').click(function(e) {
        e.preventDefault(); // Evita o comportamento padrão de seguir o link

        // Obtém o ID do produto a ser excluído do atributo data-product-id
        var produtoId = $(this).data('product-id');

        // Confirmação antes de excluir (opcional)
        if (!confirm('Tem certeza que deseja excluir este produto?')) {
            return false;
        }

        // Faz a requisição AJAX para excluir o produto
        $.ajax({
            url: 'produtos.php', // Onde o servidor vai processar a requisição
            type: 'POST',
            data: {
                action: 'excluirProduto',
                produtoId: produtoId
            },
            success: function(response) {
                // Se a exclusão foi bem-sucedida, atualize a lista de prosutos ou faça o que for necessário
                alert('Produto excluído com sucesso!');
                // Recarrega a página para atualizar a lista de produtos
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se houver erro na requisição AJAX
                alert('Erro ao excluir o produto.');
                console.error(xhr.responseText);
            }
        });
    });
});
</script>
