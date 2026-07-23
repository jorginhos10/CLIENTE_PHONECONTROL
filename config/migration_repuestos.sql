CREATE TABLE IF NOT EXISTS repuestos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(150) NOT NULL,
    codigo         VARCHAR(50)  DEFAULT '',
    codigo_barras  VARCHAR(50)  DEFAULT NULL,
    descripcion    VARCHAR(255) DEFAULT '',
    imagen         VARCHAR(255) DEFAULT NULL,
    categoria_id   INT DEFAULT NULL,
    unidad         VARCHAR(30)  NOT NULL DEFAULT 'unidad',
    precio_compra  DECIMAL(10,2) NOT NULL DEFAULT 0,
    precio_venta   DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock          INT NOT NULL DEFAULT 0,
    stock_minimo   INT NOT NULL DEFAULT 5,
    activo         TINYINT(1) NOT NULL DEFAULT 1,
    cuenta_id      INT NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cuenta (cuenta_id),
    INDEX idx_categoria (categoria_id)
);

CREATE TABLE IF NOT EXISTS inventario_repuestos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    veterinaria_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    stock       INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_vet_repuesto (veterinaria_id, repuesto_id)
);

CREATE TABLE IF NOT EXISTS ingresos_repuestos (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    veterinaria_id     INT NOT NULL,
    proveedor          VARCHAR(150) DEFAULT NULL,
    notas              VARCHAR(255) DEFAULT '',
    total              DECIMAL(10,2) NOT NULL DEFAULT 0,
    usuario_id         INT NOT NULL,
    tipo               ENUM('compra', 'transferencia_entrada', 'transferencia_salida') NOT NULL DEFAULT 'compra',
    transferencia_ref  INT DEFAULT NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_veterinaria (veterinaria_id)
);

CREATE TABLE IF NOT EXISTS ingreso_repuestos_detalles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ingreso_id      INT NOT NULL,
    repuesto_id     INT NOT NULL,
    cantidad        INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    subtotal        DECIMAL(10,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    INDEX idx_ingreso (ingreso_id)
);
