<?php
// ../views/dashboard_presidente.php
session_start();
require('../config/db.php'); // debe definir $pdo (PDO)

// seguridad: sólo Presidente General
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Presidente';

// ---------- Consultas (seguras) ----------
try {
    // Ingresos: incluir Ingreso, Donacion, Subsidio
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(monto),0) AS total_ingresos
        FROM recursos_financieros
        WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio')
    ");
    $stmt->execute();
    $totalIngresos = (float)$stmt->fetchColumn();

    // Egresos: tipo_movimiento = 'Gasto'
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(monto),0) AS total_egresos
        FROM recursos_financieros
        WHERE tipo_movimiento = 'Gasto'
    ");
    $stmt->execute();
    $totalEgresos = (float)$stmt->fetchColumn();

    // Balance
    $balance = $totalIngresos - $totalEgresos;

    // 📅 Ingresos mes actual
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(monto),0)
    FROM recursos_financieros
    WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio')
    AND MONTH(fecha) = MONTH(CURDATE())
    AND YEAR(fecha) = YEAR(CURDATE())
");
$stmt->execute();
$ingresosMesActual = (float)$stmt->fetchColumn();

// 📅 Egresos mes actual
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(monto),0)
    FROM recursos_financieros
    WHERE tipo_movimiento = 'Gasto'
    AND MONTH(fecha) = MONTH(CURDATE())
    AND YEAR(fecha) = YEAR(CURDATE())
");
$stmt->execute();
$egresosMesActual = (float)$stmt->fetchColumn();

