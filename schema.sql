-- crudfetch/schema.sql
CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(64) NOT NULL,
  producto VARCHAR(255) NOT NULL,
  precio DECIMAL(12,2) NOT NULL,
  cantidad INT NOT NULL,
  UNIQUE KEY uk_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
