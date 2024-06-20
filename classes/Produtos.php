<?php

class Produtos {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function adicionarProduto($descricao, $status, $tempoGarantia, $sku) {

        // Verifica se já existe um produto com o mesmo SKU
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM tbProdutos WHERE sku = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Já existe um produto com esse SKU
            return false;
        }

        $stmt = $this->pdo->prepare('INSERT INTO tbProdutos (descricao, status, "tempoGarantia", sku) VALUES (:descricao, :status, :tempoGarantia, :sku)');
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tempoGarantia', $tempoGarantia);
        $stmt->bindParam(':sku', $sku);
        return $stmt->execute();
    }

    public function listarProdutos() {
        $stmt = $this->pdo->prepare("SELECT * FROM tbProdutos ORDER By descricao");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProduto($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tbProdutos WHERE id = :id");
        $stmt->bindParam(':id = $id');
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarProduto($id, $descricao, $status, $tempoGarantia, $sku) {
        $stmt = $this->pdo->prepare('UPDATE tbProdutos SET descricao = :descricao, status = :status, "tempoGarantia" = :tempoGarantia, sku = :sku WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tempoGarantia', $tempoGarantia);
        $stmt->bindParam(':sku', $sku);
        return $stmt->execute();
    }

    public function deletarProduto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tbProdutos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>