<?php

require_once ROOT . '/app/models/IngresoRepuestoModel.php';
require_once ROOT . '/app/models/ProveedorModel.php';

class IngresoRepuestoController {

    private IngresoRepuestoModel $model;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model = new IngresoRepuestoModel();
    }

    public function index(): void {
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinarias   = $this->model->getVeterinarias($cuenta_id);
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);

        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'     => 'ingresos-repuestos',
            'ingresos'       => $this->model->getAll($veterinaria_id),
            'totales'        => $this->model->getTotales($veterinaria_id),
            'productos'      => $this->model->getRepuestos($cuenta_id, $veterinaria_id),
            'proveedores'    => (new ProveedorModel())->getAll($cuenta_id),
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('ingresos_repuestos/index', $datos);
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ingresos-repuestos');
        }

        $veterinaria_id = (int)($_POST['veterinaria_id'] ?? 0);
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $vetQuery       = $veterinaria_id > 0 ? '?vet=' . $veterinaria_id : '';

        $ids        = $_POST['producto_id']     ?? [];
        $cantidades = $_POST['cantidad']        ?? [];
        $precios    = $_POST['precio_unitario'] ?? [];

        $lineas = [];
        $total  = 0;

        foreach ($ids as $i => $rid) {
            $rid   = (int)$rid;
            $cant  = (int)($cantidades[$i] ?? 0);
            $precio = (float)($precios[$i] ?? 0);

            if ($rid > 0 && $cant > 0) {
                $lineas[] = [
                    'producto_id'     => $rid,
                    'cantidad'        => $cant,
                    'precio_unitario' => $precio,
                ];
                $total += $cant * $precio;
            }
        }

        $vetValida = false;
        foreach ($this->model->getVeterinarias($cuenta_id) as $v) {
            if ((int)$v['id'] === $veterinaria_id) { $vetValida = true; break; }
        }
        if ($veterinaria_id <= 0 || !$vetValida) {
            $_SESSION['flash_error'] = 'Debes seleccionar una sucursal.';
            $this->redirect('ingresos-repuestos' . $vetQuery);
        }

        if (empty($lineas)) {
            $_SESSION['flash_error'] = 'Debes agregar al menos un repuesto al ingreso.';
            $this->redirect('ingresos-repuestos' . $vetQuery);
        }

        $cabecera = [
            'veterinaria_id' => $veterinaria_id,
            'proveedor'      => trim($_POST['proveedor'] ?? ''),
            'notas'          => trim($_POST['notas']     ?? ''),
            'total'          => $total,
            'usuario_id'     => (int)($_SESSION['usuario_id'] ?? 0),
        ];

        if ($this->model->crear($cabecera, $lineas)) {
            $n = count($lineas);
            $_SESSION['flash_success'] = "Ingreso registrado con $n repuesto(s). Stock actualizado.";
        } else {
            $_SESSION['flash_error'] = 'Error al registrar el ingreso.';
        }

        $this->redirect('ingresos-repuestos' . $vetQuery);
    }

    public function detalle(): void {
        $id = (int)($_GET['id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($this->model->getDetalles($id));
        exit;
    }

    public function eliminar(): void {
        $id  = (int)($_GET['id']  ?? 0);
        $vet = (int)($_GET['vet'] ?? 0);

        if ($id > 0) {
            $this->model->eliminar($id);
            $_SESSION['flash_success'] = 'Ingreso eliminado y stock revertido.';
        }

        $query = $vet > 0 ? '?vet=' . $vet : '';
        $this->redirect('ingresos-repuestos' . $query);
    }

    private function requiereAutenticacion(): void {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    private function redirect(string $ruta): void {
        header('Location: ' . BASE_URL . '/' . $ruta);
        exit;
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require ROOT . '/app/views/' . $vista . '.php';
    }
}
