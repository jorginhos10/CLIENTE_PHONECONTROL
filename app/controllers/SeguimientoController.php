<?php

require_once ROOT . '/app/models/ReparacionModel.php';

// Controlador público: NO requiere autenticación. Solo expone datos
// de una reparación puntual a quien tenga el token (enlace compartido
// con el cliente), nunca listados ni datos sensibles internos.
class SeguimientoController {

    private ReparacionModel $model;

    public function __construct() {
        $this->model = new ReparacionModel();
    }

    public function ver(): void {
        $token = trim($_GET['token'] ?? '');
        $reparacion = $token !== '' ? $this->model->findByToken($token) : null;

        $this->render('seguimiento/index', [
            'reparacion' => $reparacion,
        ]);
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require ROOT . '/app/views/' . $vista . '.php';
    }
}
