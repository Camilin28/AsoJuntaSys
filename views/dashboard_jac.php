<?php
session_start();
require('../config/db.php');

// 🔐 Seguridad
if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    strtolower($_SESSION['usuario_rol']) !== 'presidentes de jac'
) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['jac_id']) || empty($_SESSION['jac_id'])) {
    die("Error: Usuario sin JAC asignada.");
}

$jac_id = $_SESSION['jac_id'];
$nombre = $_SESSION['usuario_nombre'] ?? 'Presidente JAC';
$paginaActual = basename($_SERVER['PHP_SELF']);

try {

    // ================= FINANZAS DE SU JAC =================
    $stmt = $pdo->prepare(" SELECT 
            SUM(CASE WHEN tipo_movimiento IN ('Ingreso','Donacion','Subsidio') THEN monto ELSE 0 END) as ingresos,
            SUM(CASE WHEN tipo_movimiento = 'Gasto' THEN monto ELSE 0 END) as egresos
        FROM recursos_financieros
        WHERE jac_id = ?
    ");
    $stmt->execute([$jac_id]);
    $finanzas = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalIngresos = (float)($finanzas['ingresos'] ?? 0);
    $totalEgresos  = (float)($finanzas['egresos'] ?? 0);
    $balance = $totalIngresos - $totalEgresos;

    // ================= DOCUMENTOS =================
    $stmt = $pdo->prepare(" SELECT COUNT(*) FROM documentos WHERE jac_id = ? AND estado = 'Pendiente'
    ");
    $stmt->execute([$jac_id]);
    $docsPendientes = (int)$stmt->fetchColumn();

    // ================= ACTAS =================
    $stmt = $pdo->prepare(" SELECT COUNT(*)  FROM actas  WHERE jac_id = ?
    ");
    $stmt->execute([$jac_id]);
    $totalActas = (int)$stmt->fetchColumn();
    // ================= EVENTOS =================
    $stmt = $pdo->prepare(" SELECT COUNT(*)  FROM agenda  WHERE jac_id = ? AND fecha >= CURDATE()
    ");
    $stmt->execute([$jac_id]);
    $eventosProximos = (int)$stmt->fetchColumn();

} catch (PDOException $e) {
    $totalIngresos = $totalEgresos = $balance = 0;
    $docsPendientes = $totalActas = $eventosProximos = 0;
}
// ================= TENDENCIA FINANCIERA (6 meses) =================
$stmt = $pdo->prepare(" SELECT 
        DATE_FORMAT(fecha, '%Y-%m') as mes,
        SUM(CASE 
            WHEN tipo_movimiento IN ('Ingreso','Donacion','Subsidio') 
            THEN monto ELSE 0 END) as ingresos,
        SUM(CASE 
            WHEN tipo_movimiento = 'Gasto' 
            THEN monto ELSE 0 END) as egresos
    FROM recursos_financieros
    WHERE jac_id = ?
    AND fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes
    ORDER BY mes ASC
");
$stmt->execute([$jac_id]);
$datosMensuales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [];
$ingresosData = [];
$egresosData = [];

foreach ($datosMensuales as $fila) {
    $meses[] = $fila['mes'];
    $ingresosData[] = $fila['ingresos'];
    $egresosData[] = $fila['egresos'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Presidente JAC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">

<style>
:root{
  --verde:#2E7D32;
  --verde-osc:#1B5E20;
  --amarillo:#FBC02D;
  --fondo:#F5F5F5;
}

body{ background:var(--fondo); margin:0; font-family:Arial; }

.topbar{
  height:70px;
  background:#fff;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:0 20px;
  box-shadow:0 2px 6px rgba(0,0,0,.08);
}

.sidebar{
  width:80px;
  background:var(--verde);
  min-height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  padding-top:20px;
  gap:12px;
}

.side-btn{
  width:50px;height:50px;
  display:flex;align-items:center;justify-content:center;
  border-radius:10px;
  color:#fff;text-decoration:none;
}

.side-btn:hover{ background:rgba(255,255,255,.15); }

.side-btn.active{
  background:rgba(255,255,255,.25);
  border:2px solid var(--amarillo);
}

.main-wrap{ display:flex; }

.content{ flex:1; padding:20px; }

.kpis{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:15px;
  margin-top:20px;
}

.kpi-card{
  background:#fff;
  padding:15px;
  border-radius:10px;
  box-shadow:0 4px 10px rgba(0,0,0,.05);
}

.kpi-card h6{ color:#666; font-size:14px; }
.kpi-card h4{ margin:0; font-weight:bold; }

@media(max-width:900px){
  .kpis{ grid-template-columns:repeat(2,1fr); }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
</head>

<body>

<div class="topbar">
  <strong style="color:var(--verde-osc)">Panel Presidente JAC</strong>
  <div>
    <span class="material-icons">account_circle</span>
    <strong><?= htmlspecialchars($nombre) ?></strong>
    <a href="../public/logout.php" class="btn btn-warning btn-sm">Cerrar sesión</a>
  </div>
</div>

<div class="main-wrap">

<nav class="sidebar">

  <a href="dashboard_jac.php"
     class="side-btn <?= $paginaActual == 'dashboard_jac.php' ? 'active' : '' ?>">
    <span class="material-icons">dashboard</span>
  </a>

  <a href="documentos.php" class="side-btn">
    <span class="material-icons">folder</span>
  </a>

  <a href="actas.php" class="side-btn">
    <span class="material-icons">description</span>
  </a>

  <a href="agenda.php" class="side-btn">
    <span class="material-icons">event</span>
  </a>

</nav>

<main class="content">

<h4>Resumen de mi JAC</h4>

<section class="kpis">

  <div class="kpi-card">
    <h6>Ingresos</h6>
    <h4 class="text-success">$<?= number_format($totalIngresos,0,',','.') ?></h4>
  </div>

  <div class="kpi-card">
    <h6>Egresos</h6>
    <h4 class="text-danger">$<?= number_format($totalEgresos,0,',','.') ?></h4>
  </div>

  <div class="kpi-card">
    <h6>Balance</h6>
    <h4 style="color:<?= $balance < 0 ? '#c62828' : '#2E7D32' ?>">
      $<?= number_format($balance,0,',','.') ?>
    </h4>
  </div>

  <div class="kpi-card">
    <h6>Documentos Pendientes</h6>
    <h4><?= $docsPendientes ?></h4>
  </div>

  <div class="kpi-card">
    <h6>Total Actas</h6>
    <h4><?= $totalActas ?></h4>
  </div>

  <div class="kpi-card">
    <h6>Eventos Próximos</h6>
    <h4><?= $eventosProximos ?></h4>
  </div>

</section>
<?php if ($balance < 0): ?>
<div class="alert alert-danger mt-3">
  ⚠ Atención: Su JAC presenta déficit financiero.
</div>
<?php endif; ?>

<div class="row mt-4">

  <!-- Gráfico -->
  <div class="col-md-7">
    <div class="card p-3">
      <h5>📈 Tendencia Financiera</h5>
      <canvas id="finanzasChart"></canvas>
    </div>
  </div>

  <!-- Calendario -->
  <div class="col-md-5">
    <div class="card p-3">
      <h5>📅 Calendario</h5>
      <div id="calendar"></div>
    </div>
  </div>

</div>

</main>
</div>
<script>
const meses = <?= json_encode($meses) ?>;
const ingresosData = <?= json_encode($ingresosData) ?>;
const egresosData = <?= json_encode($egresosData) ?>;

// 📊 Gráfico financiero
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

// 📅 Calendario filtrado por JAC
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'es',
    initialView: 'dayGridMonth',
    height: 350,
    events: '../controllers/obtener_eventos_jac.php'
  });

  calendar.render();
});
</script>
</body>
</html>