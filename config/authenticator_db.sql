-- Exclui o banco de dados se ele existir
#DROP DATABASE authenticator_db;
-- Cria um novo banco de dados
CREATE DATABASE authenticator_db;
-- Seleciona o banco de dados criado
USE authenticator_db;

-- Cria a tabela users
CREATE TABLE users (
   id INT(11) PRIMARY KEY AUTO_INCREMENT,
   name VARCHAR(45),
   password VARCHAR(30),
   secret VARCHAR(80),
   type INT(12)
);