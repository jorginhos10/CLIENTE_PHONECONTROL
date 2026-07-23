-- Token público único por reparación, usado para el enlace de
-- seguimiento que se le comparte al cliente (sin necesidad de login).
ALTER TABLE reparaciones
    ADD COLUMN token VARCHAR(40) NULL AFTER id,
    ADD UNIQUE INDEX idx_token (token);
