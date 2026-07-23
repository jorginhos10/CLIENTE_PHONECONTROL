<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneControl &mdash; Nuevo ingreso a reparación</title>
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
                <h5 class="topbar-title mb-0">Nuevo ingreso a reparación</h5>
                <p class="topbar-date mb-0">Registra un equipo que entra al taller</p>
            </div>
            <div class="topbar-right">
                <a href="<?= BASE_URL ?>/tecnicos" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver al listado
                </a>
            </div>
        </header>

        <main class="main-content">

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-xl-9">

                    <form method="POST" action="<?= BASE_URL ?>/tecnicos/guardar" id="formReparacion">
                        <input type="hidden" name="veterinaria_id" value="<?= $veterinaria_id ?>">

                        <!-- Cliente -->
                        <div class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon bg-primary-soft text-primary"><i class="bi bi-person-fill"></i></span>
                                <div>
                                    <div class="form-section-title">Propietario del equipo</div>
                                    <div class="form-section-subtitle">Selecciona un cliente ya registrado</div>
                                </div>
                            </div>
                            <div class="row g-3">
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
                        </div>

                        <!-- Equipo -->
                        <div class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon bg-info-soft text-info"><i class="bi bi-phone"></i></span>
                                <div>
                                    <div class="form-section-title">Información del dispositivo</div>
                                    <div class="form-section-subtitle">Datos del equipo que ingresa a reparación</div>
                                </div>
                            </div>
                            <div class="row g-3">
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
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold mb-0">Clave o patrón de desbloqueo</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary active" id="btn-modo-texto" onclick="cambiarModoClave('texto')" title="Contraseña">
                                                <i class="bi bi-fonts"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="btn-modo-patron" onclick="cambiarModoClave('patron')" title="Patrón">
                                                <i class="bi bi-grid-3x3-gap-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="clave_equipo" id="inp-clave-equipo-hidden">

                                    <div id="clave-modo-texto">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="inp-clave-texto" placeholder="PIN o contraseña alfanumérica" oninput="sincronizarClaveTexto()">
                                            <button type="button" class="btn btn-outline-secondary" onclick="mostrarClave()">
                                                <i class="bi bi-eye" id="icono-clave"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="clave-modo-patron" class="d-none">
                                        <div class="text-center small text-muted mb-2" id="patron-resumen">Dibuja el patrón de desbloqueo</div>
                                        <svg id="patron-svg" viewBox="0 0 240 240" width="220" height="220"
                                             class="mx-auto d-block" style="touch-action:none; cursor:pointer; max-width:100%;"></svg>
                                        <div class="text-center mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarPatron()">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Borrar patrón
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Falla y notas -->
                        <div class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon bg-warning-soft text-warning"><i class="bi bi-chat-square-text-fill"></i></span>
                                <div>
                                    <div class="form-section-title">Diagnóstico</div>
                                    <div class="form-section-subtitle">Motivo de la visita del cliente</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Falla reportada por el cliente <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="falla" rows="2" placeholder="Describe el problema tal como lo cuenta el cliente…" required></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notas internas para el técnico</label>
                                    <textarea class="form-control" name="observaciones" rows="2" placeholder="Daños previos, piezas faltantes, detalles solo visibles para el taller…"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Accesorios -->
                        <div class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon bg-secondary-soft text-secondary"><i class="bi bi-bag-check-fill"></i></span>
                                <div>
                                    <div class="form-section-title">Accesorios entregados</div>
                                    <div class="form-section-subtitle">Elementos que el cliente trae con el equipo</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <label class="accesorio-check mb-0">
                                    <input class="form-check-input m-0" type="checkbox" name="accesorios[]" value="Chip">
                                    <span>Chip</span>
                                </label>
                                <label class="accesorio-check mb-0">
                                    <input class="form-check-input m-0" type="checkbox" name="accesorios[]" value="Memoria">
                                    <span>Memoria</span>
                                </label>
                                <label class="accesorio-check mb-0">
                                    <input class="form-check-input m-0" type="checkbox" name="accesorios[]" value="Cargador">
                                    <span>Cargador</span>
                                </label>
                                <label class="accesorio-check mb-0">
                                    <input class="form-check-input m-0" type="checkbox" name="accesorios[]" value="Forro">
                                    <span>Forro</span>
                                </label>
                            </div>
                        </div>

                        <!-- Finanzas -->
                        <div class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon bg-success-soft text-success"><i class="bi bi-cash-coin"></i></span>
                                <div>
                                    <div class="form-section-title">Costos y pagos</div>
                                    <div class="form-section-subtitle">Presupuesto, anticipo y entrega</div>
                                </div>
                            </div>
                            <div class="row g-3">
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
                                    <div class="saldo-box">
                                        <span class="text-muted small fw-semibold">Saldo por cobrar</span>
                                        <span class="saldo-valor" id="lbl-saldo">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mb-4">
                            <a href="<?= BASE_URL ?>/tecnicos" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-save me-1"></i> Registrar ingreso
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
function mostrarClave() {
    const inp  = document.getElementById('inp-clave-texto');
    const icon = document.getElementById('icono-clave');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function sincronizarClaveTexto() {
    document.getElementById('inp-clave-equipo-hidden').value = document.getElementById('inp-clave-texto').value;
}

// ── Modo texto / patrón ───────────────────────────────
function cambiarModoClave(modo) {
    const btnTexto  = document.getElementById('btn-modo-texto');
    const btnPatron = document.getElementById('btn-modo-patron');
    const divTexto  = document.getElementById('clave-modo-texto');
    const divPatron = document.getElementById('clave-modo-patron');

    if (modo === 'texto') {
        btnTexto.classList.add('active');
        btnPatron.classList.remove('active');
        divTexto.classList.remove('d-none');
        divPatron.classList.add('d-none');
        sincronizarClaveTexto();
    } else {
        btnPatron.classList.add('active');
        btnTexto.classList.remove('active');
        divPatron.classList.remove('d-none');
        divTexto.classList.add('d-none');
        if (!patronInicializado) {
            initPatron();
            patronInicializado = true;
        }
        guardarPatron();
    }
}

// ── Patrón de desbloqueo (grid 3x3 estilo Android) ────
const patronDots = [];
(function construirDots() {
    const coords = [40, 120, 200];
    let num = 1;
    for (let r = 0; r < 3; r++) {
        for (let c = 0; c < 3; c++) {
            patronDots.push({ num: num++, cx: coords[c], cy: coords[r] });
        }
    }
})();

let patronSeleccion    = [];
let patronDibujando    = false;
let patronInicializado = false;

function initPatron() {
    const svg = document.getElementById('patron-svg');
    svg.innerHTML = '';

    const gLines = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    gLines.setAttribute('id', 'patron-lines');
    svg.appendChild(gLines);

    patronDots.forEach(d => {
        const c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        c.setAttribute('cx', d.cx);
        c.setAttribute('cy', d.cy);
        c.setAttribute('r', 18);
        c.setAttribute('fill', '#e2e8f0');
        c.setAttribute('data-num', d.num);
        c.classList.add('patron-dot');
        svg.appendChild(c);

        const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        t.setAttribute('x', d.cx);
        t.setAttribute('y', d.cy + 5);
        t.setAttribute('text-anchor', 'middle');
        t.setAttribute('font-size', '13');
        t.setAttribute('font-weight', '700');
        t.setAttribute('fill', '#fff');
        t.setAttribute('pointer-events', 'none');
        t.setAttribute('data-num', d.num);
        t.classList.add('patron-orden');
        svg.appendChild(t);
    });

    svg.addEventListener('pointerdown', e => {
        patronDibujando = true;
        patronSeleccion = [];
        actualizarPatronVisual();
        manejarPuntoPatron(e);
    });
    svg.addEventListener('pointermove', e => {
        if (patronDibujando) manejarPuntoPatron(e);
    });
    window.addEventListener('pointerup', () => {
        if (patronDibujando) { patronDibujando = false; guardarPatron(); }
    });
}

function manejarPuntoPatron(e) {
    const svg     = document.getElementById('patron-svg');
    const rect    = svg.getBoundingClientRect();
    const scaleX  = 240 / rect.width;
    const scaleY  = 240 / rect.height;
    const x = (e.clientX - rect.left) * scaleX;
    const y = (e.clientY - rect.top)  * scaleY;

    patronDots.forEach(d => {
        const dist = Math.hypot(d.cx - x, d.cy - y);
        if (dist < 22 && !patronSeleccion.includes(d.num)) {
            patronSeleccion.push(d.num);
            actualizarPatronVisual();
        }
    });
}

function actualizarPatronVisual() {
    const svg = document.getElementById('patron-svg');

    svg.querySelectorAll('.patron-dot').forEach(c => {
        const activo = patronSeleccion.includes(parseInt(c.dataset.num));
        c.setAttribute('fill', activo ? '#7c3aed' : '#e2e8f0');
    });

    svg.querySelectorAll('.patron-orden').forEach(t => {
        const orden = patronSeleccion.indexOf(parseInt(t.dataset.num));
        t.textContent = orden >= 0 ? orden + 1 : '';
    });

    const gLines = document.getElementById('patron-lines');
    gLines.innerHTML = '';
    for (let i = 0; i < patronSeleccion.length - 1; i++) {
        const a = patronDots.find(d => d.num === patronSeleccion[i]);
        const b = patronDots.find(d => d.num === patronSeleccion[i + 1]);
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', a.cx);
        line.setAttribute('y1', a.cy);
        line.setAttribute('x2', b.cx);
        line.setAttribute('y2', b.cy);
        line.setAttribute('stroke', '#7c3aed');
        line.setAttribute('stroke-width', 4);
        line.setAttribute('stroke-linecap', 'round');
        gLines.appendChild(line);
    }

    document.getElementById('patron-resumen').textContent = patronSeleccion.length
        ? 'Patrón guardado: ' + patronSeleccion.join(' ')
        : 'Dibuja el patrón de desbloqueo';
}

function guardarPatron() {
    document.getElementById('inp-clave-equipo-hidden').value = patronSeleccion.join('-');
}

function limpiarPatron() {
    patronSeleccion = [];
    actualizarPatronVisual();
    guardarPatron();
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
