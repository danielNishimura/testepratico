CREATE TABLE IF NOT EXISTS tbordens (
    id SERIAL PRIMARY KEY,
    dataAbertura DATE NOT NULL,
    clienteId INT NOT NULL
);

CREATE TABLE IF NOT EXISTS tbclientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    endereco VARCHAR(200) NOT NULL,
);

CREATE TABLE IF NOT EXISTS tbprodutos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    tempoGarantia VARCHAR(3) NOT NULL,
);

CREATE TABLE IF NOT EXISTS tbordem_produto (
    ordemId INT NOT NULL,
    produtoId INT NOT NULL,
    PRIMARY KEY (ordemId, produtoId),
    FOREIGN KEY (ordemId) REFERENCES tbordens(id),
    FOREIGN KEY (produtoId) REFERENCES tbprodutos(id)
);
