<?php

require_once ROOT . '/config/Database.php';

class IngresoRepuestoModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── LISTADOS ──────────────────────────────────────────

    public function getAll(int $veterinaria_id = 0): array {
        if ($veterinaria_id > 0) {
            $stmt = $this->db->prepare(
                'SELECT i.*, v.nombre AS veterinaria_nombre,
                        COUNT(d.id)     AS total_lineas,
                        SUM(d.cantidad) AS total_unidades
                 FROM ingresos_repuestos i
                 JOIN veterinarias v ON v.id = i.veterinaria_id
                 LEFT JOIN ingreso_repuestos_detalles d ON d.ingreso_id = i.id
                 WHERE i.veterinaria_id = ?
                 GROUP BY i.id ORDER BY i.created_at DESC'
            );
            $stmt->bind_param('i', $veterinaria_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $result = $this->db->query(
                'SELECT i.*, v.nombre AS veterinaria_nombre,
                        COUNT(d.id)     AS total_lineas,
                        SUM(d.cantidad) AS total_unidades
                 FROM ingresos_repuestos i
                 JOIN veterinarias v ON v.id = i.veterinaria_id
                 LEFT JOIN ingreso_repuestos_detalles d ON d.ingreso_id = i.id
                 GROUP BY i.id ORDER BY i.created_at DESC'
            );
        }
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDetalles(int $ingreso_id): array {
        $stmt = $this->db->prepare(
            'SELECT d.*, r.nombre AS producto_nombre, r.codigo, r.unidad
             FROM ingreso_repuestos_detalles d
             JOIN repuestos r ON r.id = d.repuesto_id
             WHERE d.ingreso_id = ?'
        );
        $stmt->bind_param('i', $ingreso_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(int $veterinaria_id = 0): array {
        $where  = $veterinaria_id > 0 ? "WHERE i.veterinaria_id = $veterinaria_id" : '';
        $result = $this->db->query(
            "SELECT
                COUNT(DISTINCT i.id)                                             AS total_ingresos,
                COALESCE(SUM(d.cantidad), 0)                                     AS total_unidades,
                COALESCE(SUM(i.total), 0)                                        AS valor_total,
                COALESCE(SUM(CASE WHEN MONTH(i.created_at) = MONTH(NOW())
                                   AND YEAR(i.created_at)  = YEAR(NOW())
                              THEN i.total ELSE 0 END), 0)                       AS valor_mes
             FROM ingresos_repuestos i
             LEFT JOIN ingreso_repuestos_detalles d ON d.ingreso_id = i.id
             $where"
        );
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function getVeterinarias(int $cuenta_id): array {
        $stmt = $this->db->prepare('SELECT id, nombre FROM veterinarias WHERE activo = 1 AND cuenta_id = ? ORDER BY nombre ASC');
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getRepuestos(int $cuenta_id, int $veterinaria_id = 0): array {
        if ($veterinaria_id > 0) {
            $stmt = $this->db->prepare(
                'SELECT r.id, r.nombre, r.codigo, r.unidad, r.precio_compra, r.precio_venta,
                        COALESCE(inv.stock, 0) AS stock
                 FROM repuestos r
                 LEFT JOIN inventario_repuestos inv ON inv.repuesto_id = r.id
                     AND inv.veterinaria_id = ?
                 WHERE r.activo = 1 AND r.cuenta_id = ?
                 ORDER BY r.nombre ASC'
            );
            $stmt->bind_param('ii', $veterinaria_id, $cuenta_id);
        } else {
            $stmt = $this->db->prepare(
                'SELECT id, nombre, codigo, unidad, precio_compra, precio_venta, stock
                 FROM repuestos WHERE activo = 1 AND cuenta_id = ? ORDER BY nombre ASC'
            );
            $stmt->bind_param('i', $cuenta_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ── ESCRITURA ─────────────────────────────────────────

    private function upsertStock(int $veterinaria_id, int $repuesto_id, int $cantidad): void {
        $this->db->query(
            "INSERT INTO inventario_repuestos (veterinaria_id, repuesto_id, stock)
             VALUES ($veterinaria_id, $repuesto_id, $cantidad)
             ON DUPLICATE KEY UPDATE stock = stock + $cantidad"
        );
    }

    private function decrementarStock(int $veterinaria_id, int $repuesto_id, int $cantidad): void {
        $this->db->query(
            "INSERT INTO inventario_repuestos (veterinaria_id, repuesto_id, stock)
             VALUES ($veterinaria_id, $repuesto_id, 0)
             ON DUPLICATE KEY UPDATE stock = GREATEST(0, stock - $cantidad)"
        );
    }

    public function transferir(int $origen_id, int $destino_id, array $lineas, int $usuario_id): bool {
        if (empty($lineas) || $origen_id === $destino_id) return false;

        $this->db->begin_transaction();
        try {
            $total = array_sum(array_column($lineas, 'subtotal'));

            // Validar stock suficiente en origen
            foreach ($lineas as $l) {
                $rid  = (int)$l['producto_id'];
                $cant = (int)$l['cantidad'];
                $r = $this->db->query(
                    "SELECT COALESCE(stock,0) AS stock FROM inventario_repuestos
                     WHERE veterinaria_id = $origen_id AND repuesto_id = $rid"
                );
                $stockDisp = $r ? (int)$r->fetch_assoc()['stock'] : 0;
                if ($cant > $stockDisp) throw new Exception("Stock insuficiente");
            }

            // Ingreso de SALIDA en sucursal origen
            $stmtS = $this->db->prepare(
                'INSERT INTO ingresos_repuestos (veterinaria_id, proveedor, notas, total, usuario_id, tipo)
                 VALUES (?, NULL, ?, ?, ?, "transferencia_salida")'
            );
            $notaS = "Transferencia enviada a sucursal #$destino_id";
            $stmtS->bind_param('isdi', $origen_id, $notaS, $total, $usuario_id);
            $stmtS->execute();
            $id_salida = (int)$this->db->insert_id;
            $stmtS->close();

            // Ingreso de ENTRADA en sucursal destino
            $stmtE = $this->db->prepare(
                'INSERT INTO ingresos_repuestos (veterinaria_id, proveedor, notas, total, usuario_id, tipo, transferencia_ref)
                 VALUES (?, NULL, ?, ?, ?, "transferencia_entrada", ?)'
            );
            $notaE = "Transferencia recibida desde sucursal #$origen_id";
            $stmtE->bind_param('isdii', $destino_id, $notaE, $total, $usuario_id, $id_salida);
            $stmtE->execute();
            $id_entrada = (int)$this->db->insert_id;
            $stmtE->close();

            // Actualizar ref cruzada en el registro de salida
            $this->db->query("UPDATE ingresos_repuestos SET transferencia_ref = $id_entrada WHERE id = $id_salida");

            // Detalles + movimientos de stock
            $ins = $this->db->prepare(
                'INSERT INTO ingreso_repuestos_detalles (ingreso_id, repuesto_id, cantidad, precio_unitario) VALUES (?,?,?,?)'
            );
            foreach ($lineas as $l) {
                $rid  = (int)$l['producto_id'];
                $cant = (int)$l['cantidad'];
                $prec = (float)$l['precio_unitario'];

                $ins->bind_param('iiid', $id_salida, $rid, $cant, $prec);
                $ins->execute();
                $ins->bind_param('iiid', $id_entrada, $rid, $cant, $prec);
                $ins->execute();

                $this->decrementarStock($origen_id, $rid, $cant);
                $this->upsertStock($destino_id, $rid, $cant);
            }
            $ins->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function crear(array $cabecera, array $lineas): bool {
        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ingresos_repuestos (veterinaria_id, proveedor, notas, total, usuario_id)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssdsi',
                $cabecera['veterinaria_id'], $cabecera['proveedor'],
                $cabecera['notas'],          $cabecera['total'],
                $cabecera['usuario_id']
            );
            $stmt->execute();
            $ingreso_id = (int)$this->db->insert_id;
            $stmt->close();

            $ins = $this->db->prepare(
                'INSERT INTO ingreso_repuestos_detalles (ingreso_id, repuesto_id, cantidad, precio_unitario)
                 VALUES (?, ?, ?, ?)'
            );
            foreach ($lineas as $l) {
                $rid  = (int)$l['producto_id'];
                $cant = (int)$l['cantidad'];
                $prec = (float)$l['precio_unitario'];
                $ins->bind_param('iiid', $ingreso_id, $rid, $cant, $prec);
                $ins->execute();
                $this->upsertStock($cabecera['veterinaria_id'], $rid, $cant);
            }
            $ins->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function eliminar(int $id): bool {
        $r   = $this->db->query("SELECT veterinaria_id FROM ingresos_repuestos WHERE id = $id");
        $vet = (int)($r->fetch_assoc()['veterinaria_id'] ?? 0);

        foreach ($this->getDetalles($id) as $d) {
            $this->decrementarStock($vet, $d['repuesto_id'], $d['cantidad']);
        }

        $stmt = $this->db->prepare('DELETE FROM ingresos_repuestos WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
