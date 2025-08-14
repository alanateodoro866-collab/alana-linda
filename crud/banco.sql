-- Crie o banco
CREATE DATABASE IF NOT EXISTS marista_space
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE marista_space;

-- Usuários (login)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reservas (agenda)
CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sala VARCHAR(100) NOT NULL,
  data DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fim TIME NOT NULL,
  usuario_id INT NOT NULL,
  status ENUM('reservado','cancelado') DEFAULT 'reservado',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Opcional: uma sala de exemplo
INSERT INTO reservas (sala, data, hora_inicio, hora_fim, usuario_id, status)
VALUES ('LABORATÓRIO DE INFORMÁTICA', DATE_ADD(CURDATE(), INTERVAL (2 - WEEKDAY(CURDATE())) DAY), '12:15:00', '13:05:00', 1, 'reservado');
