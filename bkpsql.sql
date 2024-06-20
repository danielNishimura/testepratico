-- Adminer 4.8.1 PostgreSQL 13.15 (Debian 13.15-1.pgdg120+1) dump

DROP TABLE IF EXISTS "tbclientes";
DROP SEQUENCE IF EXISTS dbclientes_id_seq;
CREATE SEQUENCE dbclientes_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."tbclientes" (
    "id" integer DEFAULT nextval('dbclientes_id_seq') NOT NULL,
    "nome" character varying(200),
    "cpf" character varying(14),
    "endereco" text,
    CONSTRAINT "dbclientes_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "tbclientes" ("id", "nome", "cpf", "endereco") VALUES
(15,	'Ronaldo',	'55566622233',	'Rua Catarino, 55, Bairro teste, Cidade Teste'),
(7,	'Rodrigo',	'17498755009',	'Rua teste, 199, Bairro teste, Cidade Teste'),
(32,	'Eduardo',	'92853764010',	'Rua 800 teste, 800, Bairro teste, Cidade Teste'),
(25,	'Rafael',	'37978519015',	'Rua teste 6 um, 33, Bairro, Cidade'),
(1,	'Adriano',	'11233344422',	'Rua um, 33, Bairro, Cidade'),
(37,	'Henrique',	'06201708081',	'rua 2');

DROP TABLE IF EXISTS "tbordem_produto";
CREATE TABLE "public"."tbordem_produto" (
    "ordemid" integer NOT NULL,
    "produtoid" integer NOT NULL,
    CONSTRAINT "tbordem_produto_pkey" PRIMARY KEY ("ordemid", "produtoid")
) WITH (oids = false);

INSERT INTO "tbordem_produto" ("ordemid", "produtoid") VALUES
(27,	1),
(27,	3),
(27,	5),
(38,	1),
(38,	2),
(38,	3),
(41,	1),
(41,	2),
(41,	7),
(49,	20),
(50,	5);

DROP TABLE IF EXISTS "tbordens";
DROP SEQUENCE IF EXISTS tbordem_id_seq;
CREATE SEQUENCE tbordem_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."tbordens" (
    "id" integer DEFAULT nextval('tbordem_id_seq') NOT NULL,
    "dataAbertura" date NOT NULL,
    "clienteId" integer NOT NULL,
    CONSTRAINT "tbordem_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "tbordens" ("id", "dataAbertura", "clienteId") VALUES
(27,	'2024-06-01',	7),
(38,	'2024-06-30',	25),
(41,	'2025-06-21',	37),
(49,	'2024-07-06',	1),
(50,	'2024-07-02',	1);

DROP TABLE IF EXISTS "tbprodutos";
DROP SEQUENCE IF EXISTS tbprodutos_id_seq;
CREATE SEQUENCE tbprodutos_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."tbprodutos" (
    "id" integer DEFAULT nextval('tbprodutos_id_seq') NOT NULL,
    "descricao" character varying(200) NOT NULL,
    "status" character varying(100) NOT NULL,
    "tempoGarantia" character varying(3) NOT NULL,
    "sku" character varying(10),
    CONSTRAINT "tbprodutos_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "tbprodutos" ("id", "descricao", "status", "tempoGarantia", "sku") VALUES
(2,	'Fogão',	'Ativo',	'30',	'0000000001'),
(3,	'Ventilador',	'Ativo',	'360',	'0000000002'),
(1,	'Geladeira',	'Ativo',	'36',	'0000000006'),
(7,	'Lava louças',	'Ativo',	'36',	'0000000003'),
(8,	'Televisão',	'Ativo',	'36',	'0000000004'),
(19,	'Monitor',	'Ativo',	'36',	'0000000005'),
(5,	'Freezer',	'Ativo',	'36',	'0000000007'),
(20,	'Coifa',	'Ativo',	'36',	'0000000008');

ALTER TABLE ONLY "public"."tbordem_produto" ADD CONSTRAINT "tbordem_produto_ordemid_fkey" FOREIGN KEY (ordemid) REFERENCES tbordens(id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."tbordem_produto" ADD CONSTRAINT "tbordem_produto_produtoid_fkey" FOREIGN KEY (produtoid) REFERENCES tbprodutos(id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."tbordens" ADD CONSTRAINT "tbordens_clienteId_fkey" FOREIGN KEY ("clienteId") REFERENCES tbclientes(id) NOT DEFERRABLE;

-- 2024-06-20 20:26:03.036002+00