<?php

class Clientes {
    private $pdo;
    private $id;
    private $nome;
    private $cpf;
    private $endereco;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Seters Mettods
    public function setId($id) {
        $this->id = $id; 
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    // Getters Mettods
    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function adicionarCliente() {
        // Verifica se já existe um cliente com o mesmo CPF
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM tbClientes WHERE cpf = :cpf");
        $stmt->bindParam(':cpf', $this->cpf);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Já existe um cliente com esse CPF
            return false;
        }

        // Se não há cliente com o mesmo CPF, insere o novo cliente
        $stmt = $this->pdo->prepare("INSERT INTO tbClientes (nome, cpf, endereco) VALUES (:nome, :cpf, :endereco)");
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':cpf', $this->cpf);
        $stmt->bindParam(':endereco', $this->endereco);

        if ($stmt->execute()) {
            // Retorna o ID do cliente inserido
            return $this->pdo->lastInsertId();
        } else {
            // Em caso de falha na inserção
            return false;
        }
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
        // Verifica se já existe um cliente com o mesmo CPF
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM tbClientes WHERE cpf = :cpf AND id != :id");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Já existe um cliente com esse CPF
            return false;
        }

        $this->setId($id);
        $this->setNome($nome);
        $this->setCpf($cpf);
        $this->setEndereco($endereco);

        $stmt = $this->pdo->prepare("UPDATE tbClientes SET nome = :nome, cpf = :cpf, endereco = :endereco WHERE id = :id");
        $stmt->bindParam(':id', $this->getId());
        $stmt->bindParam(':nome', $this->getNome());
        $stmt->bindParam(':cpf', $this->getCpf());
        $stmt->bindParam(':endereco', $this->getEndereco());
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