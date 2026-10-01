CREATE DATABASE cuentas_claras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cuentas_claras;

CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    tipo   ENUM('ingreso', 'gasto') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE movimientos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT UNSIGNED NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    importe      DECIMAL(10,2) NOT NULL,
    fecha        DATE NOT NULL,
    descripcion  VARCHAR(255) NULL,
    creado_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_usuario   FOREIGN KEY (usuario_id)   REFERENCES usuarios(id)   ON DELETE CASCADE,
    CONSTRAINT fk_mov_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    INDEX idx_usuario_fecha (usuario_id, fecha)
) ENGINE=InnoDB;

INSERT INTO categorias (nombre, tipo) VALUES
('Nómina', 'ingreso'), ('Otros ingresos', 'ingreso'),
('Comida', 'gasto'), ('Transporte', 'gasto'), ('Ocio', 'gasto'),
('Facturas', 'gasto'), ('Vivienda', 'gasto'), ('Salud', 'gasto'), ('Otros gastos', 'gasto');
