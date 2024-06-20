<?php
ob_start();

require 'pages/header.php';
require 'classes/Produtos.php';
require 'classes/Formatter.php';

    $produtos = new Produtos($pdo);

    // Função para sanitizar dados de entrada
    function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Verifica se a ação é para excluir o cliente
        if (isset($_POST['action']) && $_POST['action'] == "excluirProduto") {
            // Obtém o ID do cliente a ser excluído
            $produtoId = sanitizeInput($_POST['produtoId']);

            // Chama o método para excluir o cliente
            $excluiu = $produtos->deletarProduto($produtoId);

            // Retorna uma resposta adequada ao AJAX
            if ($excluiu) {
                $_SESSION['message'] = 'Produto excluído com sucesso!';
                $_SESSION['message_type'] = 'danger';
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

        // Verifica se o botão "Cancelar" foi clicado
        if (isset($_POST['action']) && $_POST['action'] == "cancelar") {
            // Redireciona de volta à página original
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Verifica se o botão "Salvar" foi clicado
        if (isset($_POST['action']) && $_POST['action'] == "salvar") {
            $id = sanitizeInput($_POST['edit_id']);
            $descricao = sanitizeInput($_POST['edit_descricao']);
            $status = sanitizeInput($_POST['edit_status']);
            $tempoGarantia = sanitizeInput($_POST['edit_tempoGarantia']);
            $sku = sanitizeInput($_POST['edit_sku']);

            // Verifica se já existe um produto com o mesmo SKU
            if (!$produtos->adicionarProduto($descricao, $status, $tempoGarantia, $sku)) {
                $_SESSION['message'] = 'Já existe um produto cadastrado com esse SKU.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            // Validar campos
            if (empty($descricao) || empty($status) || empty($tempoGarantia) || empty($sku)) {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            // Chama o método para atualizar o Produto
            $produtos->atualizarProduto($id, $descricao, $status, $tempoGarantia, $sku);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Produto atualizado com sucesso!';
        $_SESSION['message_type'] = 'warning';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

            // Exibe mensagem de sucesso
            //echo '<div class="alert alert-success" role="alert">Produto atualizado com sucesso!</div>';

        }
        // Se a ação for "adicionar", adiciona um novo Produto
        if (isset($_POST['action']) && $_POST['action'] == "adicionar") {
            $descricao = sanitizeInput($_POST['descricao']);
            $status = sanitizeInput($_POST['status']);
            $tempoGarantia = sanitizeInput($_POST['tempoGarantia']);
            $sku = sanitizeInput($_POST['sku']);

            // Verifica se já existe um produto com o mesmo SKU
            if (!$produtos->adicionarProduto($descricao, $status, $tempoGarantia, $sku)) {
                $_SESSION['message'] = 'Já existe um produto cadastrado com esse SKU.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            

            // Validar campos
            if (empty($descricao) || empty($status) || empty($tempoGarantia) || empty($sku)) {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            $produtos->adicionarProduto($descricao, $status, $tempoGarantia, $sku);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Produto cadastrado com sucesso!';
        $_SESSION['message_type'] = 'success';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
        }
    }

    // Obtém a lista de Produtos atualizada após a atualização ou adição
    $listaProdutos = $produtos->listarProdutos();

    // Exibição de mensagens de sucesso ou erro
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        Formatter::displayAlert($_SESSION['message'], $_SESSION['message_type']);
        unset($_SESSION['message']);
        unset($_SESSION['message_type']); // Limpa a mensagem da sessão após exibi-la
    }
?>

<div class="container">
    <form method="post" action="">
        <div class="col-md mt-3">
            <div class="form-floating mb-3">
                <input type="text" name="descricao" id="descricao" class="form-control" placeholder="Descrição" required>
                <label for="descricao">Descrição</label>
            </div>
        </div>

        <div class="row g-2 mt-3">
            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="text" name="status" id="status" class="form-control" placeholder="Status" required>
                    <label for="status">Status</label>
                </div>
            </div>

            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="text" name="tempoGarantia" id="tempoGarantia" class="form-control" placeholder="Tempo de garantia ( em meses )" required>
                    <label for="tempoGarantia">Tempo de Garantia ( em meses )</label>
                </div>
            </div>

            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="text" name="sku" id="sku" class="form-control" placeholder="sku" pattern="{11}" title="O SKU-Unidade de Controle de Estoque deve ter no máximo 10 caracteres" maxlength="10" value="<?php echo isset($sku) ? $sku : ''; ?>" required>
                    <label for="sku">SKU (máximo de 10 caracteres)</label>
                </div>
            </div>
        </div>




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
                <th>SKU</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaProdutos as $produto): ?>
                <tr>
                    <form method="post" action="">
                        <input type="hidden" name="edit_id" value="<?php echo $produto['id']; ?>">
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_descricao" value="<?php echo $produto['descricao']; ?>" class="form-control" required>
                            <?php else: ?>
                                <?php echo $produto['descricao']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_status" value="<?php echo $produto['status']; ?>" class="form-control" required>
                            <?php else: ?>
                                <?php echo $produto['status']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_tempoGarantia" value="<?php echo $produto['tempoGarantia']; ?>" class="form-control" required>
                            <?php else: ?>
                                <?php echo $produto['tempoGarantia']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $produto['id']): ?>
                                <input type="text" name="edit_sku" value="<?php echo $produto['sku']; ?>" class="form-control" required>
                            <?php else: ?>
                                <?php echo $produto['sku']; ?>
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

<?php
ob_end_flush();
?>
