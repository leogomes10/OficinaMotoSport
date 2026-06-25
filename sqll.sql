CREATE DATABASE IF NOT EXISTS oficina_motos;
USE oficina_motos;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS pecas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_peca VARCHAR(100) NOT NULL,
    marca VARCHAR(50),
    modelo_moto VARCHAR(50),
    preco_venda DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL
);

CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    peca_id INT NOT NULL,
    quantidade INT NOT NULL,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (peca_id) REFERENCES pecas(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha, nivel_acesso) 
VALUES ('Administrador', 'admin@oficina.com', '$2y$10$8W3Y6D7b1g6XQeIuHw5r8O7k6F3uYvXJ0u6eYh9z8w7q6e5r4t3y2', 'Administrador')
ON DUPLICATE KEY UPDATE id=id;