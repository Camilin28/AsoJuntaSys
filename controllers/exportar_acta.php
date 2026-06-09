<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../vendor/autoload.php';

requireLogin();

use Dompdf\Dompdf;
use Dompdf\Options;

// Validar parámetro de acta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: No se especificó el acta a exportar.");
}

$acta_id = intval($_GET['id']);

// Consultar datos del acta
$sql = "SELECT a.id, a.titulo, a.fecha_reunion, a.lugar, a.asistentes, a.acuerdos, 
               a.observaciones, d.titulo AS documento
        FROM actas a
        LEFT JOIN documentos d ON a.documento_id = d.id
        WHERE a.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $acta_id]);
$acta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$acta) {
    die("❌ Error: No se encontró el acta.");
}

// Configuración de Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// ✅ Logo en Base64 como marca de agua
$logoPath = __DIR__ . '/../imagenes/Logo_AsojuntaSys.png'; // Ajusta la ruta según tu proyecto
$logoBase64 = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,' . $logoBase64;

// HTML del acta
$html = '
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            position: relative;
        }
        /* 🔹 Marca de agua centrada */
.watermark {
  position: fixed;
  left: 0;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  text-align: center;
  pointer-events: none;
  z-index: 0; /* No usar negativo */
}

.watermark img {
  display: inline-block;
  width: 100%;       /* <- Aquí ajustamos más grande */
  max-width: 1600px; /* <- límite más alto */
  height: auto;
  opacity: 0.30;    /* Transparencia */
}
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .contenido {
            font-size: 14px;
            line-height: 1.6;
        }
        .contenido p {
            margin: 6px 0;
        }
        .titulo-acta {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            color: #2E7D32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2> Junta de Acción Comunal Bella Vista</h2>
        <h3 class="titulo-acta">' . htmlspecialchars($acta['titulo']) . '</h3>
    </div>

    <!-- Marca de agua -->
    <div class="watermark">
        <img src="' . $logoSrc . '" width="400">
    </div>

    <div class="contenido">
        <p><strong> Fecha:</strong> ' . htmlspecialchars($acta['fecha_reunion']) . '</p>
        <p><strong> Lugar:</strong> ' . htmlspecialchars($acta['lugar']) . '</p>
        <p><strong> Asistentes:</strong> ' . nl2br(htmlspecialchars($acta['asistentes'])) . '</p>
        <p><strong> Acuerdos:</strong> ' . nl2br(htmlspecialchars($acta['acuerdos'])) . '</p>
        <p><strong> Observaciones:</strong> ' . nl2br(htmlspecialchars($acta['observaciones'])) . '</p>
        <p><strong> Documento Asociado:</strong> ' . ($acta['documento'] ?? 'Ninguno') . '</p>
    </div>
</body>
</html>
';

// Generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Acta_" . $acta['id'] . ".pdf", ["Attachment" => false]);
exit;
