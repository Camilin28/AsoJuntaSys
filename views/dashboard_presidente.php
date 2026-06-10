<?php
session_start();
require('../config/db.php');

// seguridad: sólo Presidente General
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Presidente';


try {

    /* ==========================
       RESUMEN FINANCIERO GLOBAL
       ========================== */

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio') ");
    $stmt->execute();
    $totalIngresos = (float)$stmt->fetchColumn();

    $stmt = $pdo->prepare(" SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento = 'Gasto'");
    $stmt->execute();
    $totalEgresos = (float)$stmt->fetchColumn();
    $balance = $totalIngresos - $totalEgresos;
    /* ==========================
       INGRESOS Y EGRESOS MES ACTUAL
       ========================== */
    $stmt = $pdo->prepare(" SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio') AND MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE()) ");
    $stmt->execute();
    $ingresosMesActual = (float)$stmt->fetchColumn();

    $stmt = $pdo->prepare(" SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento='Gasto' AND MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE()) ");
    $stmt->execute();
    $egresosMesActual = (float)$stmt->fetchColumn();

    /* ==========================
       MES ANTERIOR
       ========================== */

    $stmt = $pdo->prepare(" SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio') AND MONTH(fecha)=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(fecha)=YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
    $stmt->execute();
    $ingresosMesAnterior = (float)$stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(monto),0) FROM recursos_financieros WHERE tipo_movimiento='Gasto' AND MONTH(fecha)=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(fecha)=YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
    $stmt->execute();
    $egresosMesAnterior = (float)$stmt->fetchColumn();
    $varIngresos = $ingresosMesAnterior > 0
        ? (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100
        : 0;
    $varEgresos = $egresosMesAnterior > 0
        ? (($egresosMesActual - $egresosMesAnterior) / $egresosMesAnterior) * 100
        : 0;
    /* ==========================
       DOCUMENTOS POR ESTADO
       ========================== */
    $stmt = $pdo->query(" SELECT LOWER(estado) AS estado_norm, COUNT(*) AS total FROM documentos GROUP BY estado_norm ");

    $docsData = [
        'pendiente' => 0,
        'revisado' => 0,
        'aprobado' => 0
    ];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $estado = $row['estado_norm'];

        if(isset($docsData[$estado])) {
            $docsData[$estado] = (int)$row['total'];
        }
    }

    /* ==========================
       TOTALES GENERALES
       ========================== */

    $stmt = $pdo->query("SELECT COUNT(*) FROM actas");
    $totalActas = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM juntas");
    $totalJuntas = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios    WHERE cargo='Presidente'");
    $totalPresidentes = (int)$stmt->fetchColumn();



    $stmt = $pdo->query(" SELECT COUNT(*) FROM usuarios WHERE cargo='Secretario'");

    $totalSecretarios = (int)$stmt->fetchColumn();


    /******************************
     TOTAL TESOREROS
    ******************************/
    $stmt = $pdo->query(" SELECT COUNT(*) FROM usuarios WHERE cargo='Tesorero' ");

    $totalTesoreros = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $totalUsuarios = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM documentos");
    $totalDocumentos = (int)$stmt->fetchColumn();

    $stmt = $pdo->query(" SELECT COUNT(*) FROM documentos WHERE estado='Pendiente'");
    $documentosPendientes = (int)$stmt->fetchColumn();

    /* ==========================
       EVENTOS PRÓXIMOS
       ========================== */

    $stmt = $pdo->prepare("  SELECT COUNT(*) FROM agenda WHERE STR_TO_DATE(fecha,'%Y-%m-%d') >= CURDATE() ");
    $stmt->execute();
    $eventosProximos = (int)$stmt->fetchColumn();

    /* ==========================
       JUNTAS REGISTRADAS
       ========================== */

    $stmt = $pdo->query(" SELECT j.id,  j.nombre,  COUNT(DISTINCT u.id) AS usuarios,  COUNT(DISTINCT a.id) AS actas,  COUNT(DISTINCT d.id) AS documentos FROM juntas j LEFT JOIN usuarios u ON u.jac_id = j.id LEFT JOIN actas a ON a.jac_id = j.id LEFT JOIN documentos d ON d.jac_id = j.id GROUP BY j.id ORDER BY j.nombre ASC");

    $juntasResumen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ==========================
   PRESIDENTES DE JAC
   ========================== */

    $stmt = $pdo->query(" SELECT j.id, j.nombre AS jac, u.nombre AS presidente FROM juntas j LEFT JOIN usuarios u  ON u.jac_id = j.id  AND u.cargo = 'Presidente' ORDER BY j.nombre ");

    $presidentesJAC = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ==========================
       ULTIMAS ACTAS
       ========================== */

    $stmt = $pdo->query(" SELECT a.id, a.titulo, a.fecha_reunion,  j.nombre AS junta FROM actas a LEFT JOIN juntas j ON a.jac_id = j.id ORDER BY a.fecha_reunion DESC  LIMIT 5");
    $ultimasActas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    /* ==========================
       ULTIMOS DOCUMENTOS
       ========================== */
    $stmt = $pdo->query(" SELECT d.id, d.titulo, d.estado,  d.fecha_subida,  j.nombre AS junta FROM documentos d LEFT JOIN juntas j ON d.jac_id = j.id ORDER BY d.fecha_subida DESC LIMIT 5 ");

    $ultimosDocumentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ==========================
       USUARIOS POR JAC
       ========================== */

    $stmt = $pdo->query(" SELECT j.nombre, COUNT(u.id) AS total FROM juntas j  LEFT JOIN usuarios u ON u.jac_id = j.id GROUP BY j.id ORDER BY j.nombre ");
    $labelsJAC = [];
    $dataJAC = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {

        $labelsJAC[] = $fila['nombre'];
        $dataJAC[] = (int)$fila['total'];

    }

    /* ==========================
       GRAFICO FINANCIERO
       ========================== */

    $stmt = $pdo->query(" SELECT
            DATE_FORMAT(fecha,'%Y-%m') AS mes,

            SUM(
                CASE
                WHEN tipo_movimiento IN ('Ingreso','Donacion','Subsidio')
                THEN monto
                ELSE 0
                END
            ) AS ingresos,

            SUM(
                CASE
                WHEN tipo_movimiento='Gasto'
                THEN monto
                ELSE 0
                END
            ) AS egresos

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

} catch(PDOException $e) {
    die($e->getMessage());
}

/* ==========================
   VARIABLES JS
   ========================== */

$docsPendiente = (int)($docsData['pendiente'] ?? 0);
$docsRevisado = (int)($docsData['revisado'] ?? 0);
$docsAprobado = (int)($docsData['aprobado'] ?? 0);

$juntasLabelsJson = json_encode($labelsJAC);
$juntasDataJson = json_encode($dataJAC);

$mesesJson = json_encode($meses);
$ingresosJson = json_encode($ingresosData);
$egresosJson = json_encode($egresosData);

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
    .kpis { display:grid;grid-template-columns: repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:18px;}
    .kpi-card { background:var(--card-bg); padding:16px; border-radius:12px; box-shadow:0 6px 18px rgba(16,24,40,0.04); display:flex; align-items:center; gap:12px; }
    .kpi-card:hover{transform: translateY(-3px);transition:.3s;}
    .kpi-icon{ width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; }
    .kpi-body{ flex:1; }
    .kpi-title{ font-size:0.85rem; color:var(--muted); margin:0 0 6px 0; }
    .kpi-value{ font-size:1.25rem; font-weight:700; color:var(--verde-osc); margin:0; }

    /* grid for charts/calendar */
    .grid-2 { display:grid; grid-template-columns: 1fr 420px; gap:16px; margin-bottom:16px; }
    .grid-3{ display:grid;grid-template-columns:1fr 1fr 1fr; gap:16px;margin-bottom:20px;}
    @media(max-width:1000px){.grid-3{grid-template-columns:1fr;}}
    .card { background:var(--card-bg); border-radius:12px; padding:14px; box-shadow:0 6px 18px rgba(16,24,40,0.04); }
    .card:hover{box-shadow:0 10px 25px rgba(0,0,0,.08);transition:.3s;}
    .card h3{ margin:0 0 8px 0; font-size:1rem; color:var(--verde-osc); }

    /*JACS */
    .jac-card{border:none;border-radius:15px;overflow:hidden;transition:.3s;}
    .jac-card:hover{transform:translateY(-5px);}
    .jac-header{background:linear-gradient(135deg,var(--verde),var(--verde-osc) );color:white;padding:15px;}
    .jac-body{padding:15px;}
    .executive-card{ background:linear-gradient(135deg,var(--verde),var(--verde-osc)); color:white; border-radius:15px; padding:20px; margin-bottom:20px; }
    .executive-number{ font-size:2rem;font-weight:bold; }
    .table-jac{font-size:.9rem;}
    .badge-documento{padding:6px 10px; border-radius:20px;}
    .estado-pendiente{ background:#FFF3CD; color:#856404;}
    .estado-aprobado{background:#D4EDDA;color:#155724;}
    .estado-revisado{ background:#CCE5FF; color:#004085;}
    .quick-action{text-decoration:none;color:white;padding:15px;border-radius:12px;display:flex;align-items:center;justify-content:center;gap:10px;font-weight:600;transition:.3s;}
    .quick-action:hover{transform:translateY(-4px);color:white;}
    .action-green{background:linear-gradient(135deg,#2E7D32,#1B5E20);}
    .action-yellow{background:linear-gradient(135deg,#FBC02D,#e0a800);color:black;}
    .action-blue{background:linear-gradient(135deg,#1976D2,#0D47A1);}
    .action-red{background:linear-gradient(135deg,#C62828,#8E0000);}

    /* chart sizing */
    .chart-wrap { height:260px; display:flex; align-items:center; justify-content:center; }
    canvas{ max-height:260px !important; width:100% !important; }

    /* notifications list */
    .notif-list { display:flex; flex-direction:column; gap:8px; }
    .notif-item { display:flex; justify-content:space-between; gap:10px; padding:8px; border-radius:8px; background:#fbfbfb; border:1px solid #f0f0f0; }
    .notif-item small { color:var(--muted); }
    .table-modern thead{ background:var(--verde); color:white;}
    .table-modern tbody tr:hover{background:#f7fff7;}

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
        <a href="registrar.php" class="btn btn-sm" style="margin-left:12px;">+ Crear nuevo usuario </a>
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

      <a href="actas_general.php" class="side-btn" title="Actas Generales">
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
      <div class="executive-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">🏛️ Panel Ejecutivo AsoJuntaSys</h2>
            <p class="mb-0"> Bienvenido <?= htmlspecialchars($nombre) ?>.
                Desde este panel puede supervisar todas las Juntas de Acción Comunal registradas,
                controlar documentación, actas, recursos financieros y actividades institucionales.
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="executive-number">
                <?= $totalJuntas ?>
            </div>
            <div>
                Juntas Registradas
            </div>
        </div>
    </div>
</div>
<div class="page-title">
    <div>
        <h1>Vista Ejecutiva General</h1>
        <small style="color:var(--muted)">Información consolidada de todas las Juntas de Acción Comunal</small>
    </div>
    <div style="display:flex;gap:10px;">
        <button
            class="btn btn-outline-secondary"
            id="refreshBtn">
            Actualizar
        </button>
        <a
            href="gestionar_jac.php"
            class="btn btn-success">
            🏘️ Gestionar JAC
        </a>
    </div>
</div>
<!-- ACCESOS RÁPIDOS -->
<div class="grid-3 mb-4">
    <a href="gestionar_jac.php"
       class="quick-action action-green">
        <span class="material-icons">
            location_city
        </span>
        Gestión de JAC
    </a>
    <a href="actas.php"
       class="quick-action action-blue">
        <span class="material-icons">
            description
        </span>
        Actas
    </a>
    <a href="documentos.php"
       class="quick-action action-yellow">
        <span class="material-icons">
            folder
        </span>
        Documentos
    </a>
</div>
<div class="grid-3 mb-4">
    <a href="agenda.php"
       class="quick-action action-red">
        <span class="material-icons">
            event
        </span>
        Agenda
    </a>
    <a href="../public/listar_usuario.php"
       class="quick-action action-green">
        <span class="material-icons">
            people
        </span>
        Usuarios
    </a>
    <a href="registrar.php"
       class="quick-action action-blue">
        <span class="material-icons">
            person_add
        </span>
        Crear Usuario
    </a>
</div>

      <!-- KPIs -->

      <section class="kpis" aria-label="Indicadores">
    <!-- INGRESOS -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,var(--verde),var(--verde-osc));">
            <span class="material-icons">attach_money</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Total Ingresos</p>
            <p class="kpi-value"> $<?= number_format($totalIngresos,0,',','.') ?></p>
            <small style="color:<?= $varIngresos >= 0 ? 'green' : 'red' ?>">
                <?= $varIngresos >= 0 ? '▲' : '▼' ?>
                <?= number_format(abs($varIngresos),1) ?>%
                vs mes anterior
            </small>
        </div>
    </div>
    <!-- EGRESOS -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,var(--amarillo),#e6b800);color:black;">
            <span class="material-icons">money_off</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Total Egresos</p>
            <p class="kpi-value">$<?= number_format($totalEgresos,0,',','.') ?></p>
            <small style="color:<?= $varEgresos >= 0 ? 'green' : 'red' ?>">
                <?= $varEgresos >= 0 ? '▲' : '▼' ?>
                <?= number_format(abs($varEgresos),1) ?>%
                vs mes anterior
            </small>
        </div>
    </div>
    <!-- BALANCE -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#8bc34a,var(--verde));">
            <span class="material-icons">balance</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Balance General</p>
            <p class="kpi-value"style="color:<?= $balance < 0 ? '#c62828' : 'var(--verde-osc)' ?>">
                $<?= number_format($balance,0,',','.') ?>
            </p>
        </div>
    </div> 
    <!-- EVENTOS -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,var(--verde),#4caf50)">
            <span class="material-icons"> event_available</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Eventos Próximos</p>
            <p class="kpi-value"> <?= $eventosProximos ?> </p>
        </div>
    </div>
    <!-- JAC -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#1565C0,#42A5F5)">
            <span class="material-icons">location_city</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">JAC Registradas</p>
            <p class="kpi-value"> <?= $totalJuntas ?></p>
        </div>
    </div>
    <!-- USUARIOS -->
    <div class="kpi-card">
        <div class="kpi-icon"style="background:linear-gradient(135deg,#6A1B9A,#AB47BC)">
            <span class="material-icons">people </span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Usuarios Totales </p>
            <p class="kpi-value"><?= $totalUsuarios ?></p>
        </div>
    </div>
    <!-- DOCUMENTOS -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#EF6C00,#FB8C00)">
            <span class="material-icons">folder</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Documentos</p>
            <p class="kpi-value"> <?= $totalDocumentos ?></p>
        </div>
    </div>
    <!-- ACTAS -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#00897B,#26A69A)">
            <span class="material-icons"> description</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title"> Actas Registradas</p>
            <p class="kpi-value"> <?= $totalActas ?></p>
      </div>
    </div>
    <!-- DOCUMENTOS PENDIENTES -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background:linear-gradient(135deg,#C62828,#EF5350)">
            <span class="material-icons">pending_actions</span>
        </div>
        <div class="kpi-body">
            <p class="kpi-title">Pendientes Revisión</p>
            <p class="kpi-value"> <?= $docsData['pendiente'] ?></p>
        </div>
    </div>
</section>
<?php if ($balance < 0): ?>
<div class="alert alert-danger">
    ⚠ Atención: El sistema presenta déficit financiero.
</div>
<?php endif; ?>
  
      <!-- Charts + Calendar -->
      <section class="grid-2">
    <!-- TENDENCIA FINANCIERA -->
    <div class="card">
        <h3>📈 Tendencia Financiera Global</h3>
        <div class="chart-wrap">
            <canvas id="finanzasChart"></canvas>
        </div>
    </div>
    <!-- DOCUMENTOS -->
    <div class="card">
        <h3>📄 Estado de Documentación</h3>
        <div class="chart-wrap">
            <canvas id="docsChart"></canvas>
        </div>
    </div>
    <!-- USUARIOS POR JAC -->
    <div class="card">
        <h3>👥 Usuarios por Junta</h3>
        <div class="chart-wrap">
            <canvas id="usuariosJacChart"></canvas>
        </div>
    </div>
    <!-- CALENDARIO -->
    <div class="card">
        <h3>📅 Agenda General</h3>
        <div id="calendar"></div>
    </div>
</section>
<!-- RESUMEN DE JUNTAS -->
<div class="card mb-4">
    <h3 class="mb-3">
        🏘️ Estado General de las Juntas
    </h3>
    <div class="table-responsive">
        <table class="table table-modern align-middle">
            <thead>
                <tr>
                    <th>Junta</th>
                    <th>Usuarios</th>
                    <th>Actas</th>
                    <th>Documentos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($juntasResumen as $jac): ?>
                <tr>
                    <td>
                        <strong>
                            <?= htmlspecialchars($jac['nombre']) ?>
                        </strong>
                    </td>
                    <td>
                        <?= $jac['usuarios'] ?>
                    </td>
                    <td>
                        <?= $jac['actas'] ?>
                    </td>
                    <td>
                        <?= $jac['documentos'] ?>
                    </td>
                    <td>
                        <a
                            href="ver_jac.php?id=<?= $jac['id'] ?>"
                            class="btn btn-success btn-sm">Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REPORTES -->
<div class="card mb-4">
    <h3>📊 Reportes Ejecutivos</h3>
    <p class="text-muted">Descarga información consolidada de la Asociaciónn</p>
    <div class="report-grid">
        <a href="../controllers/reporte_financieroExcel.php"
           class="btn btn-success">💰 Financiero Excel
        </a>
        <a href="../controllers/reporte_financieroPDF.php"
           class="btn btn-danger">💰 Financiero PDF
        </a>
        <a href="../controllers/reportes_actasPdf.php"
           class="btn btn-primary">📝 Actas PDF
        </a>
        <a href="../controllers/reporte_agendaPdf.php"
           class="btn btn-warning">📅 Agenda PDF
        </a>
    </div>
</div>

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

      <!-- ==========================
     PANEL GENERAL DE JAC
========================== -->

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <h3>🏘️ Juntas Registradas</h3>
            <div class="row">
                <?php foreach($juntasResumen as $jac): ?>
                <div class="col-md-6 mb-3">
                    <div class="card jac-card">
                        <div class="jac-header">
                            <h5 class="mb-0">
                                <?= htmlspecialchars($jac['nombre']) ?>
                            </h5>
                        </div>
                        <div class="jac-body">
                            <p>
                                👥 Usuarios:
                                <strong><?= $jac['usuarios'] ?></strong>
                            </p>
                            <p>
                                📝 Actas:
                                <strong><?= $jac['actas'] ?></strong>
                            </p>
                            <p>
                                📂 Documentos:
                                <strong><?= $jac['documentos'] ?></strong>
                            </p>
                            <div class="d-grid">
                                <a href="ver_jac.php?id=<?= $jac['id'] ?>"
                                   class="btn btn-success">
                                    Ver Junta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <h3>📂 Últimos Documentos</h3>
            <?php if(empty($ultimosDocumentos)): ?>
                <p class="text-muted">
                    No hay documentos registrados.
                </p>
            <?php else: ?>
                <?php foreach($ultimosDocumentos as $doc): ?>
                    <div class="border-bottom mb-2 pb-2">
                        <strong>
                            <?= htmlspecialchars($doc['titulo']) ?>
                        </strong>
                        <br>
                        <small class="text-muted">
                            <?= htmlspecialchars($doc['junta']) ?>
                        </small>
                        <br>
                        <span class="badge bg-warning text-dark">
                            <?= htmlspecialchars($doc['estado']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="card">
            <h3>📝 Últimas Actas</h3>
            <?php if(empty($ultimasActas)): ?>
                <p class="text-muted">
                    No hay actas registradas.
                </p>
            <?php else: ?>
                <?php foreach($ultimasActas as $acta): ?>
                    <div class="border-bottom mb-2 pb-2">
                        <strong>
                            <?= htmlspecialchars($acta['titulo']) ?>
                        </strong>
                        <br>
                        <small class="text-muted">
                            <?= htmlspecialchars($acta['junta']) ?>
                        </small>
                        <br>
                        <small>
                            <?= date('d/m/Y', strtotime($acta['fecha_reunion'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
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
