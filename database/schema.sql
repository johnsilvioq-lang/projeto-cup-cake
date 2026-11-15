-- Banco de dados da Dulcen Cup Cakes (MySQL / MariaDB)
-- Importe este arquivo no phpMyAdmin ou rode: mysql -u root -p < database/schema.sql
--
-- Uma coluna para cada campo do formulário de cadastro.
-- "Confirmar e-mail" e "Confirmar senha" são só conferência na tela e não são gravados.
-- A senha nunca é guardada em texto puro: apenas o hash gerado por password_hash().

CREATE DATABASE IF NOT EXISTS dulcen
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dulcen;

CREATE TABLE IF NOT EXISTS usuarios (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome_completo    VARCHAR(120) NOT NULL,           -- campo "Nome completo"
  data_nascimento  DATE         NOT NULL,           -- campo "Data de nascimento"
  cpf              CHAR(11)     NOT NULL,           -- campo "CPF" (somente dígitos)
  email            VARCHAR(160) NOT NULL,           -- campo "E-mail" (minúsculo)
  telefone         VARCHAR(11)  NOT NULL,           -- campo "Telefone" (somente dígitos, com DDD)
  rua              VARCHAR(120) NOT NULL,           -- campo "Rua"
  bairro           VARCHAR(80)  NOT NULL,           -- campo "Bairro"
  numero_casa      VARCHAR(10)  NOT NULL,           -- campo "Número"
  senha_hash       VARCHAR(255) NOT NULL,           -- campo "Senha" (hash)
  criado_em        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_cpf (cpf),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
