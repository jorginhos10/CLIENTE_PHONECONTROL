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

                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalReparacion">
                    <i class="bi bi-phone-fill me-1"></i> Nuevo ingreso
                </button>

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

<!-- ── MODAL NUEVO INGRESO ───────────────────────────── -->
<div class="modal fade" id="modalReparacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= BASE_URL ?>/tecnicos/guardar" id="formReparacion">
                <input type="hidden" name="veterinaria_id" value="<?= $veterinaria_id ?>">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-phone-fill text-primary me-2"></i>
                        Nuevo ingreso a reparación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">

                    <!-- Cliente -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-person-fill text-primary"></i>
                        <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">Propietario del equipo</span>
                    </div>
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Selecciona el cliente <span class="text-danger">*</span></label>
                            <select class="form-select" name="cliente_id" required>
                                <option value="">Busca un cliente registrado…</option>
                                <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?><?= $c['telefono'] ? ' — ' . htmlspecialchars($c['telefono']) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                ¿No está en la lista? <a href="<?= BASE_URL ?>/clientes">Regístralo primero en Clientes</a>.
                            </div>
                        </div>
                    </div>

                    <!-- Equipo -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-phone text-primary"></i>
                        <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">Información del dispositivo</span>
                    </div>
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Categoría del dispositivo</label>
                            <input type="text" class="form-control" name="tipo_equipo" placeholder="Ej: Celular, Tablet, Consola…">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fabricante <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="marca" placeholder="Ej: Samsung, Apple…" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Modelo del equipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="modelo" placeholder="Ej: Galaxy A54, iPhone 15…" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Color del equipo</label>
                            <input type="text" class="form-control" name="color" placeholder="Ej: Negro, Blanco…">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IMEI / N.º de serie</label>
                            <input type="text" class="form-control" name="serial" placeholder="Identificador del equipo">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Clave o patrón de desbloqueo</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="clave_equipo" id="inp-clave-equipo" placeholder="PIN o contraseña alfanumérica">
                                <button type="button" class="btn btn-outline-secondary" onclick="mostrarClave()">
                                    <i class="bi bi-eye" id="icono-clave"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Falla y notas -->
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Falla reportada por el cliente <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="falla" rows="2" placeholder="Describe el problema tal como lo cuenta el cliente…" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas internas para el técnico</label>
                            <textarea class="form-control" name="observaciones" rows="2" placeholder="Daños previos, piezas faltantes, detalles solo visibles para el taller…"></textarea>
                        </div>
                    </div>

                    <!-- Accesorios -->
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label fw-semibold d-block">Accesorios entregados</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accesorios[]" value="Chip" id="acc-chip">
                                <label class="form-check-label" for="acc-chip">Chip</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accesorios[]" value="Memoria" id="acc-memoria">
                                <label class="form-check-label" for="acc-memoria">Memoria</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accesorios[]" value="Cargador" id="acc-cargador">
                                <label class="form-check-label" for="acc-cargador">Cargador</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accesorios[]" value="Forro" id="acc-forro">
                                <label class="form-check-label" for="acc-forro">Forro</label>
                            </div>
                        </div>
                    </div>

                    <!-- Finanzas -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-cash-coin text-primary"></i>
                        <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">Costos y pagos</span>
                    </div>
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Valor total de la reparación</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="costo_total" id="inp-costo-total" step="0.01" min="0" placeholder="0.00" oninput="calcularSaldo()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Adelanto recibido</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="abono" id="inp-abono" step="0.01" min="0" placeholder="0.00" oninput="calcularSaldo()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Referencia de pago</label>
                            <input type="text" class="form-control" name="referencia_pago" placeholder="Ej: Ref. transferencia #…">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Descuento aplicado</label>
                            <div class="input-group">
                                <span class="input-group-text">%</span>
                                <input type="number" class="form-control" name="descuento" id="inp-descuento" step="0.01" min="0" placeholder="0.00" oninput="calcularSaldo()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha estimada de entrega</label>
                            <input type="date" class="form-control" name="fecha_entrega_estimada">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Garantía del servicio (días)</label>
                            <input type="number" class="form-control" name="dias_garantia" min="0" value="30">
                        </div>

                        <div class="col-12">
                            <div class="alert alert-light border py-2 px-3 mb-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Saldo por cobrar</span>
                                <span class="fw-bold" id="lbl-saldo">$0.00</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-1"></i> Registrar ingreso
                    </button>
                </div>
            </form>
        </div>
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

function mostrarClave() {
    const inp  = document.getElementById('inp-clave-equipo');
    const icon = document.getElementById('icono-clave');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function calcularSaldo() {
    const costo     = parseFloat(document.getElementById('inp-costo-total').value) || 0;
    const abono     = parseFloat(document.getElementById('inp-abono').value)       || 0;
    const descuento = parseFloat(document.getElementById('inp-descuento').value)   || 0;
    const montoDescuento = costo * (descuento / 100);
    const saldo = Math.max(0, costo - montoDescuento - abono);
    document.getElementById('lbl-saldo').textContent = '$' + saldo.toFixed(2);
}
</script>
</body>
</html>
