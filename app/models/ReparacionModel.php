<?php

require_once ROOT . '/config/Database.php';

class ReparacionModel {

    private mysqli $db;

    private const ORDEN_ESTADOS = ['pendiente', 'en_reparacion', 'listo', 'entregado'];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM reparaciones
             WHERE veterinaria_id = ?
             ORDER BY (estado = 'entregado') ASC, created_at DESC"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*)                          AS total,
                SUM(estado = 'pendiente')         AS total_pendientes,
                SUM(estado = 'en_reparacion')      AS total_en_reparacion,
                SUM(estado = 'listo')             AS total_listos,
                SUM(estado = 'entregado')         AS total_entregados
             FROM reparaciones WHERE veterinaria_id = ?"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function crear(array $d): bool {
        $token = bin2hex(random_bytes(20));

        $stmt = $this->db->prepare(
            'INSERT INTO reparaciones (
                token, cuenta_id, veterinaria_id, cliente_id, cliente_nombre, cliente_telefono,
                tipo_equipo, marca, modelo, color, serial, clave_equipo,
                falla, observaciones, accesorios,
                costo_total, abono, descuento, referencia_pago, fecha_entrega_estimada, dias_garantia,
                usuario_id
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('siiisssssssssssdddssii',
            $token, $d['cuenta_id'], $d['veterinaria_id'], $d['cliente_id'], $d['cliente_nombre'], $d['cliente_telefono'],
            $d['tipo_equipo'], $d['marca'], $d['modelo'], $d['color'], $d['serial'], $d['clave_equipo'],
            $d['falla'], $d['observaciones'], $d['accesorios'],
            $d['costo_total'], $d['abono'], $d['descuento'], $d['referencia_pago'], $d['fecha_entrega_estimada'], $d['dias_garantia'],
            $d['usuario_id']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function findByToken(string $token): ?array {
        $stmt = $this->db->prepare('SELECT * FROM reparaciones WHERE token = ?');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function siguienteEstado(string $estado): ?string {
        $pos = array_search($estado, self::ORDEN_ESTADOS, true);
        if ($pos === false || $pos === count(self::ORDEN_ESTADOS) - 1) {
            return null;
        }
        return self::ORDEN_ESTADOS[$pos + 1];
    }

    public function avanzarEstado(int $id, int $veterinaria_id): bool {
        $stmt = $this->db->prepare('SELECT estado FROM reparaciones WHERE id = ? AND veterinaria_id = ?');
        $stmt->bind_param('ii', $id, $veterinaria_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return false;
        }

        $nuevo = $this->siguienteEstado($row['estado']);
        if ($nuevo === null) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE reparaciones SET estado = ? WHERE id = ? AND veterinaria_id = ?');
        $stmt->bind_param('sii', $nuevo, $id, $veterinaria_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar(int $id, int $veterinaria_id): bool {
        $stmt = $this->db->prepare('DELETE FROM reparaciones WHERE id = ? AND veterinaria_id = ?');
        $stmt->bind_param('ii', $id, $veterinaria_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
