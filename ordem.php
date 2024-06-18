<?php
ob_start();
    require 'pages/header.php';
    require './classes/Ordem.php';
    require './classes/Clientes.php';
    require './classes/Produtos.php';
    require './classes/Formatter.php';
    
    $ordem = new Ordem($pdo);
    $cliente = new Clientes($pdo);
    $produto = new Produtos($pdo);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                ##############################
            // Verifica se a ação é para excluir o cliente
    if (isset($_POST['action']) && $_POST['action'] == "excluirOrdem") {
        // Obtém o ID do cliente a ser excluído
        $ordemId = $_POST['ordemId'];

        // Chama o método para excluir o cliente
        $excluiu = $ordem->deletarOrdem($ordemId);

        // Retorna uma resposta adequada ao AJAX
        if ($excluiu) {
            // Responde com sucesso (status 200)
            http_response_code(200);
            echo json_encode(['success' => true]);
            exit; // Encerra a execução do script após enviar a resposta
        } else {
            // Responde com erro (status 500 ou outro código de erro apropriado)
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir a ordem selecionada.']);
            exit; // Encerra a execução do script após enviar a resposta
        }
    }
        ##############################




        // Verifica se o botão "Salvar" foi clicado
        if (isset($_POST['action']) && $_POST['action'] == "salvar") {
            $id = $_POST['edit_id'];
            $dataAbertura = $_POST['edit_dataAbertura'];
            $clienteId = $_POST['edit_clienteId'];

            // Chama o método para atualizar o Ordem
            $ordem->atualizarOrdem($id, $dataAbertura, $clienteId);

        // Armazena a mensagem de sucesso na sessão
        $_SESSION['message'] = 'Ordem atualizado com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

            // Exibe mensagem de sucesso
            //echo '<div class="alert alert-success" role="alert">Ordem atualizado com sucesso!</div>';

        }
        // Se a ação for "adicionar", adiciona um novo Ordem
        elseif (isset($_POST['action']) && $_POST['action'] == "adicionar") {
            $dataAbertura = $_POST['dataAbertura'];
            $clienteCpf = $_POST['clienteCpf'];
            $clienteNome = $_POST['clienteNome'];
            $clienteEndereco = $_POST['clienteEndereco'];
            $produtos = $_POST['produtos'];

            //var_dump($_POST);
            //die;

            // Verifica se o cliente já existe
            $clienteExistente = $cliente->verificarCliente($clienteCpf);

            if ($clienteExistente) {
                $clienteId = $clienteExistente['id'];
            } else {
                $clienteId = $cliente->adicionarCliente($clienteNome, $clienteCpf, $clienteEndereco);
            }
            // Certifique-se de que $clienteId não está vazio ou nulo
            if (!empty($clienteId)) {
                // Chama o método para adicionar o Ordem
                $ordemId = $ordem->adicionarOrdem($dataAbertura, $clienteId);

                if ($ordemId) {
                    foreach ($produtos as $produtoId) {
                        $ordem->adicionarProdutoOrdem($ordemId, $produtoId);
                    }
                    // Armazena a mensagem de sucesso na sessão
                    $_SESSION['message'] = 'Ordem cadastrada com sucesso!';
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo 'Erro: Ordem ID não foi gerado corretamente.';
                }
            } else; {
                echo 'Erro: Cliente ID não foi definido corretamente.';
            }
        }
    }

    // Obtém a lista de ordem atualizada após a atualização ou adição
    $listaordem = $ordem->listarordem();
    $clientes = $cliente->listarClientes();
    $produtos = $produto->listarProdutos();

?>

<div class="container">
    <form method="post" action="">
        <p>Numero da ordem</p>

        <label for="dataAbertura">Data de Abertura</label>
        <input type="date" name="dataAbertura" id="dataAbertura" class="form-control">
        
        <label for="cpf">CPF do consumidor (sómente numeros)</label>
        <input type="text" name="clienteCpf" id="clienteCpf" class="form-control" pattern="[0-9]*">

        <button type="button" id="searchCliente" class="btn btn-primary mt-2 mb-2">Pesquisar Cliente</button>

        <div id="clienteInfo">
            <label for="clienteNome">Nome do consumidor</label>
            <input type="text" name="clienteNome" id="clienteNome" class="form-control">

            <label for="clienteEndereco">Endereço do consumidor</label>
            <input type="text" name="clienteEndereco" id="clienteEndereco" class="form-control">

        </div>
        <!--
        <label for="clienteId">Nome do consumidor</label>
        <select name="clienteId" id="clienteId" class="form-control">
            <?php #foreach ($clientes as $cliente): ?>
                <option value="<?php #echo $cliente['id']; ?>" data-cpf="<?php #echo $cliente['cpf']; ?>"><?php #echo $cliente['nome']; ?></option>
            <?php #endforeach; ?>
            <option value="novo">Cadastrar novo cliente</option>
        </select>
        -->
        <hr>

        <label for="produtos">Produtos</label>
        <div id="produtosContainer">
            <select name="produtos[]" id="produtos" class="form-control mb-2">
                <?php foreach ($produtos as $produto): ?>
                    <option value="<?php echo $produto['id']; ?>"> <?php echo $produto['descricao']; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" id="addProduto" class="btn btn-secondary mb-3">Adicionar Produto</button>

            <!--<label for="ordemOrdemId">Tempo de Garantia</label>-->
            <!--<input type="text" name="ordemOrdemId" id="ordemOrdemId" class="form-control">-->
        <!-- Adiciona um campo oculto para identificar a ação -->
         <hr> 
        <input type="hidden" name="action" value="adicionar">
        <input type="submit" value="Cadastrar" class="btn btn-success">
    </form>
