
CREATE DATABASE IF NOT EXISTS sabor_massa
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sabor_massa;


CREATE TABLE usuarios (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL,
  telefone    VARCHAR(20)  NOT NULL,
  pref_status TINYINT(1)   NOT NULL DEFAULT 1,
  pref_promo  TINYINT(1)   NOT NULL DEFAULT 0,
  criado_em   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;


CREATE TABLE enderecos (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario  INT UNSIGNED NOT NULL,
  apelido     VARCHAR(50)  NULL,
  cep         VARCHAR(9)   NULL,
  numero      VARCHAR(10)  NOT NULL,
  rua         VARCHAR(150) NOT NULL,
  bairro      VARCHAR(100) NOT NULL,
  cidade      VARCHAR(100) NOT NULL,
  complemento VARCHAR(150) NULL,
  principal   TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
) ENGINE=InnoDB;


CREATE TABLE produtos (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  categoria ENUM('mais-pedidos','pizzas-salgadas','pizzas-doces','bebidas') NOT NULL,
  nome      VARCHAR(100) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  preco     DECIMAL(8,2) NOT NULL,
  emoji     VARCHAR(10)  NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;


CREATE TABLE pedidos (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario      INT UNSIGNED NOT NULL,
  id_endereco     INT UNSIGNED NOT NULL,
  data_hora       DATETIME     NOT NULL,
  status          ENUM('recebido','preparo','saiu','entregue','cancelado') NOT NULL DEFAULT 'recebido',
  forma_pagamento ENUM('pix','cartao','dinheiro') NOT NULL,
  troco           VARCHAR(20)  NULL,
  observacoes     TEXT         NULL,
  taxa_entrega    DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario)  REFERENCES usuarios (id),
  FOREIGN KEY (id_endereco) REFERENCES enderecos (id)
) ENGINE=InnoDB;


CREATE TABLE itens_pedido (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_pedido      INT UNSIGNED NOT NULL,
  id_produto     INT UNSIGNED NOT NULL,
  tamanho        ENUM('media','grande','familia','unico') NOT NULL,
  quantidade     INT UNSIGNED NOT NULL,
  preco_unitario DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_pedido)  REFERENCES pedidos (id),
  FOREIGN KEY (id_produto) REFERENCES produtos (id)
) ENGINE=InnoDB;


INSERT INTO produtos (id, categoria, nome, descricao, preco, emoji) VALUES
(1,  'mais-pedidos',    'Margherita',          'Molho de tomate, mussarela, tomate fatiado, manjericão fresco e azeite.', 42.90, '🍕'),
(2,  'mais-pedidos',    'Calabresa',           'Molho de tomate, mussarela, calabresa fatiada, cebola roxa e orégano.',   44.90, '🍕'),
(3,  'mais-pedidos',    'Quatro Queijos',      'Mussarela, provolone, parmesão e catupiry original sobre molho de tomate.', 46.90, '🧀'),
(4,  'pizzas-salgadas', 'Portuguesa',          'Presunto, ovo, cebola, ervilha, azeitonas pretas e mussarela.',            46.90, '🍕'),
(5,  'pizzas-salgadas', 'Frango com Catupiry', 'Frango desfiado temperado, catupiry cremoso, milho e mussarela.',          47.90, '🍕'),
(10, 'bebidas',         'Coca-Cola 2L',        'Refrigerante de cola, garrafa de 2 litros.',                               12.00, '🥤');

INSERT INTO usuarios (id, nome, email, telefone, pref_status, pref_promo, criado_em) VALUES
(1, 'Maria Silva', 'maria.silva@email.com', '(19) 99999-1234', 1, 0, '2026-01-01 00:00:00');

INSERT INTO enderecos (id, id_usuario, apelido, cep, numero, rua, bairro, cidade, complemento, principal) VALUES
(1, 1, 'Casa',     NULL, '123', 'Rua das Pizzas',    'Centro',        'Santa Bárbara d''Oeste', NULL, 1),
(2, 1, 'Trabalho', NULL, '450', 'Av. Monte Castelo', 'Jardim Europa', 'Santa Bárbara d''Oeste', NULL, 0);

INSERT INTO pedidos (id, id_usuario, id_endereco, data_hora, status, forma_pagamento, troco, observacoes, taxa_entrega) VALUES
(1019, 1, 1, '2026-10-25 21:03:00', 'cancelado', 'dinheiro', NULL, NULL, 7.00),
(1031, 1, 1, '2026-11-02 20:15:00', 'entregue',  'cartao',   NULL, NULL, 7.00),
(1048, 1, 1, '2026-11-11 19:42:00', 'preparo',   'pix',      NULL, NULL, 7.00);

INSERT INTO itens_pedido (id_pedido, id_produto, tamanho, quantidade, preco_unitario) VALUES
(1019, 5,  'grande', 1, 47.90),
(1031, 3,  'grande', 1, 46.90),
(1031, 4,  'grande', 1, 46.90),
(1031, 10, 'unico',  1, 12.00),
(1048, 1,  'grande', 1, 42.90),
(1048, 10, 'unico',  1, 12.00);