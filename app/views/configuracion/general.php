<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneControl &mdash; Configuración general</title>
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
                <h5 class="topbar-title mb-0">Configuración general</h5>
                <p class="topbar-date mb-0">Nombre y datos generales de tu negocio</p>
            </div>
            <div class="topbar-right">
                <a href="<?= BASE_URL ?>/configuracion" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>

                <div class="dropdown">
                    <button class="btn btn-link topbar-avatar-btn p-0" data-bs-toggle="dropdown">
                        <div class="avatar-md"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <div class="dropdown-header">
                                <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></div>
                                <span class="badge badge-rol mt-1"><?= htmlspecialchars(ucfirst($usuario['rol'])) ?></span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"
                               onclick="return confirm('¿Cerrar sesión?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
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
                    <div class="card border-0 shadow-sm" style="border-radius:1rem;">
                        <div class="card-body p-4">

                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="cfg-item-icon" style="background:#eef2ff; width:52px; height:52px; border-radius:.9rem; display:flex; align-items:center; justify-content:center;">
                                    <i class="bi bi-building-fill" style="color:#4f46e5; font-size:1.4rem;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Datos del negocio</h5>
                                    <p class="text-muted small mb-0">Este nombre identifica tu cuenta en todo el sistema.</p>
                                </div>
                            </div>

                            <form method="POST" action="<?= BASE_URL ?>/configuracion/general/guardar">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nombre del negocio <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre"
                                           value="<?= htmlspecialchars($cuenta['nombre'] ?? '') ?>"
                                           placeholder="Ej: Mi Taller de Reparaciones" required>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="bi bi-save me-1"></i> Guardar cambios
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>/configuracion/sucursales" class="text-decoration-none small">
                            <i class="bi bi-diagram-3-fill me-1"></i>¿Buscas los datos de una sucursal? Gestiónalos aquí
                        </a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