</div>
<hr>

<div class="container">
    <h2>Ordens Cadastradas</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Data de abertura</th>
                <th>Nome do consumidor</th>
                <th>CPF do consumidor</th>
                <th>Produtos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaordem as $ordem): ?>
                <tr>
                    <form method="post" action="">
                        <input type="hidden" name="edit_id" value="<?php echo $ordem['id']; ?>">
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $ordem['id']): ?>
                                <input type="text" name="edit_dataAbertura" value="<?php echo Formatter::formatDataAbertura($ordem['dataAbertura']); ?>" class="form-control">
                            <?php else: ?>
                                <?php echo Formatter::formatDataAbertura($ordem['dataAbertura']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $ordem['id']): ?>
                                <input type="text" name="edit_clienteId" value="<?php echo $ordem['clienteNome']; ?>" class="form-control">
                            <?php else: ?>
                                <?php echo $ordem['clienteNome']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo Formatter::formatCPF($ordem['clienteCpf'])  ; ?>
                        </td>
                        <td>
                            <?php if (isset($ordem['produtos']) && is_array($ordem['produtos'])): ?>
                                <?php foreach ($ordem['produtos'] as $produto): ?>
                                    <?php echo $produto['descricao']; ?><br>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $ordem['id']): ?>
                                <button type="submit" name="action" value="salvar" class="btn btn-success">Salvar</button>
                                <button type="submit" name="action" value="cancelar" class="btn btn-danger">Cancelar</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="editar" class="btn btn-primary">Editar</button>
                            <?php endif; ?>
                            <a href="#" class="btn btn-danger delete-ordem" data-ordem-id="<?php echo $ordem['id']; ?>">Excluir</a>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'pages/footer.php' ?>

<!-- Inclua o jQuery -->
<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {

    $('#clienteInfo').hide();

    $('#searchCliente').click(function() {
        var cpf = $('#clienteCpf').val();

        $.ajax({
            url: 'buscar_cliente.php',
            type: 'POST',
            data: {cpf: cpf},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#clienteNome').val(response.nome);
                    $('#clienteEndereco').val(response.endereco);
                    $('#clienteInfo').show();
                } else {
                    $('#clienteNome').val('');
                    $('#clienteEndereco').val('');
                    $('#clienteInfo').show();
                }
            },
            error: function() {
                console.log('Erro ao buscar cliente');
            }
        });
    });

    $('#clienteCpf').on('input', function() {
        // Remove caracteres não numéricos
        var sanitized = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(sanitized);
    });

    $('#addProduto').click(function() {
        var newProdutoSelect = $('select[name="produtos[]"]:first').clone();
        newProdutoSelect.val('');
        $('#produtosContainer').append(newProdutoSelect);
    });

    // Captura o evento de clique no botão de exclusão
    $('.delete-ordem').click(function(e) {
    e.preventDefault(); // Evita o comportamento padrão de seguir o link

    // Obtém o ID da ordem a ser excluída do atributo data-ordem-id
    var ordemId = $(this).data('ordem-id');

    // Confirmação antes de excluir (opcional)
    if (!confirm('Tem certeza que deseja excluir esta ordem?')) {
        return false;
    }

    // Faz a requisição AJAX para excluir a ordem
    $.ajax({
        url: 'ordem.php', // Onde o servidor vai processar a requisição
        type: 'POST',
        data: {
            action: 'excluirOrdem',
            ordemId: ordemId
        },
        success: function(response) {
            // Se a exclusão foi bem-sucedida, atualize a lista de ordens ou faça o que for necessário
            alert('Ordem excluída com sucesso!');
            // Recarrega a página para atualizar a lista de ordens
            location.reload();
        },
        error: function(xhr, status, error) {
            // Se houver erro na requisição AJAX
            alert('Erro ao excluir a ordem.');
            console.error(xhr.responseText);
        }
    });
    });
});
</script>
