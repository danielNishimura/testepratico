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

// Função para sanitizar dados de entrada
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == "adicionar") {
        $dataAbertura = sanitizeInput($_POST['dataAbertura']);
        $clienteCpf = sanitizeInput($_POST['clienteCpf']);
        $clienteNome = sanitizeInput($_POST['clienteNome']);
        $clienteEndereco = sanitizeInput($_POST['clienteEndereco']);
        $produtos = $_POST['produtos'];
        $errors = [];

        // Verificar se os campos obrigatórios estão vazios
        if (empty($dataAbertura)) {
            $errors[] = 'A data de abertura é obrigatória.';
        }

        if (empty($clienteCpf)) {
            $errors[] = 'O CPF do consumidor é obrigatório.';
        }

        if (empty($clienteNome)) {
            $errors[] = 'O nome do consumidor é obrigatório.';
        }

        if (empty($clienteEndereco)) {
            $errors[] = 'O endereço do consumidor é obrigatório.';
        }

        if (empty($produtos)) {
            $errors[] = 'Pelo menos um produto deve ser selecionado.';
        }

        // Verificar se o cliente já existe pelo CPF
        $clienteExistente = $cliente->verificarCliente($clienteCpf);
        if (!$clienteExistente) {

            $cliente->setNome($clienteNome);
            $cliente->setCpf($clienteCpf);
            $cliente->setEndereco($clienteEndereco);
            // Se o cliente não existe, adiciona o novo cliente
            $clienteId = $cliente->adicionarCliente($cliente->getNome(), $cliente->getCpf(), $cliente->getEndereco());
            if (!$clienteId) {
                $errors[] = 'Erro ao adicionar um novo cliente.';
            }

        } else {
            // Se o cliente já existe, obter o ID do cliente existente
            $clienteId = $clienteExistente['id'];
        }

        if (empty($errors)) {
            // Chama o método para adicionar a ordem
            $ordemId = $ordem->adicionarOrdem($dataAbertura, $clienteId);

            if ($ordemId) {
                foreach ($produtos as $produtoId) {
                    $ordem->adicionarProdutoOrdem($ordemId, $produtoId);
                }
                // Armazena a mensagem de sucesso na sessão
                $_SESSION['message'] = 'Ordem cadastrada com sucesso!';
                $_SESSION['message_type'] = 'success';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $errors[] = 'Erro ao cadastrar a ordem.';
            }
        }

        // Se houver erros, armazenar na sessão e redirecionar de volta
        $_SESSION['errors'] = $errors;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] == "excluirOrdem") {
        // Ação para excluir a ordem
        $ordemId = $_POST['ordemId'];

        if ($ordem->deletarOrdem($ordemId)) {
            echo 'Ordem excluída com sucesso!';
            // Aqui você pode retornar qualquer resposta necessária para o AJAX
            exit;
        } else {
            // Se houver um erro ao excluir
            http_response_code(500);
            echo 'Erro ao excluir a ordem.';
            exit;
        }
    }
}

// Obtém a lista de ordens atualizada após a atualização ou adição
$listaordem = $ordem->listarordem();
$clientes = $cliente->listarClientes();
$produtos = $produto->listarProdutos();

// Exibição de mensagens de sucesso ou erro
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    Formatter::displayAlert($_SESSION['message'], $_SESSION['message_type']);
    unset($_SESSION['message']);
    unset($_SESSION['message_type']); // Limpa a mensagem da sessão após exibi-la
}

// Exibição de erros de validação
if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        Formatter::displayAlert($error, 'danger');
    }
    unset($_SESSION['errors']);
}
?>

<div class="container">
    <form method="post" action="">

        <div class="row g-2 mt-3">
            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="date" name="dataAbertura" id="dataAbertura" class="form-control" placeholder="Data de abertura" required>
                    <label for="dataAbertura">Data de Abertura</label>
                </div>
            </div>
            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="text" name="clienteCpf" id="clienteCpf" class="form-control" pattern="[0-9]*" placeholder="CPF do consumidor (sómente numeros)" required>
                    <label for="cpf">CPF do consumidor (sómente numeros)</label>
                </div>
            </div>
            <div class="col-md">
                <div class="form-floating mb-3">
                    <button type="button" id="searchCliente" class="btn btn-primary mt-2 mb-2">Pesquisar Cliente</button>
                </div>
            </div>
        </div>

        <div id="clienteInfo">
            <div class="form-floating mb-3">
                <input type="text" name="clienteNome" id="clienteNome" class="form-control" placeholder="Nome do consumidor" required>
                <label for="clienteNome">Nome do consumidor</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="clienteEndereco" id="clienteEndereco" class="form-control" placeholder="Endereço do consumidor">
                <label for="clienteEndereco">Endereço do consumidor</label>
            </div>
        </div>

        <div class="row g-2 mt-3">
            <div class="col-md-4">
                <div id="produtosContainer">
                    <div class="form-floating">
                        <select name="produtos[]" id="produtos" class="form-select mb-2" aria-label="form-select">
                            <?php foreach ($produtos as $produto): ?>
                            <option value="<?php echo $produto['id']; ?>"> <?php echo $produto['descricao']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="produtos">Produtos</label>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                        <button type="button" id="addProduto" class="btn btn-secondary mb-2">Adicionar Produto</button>
                </div>
            </div>
        </div>
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
                <th>Código</th>
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
                            <?php echo $ordem['id']; ?>
                        </td>
                        <td>
                            <?php if(isset($_POST['edit_id']) && $_POST['edit_id'] == $ordem['id']): ?>
                                <input type="text" name="edit_dataAbertura" value="<?php echo Formatter::formatDataAbertura($ordem['dataAbertura']); ?>" class="form-control">
                            <?php else: ?>
                                <?php echo Formatter::formatDataAbertura($ordem['dataAbertura']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $ordem['clienteNome']; ?>
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
        console.log('ID da ordem a ser excluída:', ordemId); // Log para depuração

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
                console.log('Resposta do servidor:', response); // Log para depuração
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