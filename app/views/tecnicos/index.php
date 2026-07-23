<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneControl &mdash; Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <div class="main-wrapper">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Técnico</h5>
                <p class="topbar-date mb-0">Celulares en reparación</p>
            </div>
            <div class="topbar-right">
                <!-- Veterinaria -->
                <?php require ROOT . '/app/views/layouts/vet_selector.php'; ?>

                <a href="<?= BASE_URL ?>/tecnicos/nuevo" class="btn btn-primary-custom btn-sm">
                    <i class="bi bi-phone-fill me-1"></i> Nuevo ingreso
                </a>

                <div class="dropdown">
                    <button class="btn btn-link topbar-avatar-btn p-0" data-bs-toggle="dropdown">
                        <div class="avatar-md"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><div class="dropdown-header">
                            <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></div>
                        </div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"
                               onclick="return confirm('¿Cerrar sesión?')">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="main-content">

            <!-- Flash -->
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

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-secondary-soft"><i class="bi bi-phone text-secondary"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total'] ?? 0) ?></div>
                            <div class="stat-label">Ingresos totales</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass-split text-warning"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_pendientes'] ?? 0) ?></div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info-soft"><i class="bi bi-tools text-info"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_en_reparacion'] ?? 0) ?></div>
                            <div class="stat-label">En reparación</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-soft"><i class="bi bi-check-circle-fill text-success"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_listos'] ?? 0) ?></div>
                            <div class="stat-label">Listos para entregar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2 gap-2 flex-wrap">
                    <h6 class="card-title mb-0 fw-semibold">Listado de reparaciones</h6>
                    <input type="text" class="form-control form-control-sm" id="buscador"
                        placeholder="Buscar cliente, modelo, falla…" style="width:220px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaReparaciones">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Falla reportada</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reparaciones)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-phone fs-2 d-block mb-2 opacity-25"></i>
                                        No hay reparaciones registradas.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($reparaciones as $i => $r): ?>
                                <tr>
                                    <td class="ps-3 text-muted small"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($r['cliente_nombre']) ?></div>
                                        <?php if ($r['cliente_telefono']): ?>
                                        <div class="text-muted small"><?= htmlspecialchars($r['cliente_telefono']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($r['marca'] . ' ' . $r['modelo']) ?></div>
                                        <?php if (!empty($r['tipo_equipo'])): ?>
                                        <div class="text-muted small"><?= htmlspecialchars($r['tipo_equipo']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small" style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= htmlspecialchars($r['falla']) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badges = [
                                            'pendiente'     => ['bg-warning-soft text-warning',  'hourglass-split',  'Pendiente'],
                                            'en_reparacion' => ['bg-info-soft text-info',         'tools',            'En reparación'],
                                            'listo'         => ['bg-success-soft text-success',   'check-circle-fill','Listo'],
                                            'entregado'     => ['bg-secondary-soft text-secondary','box-seam-fill',   'Entregado'],
                                        ];
                                        [$clase, $icono, $texto] = $badges[$r['estado']] ?? $badges['pendiente'];
                                        ?>
                                        <span class="badge <?= $clase ?>">
                                            <i class="bi bi-<?= $icono ?> me-1"></i><?= $texto ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-muted small"><?= date('d/m/Y', strtotime($r['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <?php
                                            $siguientes = [
                                                'pendiente'     => ['tools',      'btn-outline-info',      'Iniciar reparación'],
                                                'en_reparacion' => ['check-lg',   'btn-outline-success',   'Marcar como listo'],
                                                'listo'         => ['box-arrow-up', 'btn-outline-secondary', 'Marcar como entregado'],
                                            ];
                                            ?>
                                            <?php if (isset($siguientes[$r['estado']])): [$icono2, $clase2, $titulo2] = $siguientes[$r['estado']]; ?>
                                            <a href="<?= BASE_URL ?>/tecnicos/estado?id=<?= $r['id'] ?>&vet=<?= $veterinaria_id ?>"
                                               class="btn btn-sm <?= $clase2 ?> btn-accion" title="<?= $titulo2 ?>"
                                               onclick="return confirm('¿<?= $titulo2 ?>?')">
                                                <i class="bi bi-<?= $icono2 ?>"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="<?= BASE_URL ?>/tecnicos/eliminar?id=<?= $r['id'] ?>&vet=<?= $veterinaria_id ?>"
                                               class="btn btn-sm btn-outline-danger btn-accion" title="Eliminar"
                                               onclick="return confirm('¿Eliminar este registro?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (!empty($reparaciones)): ?>
                <div class="card-footer bg-white border-0 text-muted small">
                    Total de registros: <?= count($reparaciones) ?>
                </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaReparaciones tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

</script>
</body>
</html>
