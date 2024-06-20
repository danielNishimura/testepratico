<?php

class Produtos {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function adicionarProduto($descricao, $status, $tempoGarantia) {
        $stmt = $this->pdo->prepare('INSERT INTO tbProdutos (descricao, status, "tempoGarantia") VALUES (:descricao, :status, :tempoGarantia)');
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tempoGarantia', $tempoGarantia);
        return $stmt->execute();
    }

    public function listarProdutos() {
        $stmt = $this->pdo->prepare("SELECT * FROM tbProdutos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProduto($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tbProdutos WHERE id = :id");
        $stmt->bindParam(':id = $id');
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarProduto($id, $descricao, $status, $tempoGarantia) {
        $stmt = $this->pdo->prepare('UPDATE tbProdutos SET descricao = :descricao, status = :status, "tempoGarantia" = :tempoGarantia WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tempoGarantia', $tempoGarantia);
        return $stmt->execute();
    }

    public function deletarProduto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tbProdutos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>