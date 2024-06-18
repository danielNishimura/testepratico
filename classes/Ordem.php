<?php

class Ordem {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function adicionarOrdem($dataAbertura, $clienteId) {
        $stmt = $this->pdo->prepare('INSERT INTO "tbordens" ("dataAbertura", "clienteId") VALUES (:dataAbertura, :clienteId)');
        $stmt->bindParam(':dataAbertura', $dataAbertura);
        $stmt->bindParam(':clienteId', $clienteId);
        $stmt->execute();
        
        $ordemId = $this->pdo->lastInsertId();
        return $ordemId;
    }

    public function adicionarProdutoOrdem($ordemid, $produtoid) {
        // Verifica se a combinação já existe
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM "tbordem_produto" WHERE "ordemid" = :ordemid AND "produtoid" = :produtoid');
        $stmt->bindParam(':ordemid', $ordemid);
        $stmt->bindParam(':produtoid', $produtoid);
        $stmt->execute();

        if ($stmt->fetchColumn() == 0) {
            // Insere apenas se a combinação não existir
            $stmt = $this->pdo->prepare('INSERT INTO "tbordem_produto" ("ordemid", "produtoid") VALUES (:ordemid, :produtoid)');
            $stmt->bindParam(':ordemid', $ordemid);
            $stmt->bindParam(':produtoid', $produtoid );
            $stmt->execute();
        } else {
            echo 'Essa ordem já existe.';
        }
    }

    public function listarOrdem() {
        $stmt = $this->pdo->prepare('SELECT o.*, c.nome as "clienteNome", c.cpf as "clienteCpf" FROM "tbordens" o JOIN "tbclientes" c ON o."clienteId" = c."id"');
        $stmt->execute();
        $ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ordens as &$ordem) {
            $ordem['produtos'] = $this->listarProdutosOrdem($ordem['id']);
        }

        return $ordens;
    }

    public function listarProdutosOrdem($ordemId) {
        $stmt = $this->pdo->prepare('SELECT p.* FROM "tbprodutos" p JOIN "tbordem_produto" op ON p."id" = op."produtoid" WHERE op."ordemid" = :ordemid');
        $stmt->bindParam(':ordemid', $ordemId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarOrdem($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM "tbordens" WHERE "id" = :id');
        $stmt->bindParam(':id = $id');
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarOrdem($id, $dataAbertura, $clienteId) {
        $stmt = $this->pdo->prepare('UPDATE "tbordens" SET "dataAbertura" = :dataAbertura, "clienteId" = :clienteId WHERE "id" = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dataAbertura', $dataAbertura);
        $stmt->bindParam(':clienteId', $clienteId);
        return $stmt->execute();
    }

    public function deletarOrdem($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tbordens WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>