// 📅 Ingresos mes anterior
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(monto),0)
    FROM recursos_financieros
    WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio')
    AND MONTH(fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
");
$stmt->execute();
$ingresosMesAnterior = (float)$stmt->fetchColumn();

// 📅 Egresos mes anterior
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(monto),0)
    FROM recursos_financieros
    WHERE tipo_movimiento = 'Gasto'
    AND MONTH(fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
");
$stmt->execute();
$egresosMesAnterior = (float)$stmt->fetchColumn();

// 📊 Variaciones
$varIngresos = $ingresosMesAnterior > 0 
    ? (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 
    : 0;

$varEgresos = $egresosMesAnterior > 0 
    ? (($egresosMesActual - $egresosMesAnterior) / $egresosMesAnterior) * 100 
    : 0;
  
    // Documentos por estado (normalizamos mayúsculas/minúsculas)
    $stmt = $pdo->query("
        SELECT LOWER(estado) as estado_norm, COUNT(*) AS total
        FROM documentos
        GROUP BY estado_norm
    ");
    $docGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Inicializar
    $docsData = [
        'pendiente' => 0,
        'revisado'  => 0,
        'aprobado'  => 0
    ];
    foreach ($docGroups as $r) {
        $key = $r['estado_norm'];
        if (isset($docsData[$key])) {
            $docsData[$key] = (int)$r['total'];
        } else {
            // si hay otros estados, agruparlos en 'pendiente' por defecto
            $docsData[$key] = (int)$r['total'];
        }
    }

    // Total actas
    $stmt = $pdo->query("SELECT COUNT(*) FROM actas");
    $totalActas = (int)$stmt->fetchColumn();

    //Total del Juntas
    $stmt = $pdo->query("SELECT COUNT(*) FROM juntas");
    $totalJuntas = (int)$stmt->fetchColumn();

    // Eventos próximos (count)
    $stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM agenda 
    WHERE STR_TO_DATE(fecha, '%Y-%m-%d') >= CURDATE()
");
    $stmt->execute();
    $eventosProximos = (int)$stmt->fetchColumn();

    // Últimas actividades (limit 6): combinamos documentos, actas y movimientos
    $notifs = [];
    // docs
    $stmt = $pdo->query("SELECT 'Documento' AS tipo, titulo AS item, fecha_subida AS fecha FROM documentos ORDER BY fecha_subida DESC LIMIT 3");
    $notifs = array_merge($notifs, $stmt->fetchAll(PDO::FETCH_ASSOC));
    // actas
    $stmt = $pdo->query("SELECT 'Acta' AS tipo, titulo AS item, fecha_reunion AS fecha FROM actas ORDER BY fecha_reunion DESC LIMIT 3");
    $notifs = array_merge($notifs, $stmt->fetchAll(PDO::FETCH_ASSOC));
    // movimientos (recursos_financieros)
    $stmt = $pdo->query("SELECT 'Movimiento' AS tipo, CONCAT(tipo_movimiento, ' - ', COALESCE(descripcion,'')) AS item, fecha AS fecha FROM recursos_financieros ORDER BY fecha DESC LIMIT 3");
    $notifs = array_merge($notifs, $stmt->fetchAll(PDO::FETCH_ASSOC));
    // ordenar por fecha descendente y limitar
    usort($notifs, function($a,$b){ return strcmp($b['fecha'],$a['fecha']); });
    $notifs = array_slice($notifs, 0, 6);
    
    //Grafico tendencia mensual ingresos vs egresos (últimos 6 meses)
    $stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(fecha, '%Y-%m') as mes,
        SUM(CASE 
            WHEN tipo_movimiento IN ('Ingreso','Donacion','Subsidio') 
            THEN monto ELSE 0 END) as ingresos,
        SUM(CASE 
            WHEN tipo_movimiento = 'Gasto' 
            THEN monto ELSE 0 END) as egresos
    FROM recursos_financieros
    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes
    ORDER BY mes ASC
");

$datosMensuales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [];
$ingresosData = [];
$egresosData = [];

foreach ($datosMensuales as $fila) {
    $meses[] = $fila['mes'];
    $ingresosData[] = $fila['ingresos'];
    $egresosData[] = $fila['egresos'];
}

} catch (PDOException $e) {
    // en caso de error, definir valores por defecto
    $totalIngresos = $totalEgresos = $balance = 0;
    $docsData = ['pendiente'=>0,'revisado'=>0,'aprobado'=>0];
    $totalActas = $eventosProximos = 0;
    $notifs = [];
    // opcional: loguear $e->getMessage();
}

// preparar datos para JS
$docsPendiente = (int)($docsData['pendiente'] ?? 0);
$docsRevisado  = (int)($docsData['revisado'] ?? 0);
$docsAprobado  = (int)($docsData['aprobado'] ?? 0);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Asociación de Juntas de Acción Comunal - Panel Presidencial</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Material Icons (monocromáticos sobre verde) -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>


  <style>
    :root{
      --verde:#2E7D32;
      --verde-osc:#1B5E20;
      --amarillo:#FBC02D;
      --fondo:#F5F5F5;
      --card-bg:#ffffff;
      --muted:#6b7280;
    }
    body{ background:var(--fondo); font-family: Inter, system-ui, Arial, sans-serif; margin:0; }
    /* header */
    .topbar{
      height:72px;
      background:#fff;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 20px;
      position:fixed; left:0; right:0; top:0; z-index:1000;
    }
    .brand { font-weight:700; color:var(--verde); font-size:1rem; display:flex; gap:12px; align-items:center;}
    .brand .logo {
      width:42px; height:42px; border-radius:8px; background:linear-gradient(135deg,var(--verde),var(--verde-osc)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700;
    }
    .top-actions{ display:flex; gap:12px; align-items:center; }
    .icon-btn{
      width:44px; height:44px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--verde);
      background:#fff; border:1px solid #eee;
    }
    .user-box{ display:flex; gap:10px; align-items:center; background:#fff; padding:6px 10px; border-radius:8px; border:1px solid #eee; }
    .user-box strong{ font-size:0.95rem; color:var(--verde-osc); }

    /* layout */
    .main-wrap{ display:flex; margin-top:82px; }
    .sidebar{
      width:78px;
      background:var(--verde);
      min-height:calc(100vh - 82px);
      padding:18px 10px;
      border-right: 1px solid rgba(0,0,0,0.04);
      display:flex; flex-direction:column; gap:8px; align-items:center;
    }
    .side-btn{ width:54px; height:54px; display:flex; align-items:center; justify-content:center; border-radius:10px; color:#fff; cursor:pointer; text-decoration:none; }
    .side-btn:hover{ background:rgba(255,255,255,0.08); }

    .content {
      flex:1;
      padding:20px;
      padding-left:34px;
    }
    .report-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* 2 columnas */
  gap: 12px;
}

.report-grid .btn {
  width: 100%;
  height: 150px;
font-size: 1rem;
  
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}

@media (max-width: 768px) {
  .report-grid {
    grid-template-columns: 1fr;
  }
}

    /* header row inside content */
    .page-title{ display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:18px; }
    .page-title h1{ font-size:18px; margin:0; color:var(--verde-osc); }

    /* KPI cards */
    .kpis { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:16px; margin-bottom:18px; }
    .kpi-card { background:var(--card-bg); padding:16px; border-radius:12px; box-shadow:0 6px 18px rgba(16,24,40,0.04); display:flex; align-items:center; gap:12px; }
    .kpi-icon{ width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; }
    .kpi-body{ flex:1; }
    .kpi-title{ font-size:0.85rem; color:var(--muted); margin:0 0 6px 0; }
    .kpi-value{ font-size:1.25rem; font-weight:700; color:var(--verde-osc); margin:0; }

    /* grid for charts/calendar */
    .grid-2 { display:grid; grid-template-columns: 1fr 420px; gap:16px; margin-bottom:16px; }
    .card { background:var(--card-bg); border-radius:12px; padding:14px; box-shadow:0 6px 18px rgba(16,24,40,0.04); }
    .card h3{ margin:0 0 8px 0; font-size:1rem; color:var(--verde-osc); }

    /* chart sizing */
    .chart-wrap { height:260px; display:flex; align-items:center; justify-content:center; }
    canvas{ max-height:260px !important; width:100% !important; }

    /* notifications list */
    .notif-list { display:flex; flex-direction:column; gap:8px; }
    .notif-item { display:flex; justify-content:space-between; gap:10px; padding:8px; border-radius:8px; background:#fbfbfb; border:1px solid #f0f0f0; }
    .notif-item small { color:var(--muted); }

    /* responsive */
    @media (max-width: 1000px){
      .kpis{ grid-template-columns: repeat(2,1fr); }
      .grid-2{ grid-template-columns: 1fr; }
      .sidebar{ display:flex; flex-direction:row; width:100%; min-height:54px; padding:10px; position:fixed; bottom:0; left:0; right:0; z-index:900; border-right:none; }
      .main-wrap{ margin-top:82px; }
    }
  </style>
</head>
<body>

  <!-- topbar -->
  <header class="topbar">
    <div class="brand">
      <div class="logo">AJ</div>
      <div>
        <div style="font-size:0.9rem;">Asociación de Juntas de Acción Comunal</div>
        <div style="font-size:0.76rem; color:var(--muted);">Panel Presidencial</div>
      </div>
    </div>

    <div class="top-actions">
      <div title="Notificaciones" class="icon-btn" id="btnNotify">
        <span class="material-icons">notifications</span>
      </div>

      <div class="user-box">
        <span class="material-icons" style="color:var(--verde-osc)">account_circle</span>
        <div>
          <strong><?= htmlspecialchars($nombre) ?></strong><br>
          <small style="color:var(--muted)">Presidente General</small>
        </div>
        <a href="../public/logout.php" class="btn btn-sm" style="margin-left:12px; background:var(--amarillo); color:#000;">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <!-- main layout -->
  <div class="main-wrap">
    <!-- slim sidebar with icons -->
    <nav class="sidebar" aria-label="Sidebar">
      <a href="dashboard_presidente.php" class="side-btn" title="Panel">
        <span class="material-icons">dashboard</span>
      </a>

      <a href="../public/listar_usuario.php" class="side-btn" title="Usuarios">
        <span class="material-icons">people</span>
      </a>

      <a href="documentos.php" class="side-btn" title="Documentos">
        <span class="material-icons">folder_open</span>
      </a>

      <a href="actas.php" class="side-btn" title="Actas">
        <span class="material-icons">description</span>
      </a>

      <a href="agenda.php" class="side-btn" title="Agenda">
        <span class="material-icons">event</span>
      </a>
      <?php if ($_SESSION['usuario_rol'] === 'Presidente General'): ?>
      <a href="gestionar_jac.php" class="side-btn" title="Gestión JAC">
        <span class="material-icons">location_city</span>
      </a>
<?php endif; ?>
        </nav>

    <!-- content -->
    <main class="content">
      <div class="page-title">
        <div>
          <h1>Vista Ejecutiva</h1>
          <small style="color:var(--muted)">Resumen de la actividad reciente y métricas financieras</small>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
          <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">Actualizar</button>
        </div>
      </div>

      <!-- KPIs -->
      <section class="kpis" aria-label="Indicadores">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:linear-gradient(135deg,var(--verde),var(--verde-osc));"><span class="material-icons">attach_money</span></div>
          <div class="kpi-body">
            <p class="kpi-title">Total Ingresos</p>
            <p class="kpi-value">$<?= number_format($totalIngresos,0,',','.') ?></p>
            <small style="color:<?= $varIngresos >= 0 ? 'green' : 'red' ?>">
            <?= $varIngresos >= 0 ? '▲' : '▼' ?>
            <?= number_format(abs($varIngresos),1) ?>% vs mes anterior
</small>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background:linear-gradient(135deg,var(--amarillo),#e6b800); color:#000;"><span class="material-icons">money_off</span></div>
          <div class="kpi-body">
            <p class="kpi-title">Total Egresos</p>
            <p class="kpi-value">$<?= number_format($totalEgresos,0,',','.') ?></p>
            <small style="color:<?= $varEgresos >= 0 ? 'green' : 'red' ?>">
            <?= $varEgresos >= 0 ? '▲' : '▼' ?>
             <?= number_format(abs($varEgresos),1) ?>% vs mes anterior
          </small>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background:linear-gradient(135deg,#8bc34a,var(--verde));"><span class="material-icons">balance</span></div>
          <div class="kpi-body">
            <p class="kpi-title">Balance</p>
            <p class="kpi-value" style="color:<?= $balance < 0 ? '#c62828' : 'var(--verde-osc)' ?>">$<?= number_format($balance,0,',','.') ?></p>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background:linear-gradient(135deg,var(--verde),#4caf50)"><span class="material-icons">event_available</span></div>
          <div class="kpi-body">
            <p class="kpi-title">Eventos próximos</p>
            <p class="kpi-value"><?= $eventosProximos ?></p>
          </div>
        </div>
        <div class="kpi-card">
  <div class="kpi-icon" style="background:linear-gradient(135deg,#1565C0,#42A5F5)">
    <span class="material-icons">location_city</span>
  </div>
  <div class="kpi-body">
    <p class="kpi-title">JAC Registradas</p>
    <p class="kpi-value"><?= $totalJuntas ?></p>
  </div>
</div>
        <?php if ($balance < 0): ?>
<div class="alert alert-danger mt-3">
  ⚠ Atención: El sistema presenta déficit financiero.
</div>
<?php endif; ?>   
      </section>
      

      <!-- Charts + Calendar -->
      <section class="grid-2">
            <div class="card">
             <h3>📈 Tendencia Financiera</h3>
                <div class="chart-wrap">
            <canvas id="finanzasChart"></canvas>
              </div>
             </div>
              <div class="card">
                <h3>Documentos por estado</h3>
               <div class="chart-wrap">
                  <canvas id="docsChart" aria-label="Documentos por estado" role="img"></canvas>
               </div>
              </div>

        <div class="card">
          <h3>Calendario</h3>
          <div id="calendar"></div>
        </div>
          <div class="card mt-3">
          <h3>📊 Reportes Ejecutivos</h3>
         <p style="color:#555;">Descarga los reportes más recientes del sistema.</p>
        <div class="report-grid">
                  <a href="../controllers/reporte_financieroExcel.php" class="btn btn-success btn-sm">💰 Financiero (Excel)</a>
                <a href="../controllers/reporte_financieroPDF.php" class="btn btn-danger btn-sm">💰 Financiero (PDF)</a>
              <a href="../controllers/reportes_actasPdf.php" class="btn btn-primary btn-sm">📝 Actas (PDF)</a>
             <a href="../controllers/reporte_agendaPdf.php" class="btn btn-warning btn-sm">📅 Agenda (PDF)</a>
            </div>
          </div>

      </section>
<!-- 🔹 Mini resumen de eventos -->
<section class="card mt-3">
  <h3>🗓️ Próximos Eventos</h3>
  <div id="eventosResumen" class="d-flex flex-wrap gap-3">
    <?php
    $stmt = $pdo->query("SELECT titulo, fecha, hora, color 
                         FROM agenda 
                         WHERE fecha >= CURDATE() 
                         ORDER BY fecha ASC 
                         LIMIT 3");
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($eventos) == 0) {
        echo "<p class='text-muted'>No hay eventos próximos registrados.</p>";
    } else {
        foreach ($eventos as $ev) {
            echo "
            <div class='p-10 rounded' 
                 style='background:{$ev['color']}; color:white; min-width:220px;'>
              <strong>{$ev['titulo']}</strong><br>
              <small>".date('d/m/Y', strtotime($ev['fecha']))." - {$ev['hora']}</small>
            </div>";
        }
    }
    ?>
  </div>
</section>

      <!-- Actas y actividad -->
      <section style="display:grid; grid-template-columns:1fr 360px; gap:16px;">
        <div class="card">
          <h3>Total de actas registradas</h3>
          <div style="font-size:2.6rem; color:var(--verde-osc); font-weight:700; margin-top:8px;"><?= $totalActas ?></div>
        </div>

        <aside class="card">
          <h3>Actividad reciente</h3>
          <div class="notif-list" aria-live="polite">
            <?php if (count($notifs) === 0): ?>
              <p class="small text-muted">No hay actividad reciente.</p>
            <?php else: ?>
              <?php foreach ($notifs as $n): ?>
                <div class="notif-item">
                  <div>
                    <strong><?= htmlspecialchars($n['tipo']) ?></strong>
                    <div class="small" style="color:var(--muted)"><?= htmlspecialchars(substr($n['item'],0,80)) ?><?= (strlen($n['item'])>80)?'...':'' ?></div>
                  </div>
                  <small><?= htmlspecialchars(substr($n['fecha'],0,16)) ?></small>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </aside>
      </section>
    </main>
  </div>

  <!-- scripts -->
  <script>
    // Chart data from PHP
    const docsData = {
      labels: ['Pendiente','Revisado','Aprobado'],
      values: [<?= $docsPendiente ?>, <?= $docsRevisado ?>, <?= $docsAprobado ?>],
      colors: ['<?= $GLOBALS['__doc_color1'] = '#FBC02D' ?>','<?= $GLOBALS['__doc_color2'] = '#81C784' ?>','<?= $GLOBALS['__doc_color3'] = '#4CAF50' ?>']
    };

    // render docs donut
    (function(){
      const ctx = document.getElementById('docsChart').getContext('2d');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: docsData.labels,
          datasets: [{
            data: docsData.values,
            backgroundColor: docsData.colors,
            hoverOffset: 8,
            borderWidth: 0
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' }
          },
          cutout: '68%'
        }
      });
    })();

    const meses = <?= json_encode($meses) ?>;
const ingresosData = <?= json_encode($ingresosData) ?>;
const egresosData = <?= json_encode($egresosData) ?>;

new Chart(document.getElementById('finanzasChart'), {
    type: 'line',
    data: {
        labels: meses,
        datasets: [
            {
                label: 'Ingresos',
                data: ingresosData,
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46,125,50,0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Egresos',
                data: egresosData,
                borderColor: '#c62828',
                backgroundColor: 'rgba(198,40,40,0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

    // FullCalendar: cargar eventos desde controlador
    document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');

  if (!calendarEl) {
    console.error("No se encontró el div #calendar");
    return;
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'es',
    initialView: 'dayGridMonth',
    height: 400,
    events: '../controllers/obtener_eventos.php',  // 🔹 Ruta al archivo
    eventDisplay: 'block',
    eventColor: '#2E7D32', // color por defecto
    eventDidMount: function(info) {
      if (info.event.extendedProps.color) {
        info.el.style.backgroundColor = info.event.extendedProps.color;
        info.el.style.border = 'none';
        info.el.style.color = '#fff';
      }
    }
  });

  calendar.render();
});


    // Botón actualizar
    document.getElementById('refreshBtn').addEventListener('click', function(){
      location.reload();
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
