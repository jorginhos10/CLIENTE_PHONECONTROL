CREATE TABLE IF NOT EXISTS reparaciones (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    cuenta_id        INT NOT NULL,
    veterinaria_id   INT NOT NULL,
    cliente_nombre   VARCHAR(150) NOT NULL,
    cliente_telefono VARCHAR(30) DEFAULT '',
    modelo           VARCHAR(150) NOT NULL,
    falla            VARCHAR(255) NOT NULL,
    estado           ENUM('pendiente', 'en_reparacion', 'listo', 'entregado') NOT NULL DEFAULT 'pendiente',
    usuario_id       INT NOT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cuenta (cuenta_id),
    INDEX idx_veterinaria (veterinaria_id)
);
