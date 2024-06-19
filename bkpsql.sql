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
(1,	'Adriano',	'11233344422',	'Rua um, 33, Bairro, Cidade'),
(3,	'Luis',	'22233344133',	'Rua um, 55, Bairro, Cidade'),
(7,	'Teste Telecontrol',	'17498755009',	'Rua teste, 199, Bairro teste, Cidade Teste'),
(8,	'Felipe',	'60308107071',	'Rua teste, 555, Bairro teste de Marilia, Cidade Teste'),
(12,	'Adriano',	'11233344455',	'Rua um, 33, Bairro, Cidade'),
(13,	'Teste Telecontrol',	'11233344444',	'Rua teste, 555, Bairro teste de Marilia, Cidade Teste'),
(15,	'Ronaldo',	'55566622233',	'Rua Catarino, 55, Bairro teste, Cidade Teste'),
(20,	'Teste Telecontrol',	'07217618010',	'Rua teste, 199, Bairro teste, Cidade Teste'),
(22,	'3Teste Telecontrol',	'93837554074',	'Rua teste, 555, Bairro teste de Marilia, Cidade Teste'),
(23,	'4Teste Telecontrol',	'62449514090',	'Rua teste 4 um, 33, Bairro, Cidade'),
(24,	'5Teste Telecontrol',	'02823523090',	'Rua teste 5 um, 33, Bairro, Cidade'),
(25,	'6Teste Telecontrol',	'37978519015',	'Rua teste 6 um, 33, Bairro, Cidade');

DROP TABLE IF EXISTS "tbordem_produto";
CREATE TABLE "public"."tbordem_produto" (
    "ordemid" integer NOT NULL,
    "produtoid" integer NOT NULL,
    CONSTRAINT "tbordem_produto_pkey" PRIMARY KEY ("ordemid", "produtoid")
) WITH (oids = false);

INSERT INTO "tbordem_produto" ("ordemid", "produtoid") VALUES
(26,	1),
(26,	2),
(27,	1),
(27,	3),
(27,	5),
(33,	1),
(37,	1),
(38,	1),
(38,	2),
(38,	3);

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
(26,	'2024-06-18',	1),
(27,	'2024-06-01',	7),
(33,	'2024-06-19',	20),
(37,	'2024-06-24',	24),
(38,	'2024-06-30',	25);

DROP TABLE IF EXISTS "tbprodutos";
DROP SEQUENCE IF EXISTS tbprodutos_id_seq;
CREATE SEQUENCE tbprodutos_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."tbprodutos" (
    "id" integer DEFAULT nextval('tbprodutos_id_seq') NOT NULL,
    "descricao" character varying(200) NOT NULL,
    "status" character varying(100) NOT NULL,
    "tempoGarantia" character varying(3) NOT NULL,
    CONSTRAINT "tbprodutos_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "tbprodutos" ("id", "descricao", "status", "tempoGarantia") VALUES
(1,	'Geladeira',	'Ativo',	'36'),
(2,	'Fogão',	'Ativo',	'30'),
(3,	'Ventilador',	'Ativo',	'360'),
(5,	'Coifa',	'Ativo',	'36');

ALTER TABLE ONLY "public"."tbordem_produto" ADD CONSTRAINT "tbordem_produto_ordemid_fkey" FOREIGN KEY (ordemid) REFERENCES tbordens(id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."tbordem_produto" ADD CONSTRAINT "tbordem_produto_produtoid_fkey" FOREIGN KEY (produtoid) REFERENCES tbprodutos(id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."tbordens" ADD CONSTRAINT "tbordens_clienteId_fkey" FOREIGN KEY ("clienteId") REFERENCES tbclientes(id) NOT DEFERRABLE;

-- 2024-06-19 17:08:55.305419+00