-- Banco separado usado pelos testes de integração e Feature (PHPUnit),
-- para que a suíte nunca toque nos dados de desenvolvimento.
CREATE DATABASE IF NOT EXISTS cuidar_plus_teste CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON cuidar_plus_teste.* TO 'cuidar'@'%';
FLUSH PRIVILEGES;
