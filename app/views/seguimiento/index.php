<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneControl &mdash; Seguimiento de tu reparación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <style>
        body.seg-body {
            margin: 0;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .seg-wrap { width: 100%; max-width: 480px; }
        .seg-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .seg-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), #059669);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem;
        }
        .seg-brand-name { font-weight: 800; font-size: 1.15rem; color: #1e293b; }

        .seg-card {
            background: #fff;
            border-radius: 1.1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
            padding: 1.75rem;
        }

        .seg-steps {
            display: flex;
            justify-content: space-between;
            margin: 1.25rem 0 1.5rem;
        }
        .seg-step { flex: 1; text-align: center; position: relative; }
        .seg-step .dot {
            width: 30px; height: 30px; border-radius: 50%;
            background: #e2e8f0; color: #94a3b8;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto .4rem; font-size: .95rem;
            transition: background .2s, color .2s;
        }
        .seg-step.activo .dot,
        .seg-step.hecho .dot { background: var(--primary); color: #fff; }
        .seg-step::after {
            content: '';
            position: absolute;
            top: 15px; left: 50%; width: 100%; height: 3px;
            background: #e2e8f0; z-index: 0;
        }
        .seg-step:last-child::after { display: none; }
        .seg-step.hecho::after { background: var(--primary); }
        .seg-step .lbl { font-size: .68rem; color: #64748b; font-weight: 600; position: relative; z-index: 1; }
        .seg-step .dot { position: relative; z-index: 1; }

        .seg-row {
            display: flex; justify-content: space-between; gap: 1rem;
            padding: .55rem 0; border-bottom: 1px solid #f1f5f9;
            font-size: .87rem;
        }
        .seg-row:last-child { border-bottom: none; }
        .seg-row .lbl { color: #64748b; }
        .seg-row .val { font-weight: 600; color: #1e293b; text-align: right; }
    </style>
</head>
<body class="seg-body">

<div class="seg-wrap">

    <div class="seg-brand">
        <div class="seg-brand-icon"><i class="bi bi-phone-fill"></i></div>
        <div class="seg-brand-name">PhoneControl</div>
    </div>

    <div class="seg-card">

        <?php if (!$reparacion): ?>

            <div class="text-center py-3">
                <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                <h5 class="fw-bold mt-3 mb-1">No encontramos esta reparación</h5>
                <p class="text-muted small mb-0">Verifica que el enlace esté completo o pregunta al taller por el enlace correcto.</p>
            </div>

        <?php else: ?>

            <?php
            $estados = ['pendiente', 'en_reparacion', 'listo', 'entregado'];
            $pos     = array_search($reparacion['estado'], $estados, true);
            $labels  = ['Recibido', 'En reparación', 'Listo', 'Entregado'];

            $badges = [
                'pendiente'     => ['bg-warning-soft text-warning',  'hourglass-split',   'Recibido, pendiente de revisión'],
                'en_reparacion' => ['bg-info-soft text-info',        'tools',             'Tu equipo está en reparación'],
                'listo'         => ['bg-success-soft text-success',  'check-circle-fill', 'Listo para recoger'],
                'entregado'     => ['bg-secondary-soft text-secondary','box-seam-fill',    'Entregado'],
            ];
            [$clase, $icono, $texto] = $badges[$reparacion['estado']] ?? $badges['pendiente'];

            $costo      = (float)$reparacion['costo_total'];
            $abono      = (float)$reparacion['abono'];
            $descuento  = (float)$reparacion['descuento'];
            $montoDesc  = $costo * ($descuento / 100);
            $saldo      = max(0, $costo - $montoDesc - $abono);
            ?>

            <div class="text-center mb-3">
                <span class="badge <?= $clase ?> px-3 py-2" style="font-size:.85rem;">
                    <i class="bi bi-<?= $icono ?> me-1"></i><?= htmlspecialchars($texto) ?>
                </span>
            </div>

            <div class="seg-steps">
                <?php foreach ($estados as $i => $e): ?>
                <div class="seg-step <?= $i < $pos ? 'hecho' : ($i === $pos ? 'activo' : '') ?>">
                    <div class="dot">
                        <?php if ($i < $pos): ?><i class="bi bi-check-lg"></i><?php else: ?><?= $i + 1 ?><?php endif; ?>
                    </div>
                    <div class="lbl"><?= $labels[$i] ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-2">
                <div class="seg-row">
                    <span class="lbl">Cliente</span>
                    <span class="val"><?= htmlspecialchars($reparacion['cliente_nombre']) ?></span>
                </div>
                <div class="seg-row">
                    <span class="lbl">Equipo</span>
                    <span class="val"><?= htmlspecialchars(trim($reparacion['marca'] . ' ' . $reparacion['modelo'])) ?></span>
                </div>
                <?php if (!empty($reparacion['color'])): ?>
                <div class="seg-row">
                    <span class="lbl">Color</span>
                    <span class="val"><?= htmlspecialchars($reparacion['color']) ?></span>
                </div>
                <?php endif; ?>
                <div class="seg-row">
                    <span class="lbl">Falla reportada</span>
                    <span class="val"><?= htmlspecialchars($reparacion['falla']) ?></span>
                </div>
                <?php if (!empty($reparacion['accesorios'])): ?>
                <div class="seg-row">
                    <span class="lbl">Accesorios entregados</span>
                    <span class="val"><?= htmlspecialchars(str_replace(',', ', ', $reparacion['accesorios'])) ?></span>
                </div>
                <?php endif; ?>
                <div class="seg-row">
                    <span class="lbl">Fecha de ingreso</span>
                    <span class="val"><?= date('d/m/Y', strtotime($reparacion['created_at'])) ?></span>
                </div>
                <?php if (!empty($reparacion['fecha_entrega_estimada'])): ?>
                <div class="seg-row">
                    <span class="lbl">Entrega estimada</span>
                    <span class="val"><?= date('d/m/Y', strtotime($reparacion['fecha_entrega_estimada'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($costo > 0): ?>
                <div class="seg-row">
                    <span class="lbl">Saldo pendiente</span>
                    <span class="val">$<?= number_format($saldo, 2) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($reparacion['estado'] === 'entregado' && (int)$reparacion['dias_garantia'] > 0): ?>
                <div class="seg-row">
                    <span class="lbl">Garantía del servicio</span>
                    <span class="val"><?= (int)$reparacion['dias_garantia'] ?> días desde la entrega</span>
                </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>

    <p class="text-center text-muted mt-3 mb-0" style="font-size:.72rem;">
        Enlace privado de seguimiento — no lo compartas con terceros.
    </p>

</div>

</body>
</html>
