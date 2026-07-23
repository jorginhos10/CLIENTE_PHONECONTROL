<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneControl &mdash; Mi perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Mi perfil</h5>
                <p class="topbar-date mb-0">Tus datos personales y contraseña</p>
            </div>
            <div class="topbar-right">
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </header>

        <main class="main-content">

            <?php if (!empty($success)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-7">

                    <!-- Datos personales -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <span class="form-section-icon bg-primary-soft text-primary"><i class="bi bi-person-fill"></i></span>
                            <div>
                                <div class="form-section-title">Datos personales</div>
                                <div class="form-section-subtitle">Tu nombre y correo de acceso</div>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/perfil/guardar">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre"
                                           value="<?= htmlspecialchars($usuarioForm['nombre'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= htmlspecialchars($usuarioForm['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <span class="badge badge-rol"><?= htmlspecialchars(ucfirst($usuarioForm['rol'] ?? '')) ?></span>
                                    <span class="text-muted small ms-1">— tu rol lo asigna un administrador en Usuarios.</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-save me-1"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Cambiar contraseña -->
                    <div class="form-section mb-4">
                        <div class="form-section-header">
                            <span class="form-section-icon bg-warning-soft text-warning"><i class="bi bi-key-fill"></i></span>
                            <div>
                                <div class="form-section-title">Cambiar contraseña</div>
                                <div class="form-section-subtitle">Necesitas tu contraseña actual para confirmar</div>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/perfil/password">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Contraseña actual <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_actual" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nueva contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_nueva" minlength="6" required>
                                    <div class="form-text">Mínimo 6 caracteres.</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-key me-1"></i> Cambiar contraseña
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
