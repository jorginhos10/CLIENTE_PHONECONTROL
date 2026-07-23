<?php

require_once ROOT . '/app/models/ReparacionModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';
require_once ROOT . '/app/models/ClienteModel.php';

class TecnicoController {

    private ReparacionModel  $model;
    private VeterinariaModel $vetModel;
    private ClienteModel     $clienteModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model        = new ReparacionModel();
        $this->vetModel     = new VeterinariaModel();
        $this->clienteModel = new ClienteModel();
    }

    public function index(): void {
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinarias   = $this->vetModel->getAll($cuenta_id);
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);
        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'     => 'tecnicos',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'reparaciones'   => $this->model->getAll($veterinaria_id),
            'totales'        => $this->model->getTotales($veterinaria_id),
            'clientes'       => $this->clienteModel->getAll($cuenta_id),
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('tecnicos/index', $datos);
    }

    public function nuevo(): void {
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinarias   = $this->vetModel->getAll($cuenta_id);
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);
        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'     => 'tecnicos',
            'veterinaria_id' => $veterinaria_id,
            'clientes'       => $this->clienteModel->getAll($cuenta_id),
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'error' => $_SESSION['flash_error'] ?? '',
        ];
        unset($_SESSION['flash_error']);

        $this->render('tecnicos/nuevo', $datos);
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('tecnicos');
        }

        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinaria_id = (int)($_POST['veterinaria_id'] ?? 0);
        $cliente_id     = (int)($_POST['cliente_id'] ?? 0);
        $marca          = trim($_POST['marca'] ?? '');
        $modelo         = trim($_POST['modelo'] ?? '');
        $falla          = trim($_POST['falla'] ?? '');

        if (!$this->vetModel->findById($veterinaria_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'Selecciona una sucursal válida.';
            $this->redirect('tecnicos/nuevo');
        }

        $cliente = $this->clienteModel->findById($cliente_id, $cuenta_id);
        if (!$cliente) {
            $_SESSION['flash_error'] = 'Selecciona un cliente válido de la lista.';
            $this->redirect('tecnicos/nuevo');
        }

        if (empty($marca) || empty($modelo) || empty($falla)) {
            $_SESSION['flash_error'] = 'Fabricante, modelo y falla son obligatorios.';
            $this->redirect('tecnicos/nuevo');
        }

        $accesorios = implode(',', array_filter((array)($_POST['accesorios'] ?? [])));
        $fechaEntrega = trim($_POST['fecha_entrega_estimada'] ?? '');

        $ok = $this->model->crear([
            'cuenta_id'              => $cuenta_id,
            'veterinaria_id'         => $veterinaria_id,
            'cliente_id'             => $cliente_id,
            'cliente_nombre'         => trim($cliente['nombre'] . ' ' . $cliente['apellido']),
            'cliente_telefono'       => $cliente['telefono'] ?? '',
            'tipo_equipo'            => trim($_POST['tipo_equipo'] ?? ''),
            'marca'                  => $marca,
            'modelo'                 => $modelo,
            'color'                  => trim($_POST['color'] ?? ''),
            'serial'                 => trim($_POST['serial'] ?? ''),
            'clave_equipo'           => trim($_POST['clave_equipo'] ?? ''),
            'falla'                  => $falla,
            'observaciones'          => trim($_POST['observaciones'] ?? ''),
            'accesorios'             => $accesorios,
            'costo_total'            => (float)($_POST['costo_total'] ?? 0),
            'abono'                  => (float)($_POST['abono'] ?? 0),
            'descuento'              => (float)($_POST['descuento'] ?? 0),
            'referencia_pago'        => trim($_POST['referencia_pago'] ?? ''),
            'fecha_entrega_estimada' => $fechaEntrega !== '' ? $fechaEntrega : null,
            'dias_garantia'          => (int)($_POST['dias_garantia'] ?? 30),
            'usuario_id'             => (int)($_SESSION['usuario_id'] ?? 0),
        ]);

        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Reparación registrada correctamente.'
            : 'Error al registrar la reparación.';

        $this->redirect('tecnicos');
    }

    public function avanzarEstado(): void {
        $id  = (int)($_GET['id']  ?? 0);
        $vet = (int)($_GET['vet'] ?? 0);

        if ($id > 0) {
            $ok = $this->model->avanzarEstado($id, $vet);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Estado actualizado.'
                : 'No se pudo actualizar el estado.';
        }

        $this->redirect('tecnicos');
    }

    public function eliminar(): void {
        $id  = (int)($_GET['id']  ?? 0);
        $vet = (int)($_GET['vet'] ?? 0);

        if ($id > 0) {
            $this->model->eliminar($id, $vet);
            $_SESSION['flash_success'] = 'Reparación eliminada.';
        }

        $this->redirect('tecnicos');
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
