<?php

class Clientes {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function adicionarCliente($nome, $cpf, $endereco) {
        $stmt = $this->pdo->prepare("INSERT INTO tbClientes (nome, cpf, endereco) VALUES (:nome, :cpf, :endereco)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':endereco', $endereco);
        return $stmt->execute();
    }

    public function listarClientes() {
        $stmt = $this->pdo->prepare("SELECT * FROM tbClientes ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarCliente($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tbClientes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarCliente($id, $nome, $cpf, $endereco) {
        $stmt = $this->pdo->prepare("UPDATE tbClientes SET nome = :nome, cpf = :cpf, endereco = :endereco WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':endereco', $endereco);
        return $stmt->execute();
    }

    public function deletarCliente($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tbClientes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function verificarCliente($cpf) {
        $stmt = $this->pdo->prepare("SELECT id, nome, endereco FROM tbclientes WHERE cpf = :cpf");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>