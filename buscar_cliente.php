<?php
require_once 'conexao.php';
require './classes/Clientes.php';

$cliente = new Clientes($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = $_POST['cpf'];
    $clienteInfo = $cliente->verificarCliente($cpf);

    if ($clienteInfo) {
        echo json_encode([
            'success' => true,
            'nome' => $clienteInfo['nome'],
            'endereco' => $clienteInfo['endereco']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
