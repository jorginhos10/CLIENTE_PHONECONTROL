<?php

require_once ROOT . '/app/models/ReparacionModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';

class TecnicoController {

    private ReparacionModel  $model;
    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model    = new ReparacionModel();
        $this->vetModel = new VeterinariaModel();
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

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('tecnicos');
        }

        $cuenta_id       = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinaria_id  = (int)($_POST['veterinaria_id'] ?? 0);
        $cliente_nombre  = trim($_POST['cliente_nombre'] ?? '');
        $cliente_telefono = trim($_POST['cliente_telefono'] ?? '');
        $modelo          = trim($_POST['modelo'] ?? '');
        $falla           = trim($_POST['falla'] ?? '');

        if (!$this->vetModel->findById($veterinaria_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'Selecciona una sucursal válida.';
            $this->redirect('tecnicos');
        }

        if (empty($cliente_nombre) || empty($modelo) || empty($falla)) {
            $_SESSION['flash_error'] = 'Cliente, modelo y falla son obligatorios.';
            $this->redirect('tecnicos');
        }

        $ok = $this->model->crear([
            'cuenta_id'        => $cuenta_id,
            'veterinaria_id'   => $veterinaria_id,
            'cliente_nombre'   => $cliente_nombre,
            'cliente_telefono' => $cliente_telefono,
            'modelo'           => $modelo,
            'falla'            => $falla,
            'usuario_id'       => (int)($_SESSION['usuario_id'] ?? 0),
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
