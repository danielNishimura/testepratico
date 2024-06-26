<?php
ob_start();
    require 'pages/header.php';
    require 'classes/Clientes.php';
    require 'classes/Formatter.php';

    $clientes = new Clientes($pdo);
    
// Função para sanitizar dados de entrada
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Ação: Excluir Cliente
        if ($_POST['action'] == "excluirCliente") {
            $clienteId = sanitizeInput($_POST['clienteId']);
            $excluiu = $clientes->deletarCliente($clienteId);

            if ($excluiu) {
                $_SESSION['message'] = 'Cliente excluído com sucesso!';
                $_SESSION['message_type'] = 'danger';
                http_response_code(200);
                echo json_encode(['success' => true]);
                exit;
            } else {
                $_SESSION['message'] = 'Erro ao excluir o cliente.';
                $_SESSION['message_type'] = 'danger';
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao excluir o cliente.']);
                exit;
            }
        }

        // Ação: Cancelar Edição
        if ($_POST['action'] == "cancelar") {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Ação: Salvar Edição
        if ($_POST['action'] == "salvar") {
            $id = sanitizeInput($_POST['edit_id']);
            $nome = sanitizeInput($_POST['edit_nome']);
            $cpf = sanitizeInput($_POST['edit_cpf']);
            $endereco = sanitizeInput($_POST['edit_endereco']);

            // Validar campos
            if (empty($nome) || empty($cpf) || empty($endereco)) {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            if (!Formatter::validarCPF($cpf)) {
                $_SESSION['message'] = 'CPF inválido.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $clientes->setId($id);
            $clientes->setNome($nome);
            $clientes->setCpf($cpf);
            $clientes->setEndereco($endereco);

            // Atualizar cliente
            if (!$clientes->atualizarCliente($id, $nome, $cpf, $endereco)) {
                $_SESSION['message'] = 'Já existe um cliente cadastrado com esse CPF.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $_SESSION['message'] = 'Cliente atualizado com sucesso!';
            $_SESSION['message_type'] = 'warning';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Ação: Adicionar Cliente
        if ($_POST['action'] == "adicionar") {
            $nome = sanitizeInput($_POST['nome']);
            $cpf = sanitizeInput($_POST['cpf']);
            $endereco = sanitizeInput($_POST['endereco']);

            // Validar campos
            if (empty($nome) || empty($cpf) || empty($endereco)) {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            if (!Formatter::validarCPF($cpf)) {
                $_SESSION['message'] = 'CPF inválido.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $clientes->setNome($nome);
            $clientes->setCpf($cpf);
            $clientes->setEndereco($endereco);

            // Verifica se já existe um cliente com o mesmo CPF
            if (!$clientes->adicionarCliente()) {
                $_SESSION['message'] = 'Já existe um cliente cadastrado com esse CPF.';
                $_SESSION['message_type'] = 'danger';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $_SESSION['message'] = 'Cliente cadastrado com sucesso!';
            $_SESSION['message_type'] = 'success';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Listar clientes
$listaClientes = $clientes->listarClientes();

// Exibição de mensagens de sucesso ou erro
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    Formatter::displayAlert($_SESSION['message'], $_SESSION['message_type']);
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="container">
    <form method="post" action="">
        <div class="row g-2 mt-3">
            <div class="col-md-8">
                <div class="form-floating mb-3">
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Nome" required>
                    <label for="nome">Nome</label>
                </div>
            </div>

            <div class="col-md">
                <div class="form-floating mb-3">
                    <input type="text" name="cpf" id="cpf" class="form-control" placeholder="CPF ( sómente numeros )" pattern="[0-9]{11}" title="Digite um CPF válido com 11 dígitos numéricos" maxlength="11" value="<?php echo isset($cpf) ? $cpf : ''; ?>" required>
                    <label for="cpf">CPF ( sómente numeros )</label>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-floating mb-3">
                <input type="text" name="endereco" id="endereco" class="form-control" placeholder="Endereço">
                <label for="endereco">Endereço</label>
            </div>
        </div>
        

        <!-- Adiciona um campo oculto para identificar a ação -->
        <input type="hidden" name="action" value="adicionar">
        <input type="submit" value="Cadastrar" class="btn btn-success mb-3">
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

<script src="assets/js/scripts.js"></script>

<?php
ob_end_flush();
?>