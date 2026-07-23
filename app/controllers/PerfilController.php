<?php

require_once ROOT . '/app/models/UsuarioModel.php';

// Autoservicio: cualquier usuario autenticado (sin importar su rol)
// puede editar su propio nombre/correo y cambiar su contraseña aquí.
// A diferencia de UsuarioController, no requiere ser admin — pero
// tampoco expone rol ni sucursal_id como editables (esos siguen
// siendo exclusivos de Usuarios, solo para administradores).
class PerfilController {

    private UsuarioModel $model;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model = new UsuarioModel();
    }

    public function index(): void {
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $usuarioDb = $this->model->findById($usuarioId, $cuenta_id);

        $datos = [
            'activePage'  => '',
            'usuarioForm' => $usuarioDb,
            'usuario'     => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('perfil/index', $datos);
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perfil');
        }

        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $nombre    = trim($_POST['nombre'] ?? '');
        $email     = trim($_POST['email']  ?? '');

        if (empty($nombre) || empty($email)) {
            $_SESSION['flash_error'] = 'El nombre y el correo son obligatorios.';
            $this->redirect('perfil');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El correo electrónico no es válido.';
            $this->redirect('perfil');
        }

        if ($this->model->emailExiste($email, $usuarioId)) {
            $_SESSION['flash_error'] = 'Ya existe un usuario con ese correo.';
            $this->redirect('perfil');
        }

        // Preservamos rol y sucursal_id actuales: este formulario nunca
        // los expone, así que no se pueden auto-escalar privilegios.
        $actual = $this->model->findById($usuarioId, $cuenta_id);
        $ok = $this->model->actualizar($usuarioId, [
            'nombre'      => $nombre,
            'email'       => $email,
            'rol'         => $actual['rol'] ?? 'recepcion',
            'sucursal_id' => $actual['sucursal_id'] ?? 0,
        ], $cuenta_id);

        if ($ok) {
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_email']  = $email;
        }

        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Perfil actualizado correctamente.'
            : 'Error al actualizar el perfil.';

        $this->redirect('perfil');
    }

    public function password(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perfil');
        }

        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $actualPw  = $_POST['password_actual'] ?? '';
        $nuevaPw   = $_POST['password_nueva']  ?? '';

        $usuario = $this->model->findById($usuarioId, $cuenta_id);

        if (!$usuario || !$this->model->verificarPassword($actualPw, $usuario['password'])) {
            $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
            $this->redirect('perfil');
        }

        if (strlen($nuevaPw) < 6) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            $this->redirect('perfil');
        }

        $ok = $this->model->cambiarPassword($usuarioId, $nuevaPw, $cuenta_id);
        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Contraseña actualizada correctamente.'
            : 'Error al actualizar la contraseña.';

        $this->redirect('perfil');
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
