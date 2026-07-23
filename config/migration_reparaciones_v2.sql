-- Agrega a la tabla `reparaciones` ya existente las columnas nuevas
-- (cliente_id, datos del equipo, finanzas) que el migration_reparaciones.sql
-- original no pudo crear porque la tabla ya existía con el esquema viejo.
-- Confirmado por SHOW COLUMNS FROM reparaciones que ninguna de estas
-- columnas existe todavía, así que es seguro correr esto tal cual.

ALTER TABLE reparaciones
    ADD COLUMN cliente_id             INT NULL AFTER veterinaria_id,
    ADD COLUMN tipo_equipo            VARCHAR(60) DEFAULT '' AFTER cliente_telefono,
    ADD COLUMN marca                  VARCHAR(60) NOT NULL DEFAULT '' AFTER tipo_equipo,
    ADD COLUMN color                  VARCHAR(40) DEFAULT '' AFTER modelo,
    ADD COLUMN serial                 VARCHAR(100) DEFAULT '' AFTER color,
    ADD COLUMN clave_equipo           VARCHAR(100) DEFAULT '' AFTER serial,
    ADD COLUMN observaciones          TEXT NULL AFTER falla,
    ADD COLUMN accesorios             VARCHAR(255) DEFAULT '' AFTER observaciones,
    ADD COLUMN costo_total            DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER accesorios,
    ADD COLUMN abono                  DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER costo_total,
    ADD COLUMN descuento              DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER abono,
    ADD COLUMN referencia_pago        VARCHAR(100) DEFAULT '' AFTER descuento,
    ADD COLUMN fecha_entrega_estimada DATE NULL AFTER referencia_pago,
    ADD COLUMN dias_garantia          INT NOT NULL DEFAULT 30 AFTER fecha_entrega_estimada;
