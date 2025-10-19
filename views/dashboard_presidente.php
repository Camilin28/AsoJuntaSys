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

    // Eventos próximos (count)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE fecha >= CURDATE()");
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
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>

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

      <a href="estadisticas.php" class="side-btn" title="Estadísticas">
        <span class="material-icons">bar_chart</span>
      </a>
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
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background:linear-gradient(135deg,var(--amarillo),#e6b800); color:#000;"><span class="material-icons">money_off</span></div>
          <div class="kpi-body">
            <p class="kpi-title">Total Egresos</p>
            <p class="kpi-value">$<?= number_format($totalEgresos,0,',','.') ?></p>
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
      </section>

      <!-- Charts + Calendar -->
      <section class="grid-2">
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

    // FullCalendar: cargar eventos desde controlador
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 420,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,dayGridWeek'
        },
        events: '../controllers/obtener_eventos.php',
        locale: 'es',
        eventDidMount: function(info) {
          // asegurar color y estilo
          info.el.style.backgroundColor = info.event.extendedProps.color || '#2E7D32';
          info.el.style.border = 'none';
          info.el.style.color = '#fff';
        },
        eventClick: function(info) {
          // detalle básico:
          let title = info.event.title || 'Evento';
          let desc  = info.event.extendedProps.description || '';
          alert(title + (desc ? "\n\n" + desc : ""));
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
