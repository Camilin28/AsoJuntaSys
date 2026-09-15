<?php
require_once '../includes/auth.php';
require_once '../includes/auditoria.php';
require '../config/db.php';
require '../config/ia.php';

// RF-028: Asistente Virtual de Consulta Institucional.
// Respeta el rol del usuario: solo arma contexto de los datos a los
// que ese usuario ya tiene acceso en el resto del sistema (RNF-016).

requireLogin();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'respuesta' => 'Método no permitido.']);
    exit();
}

$pregunta = trim($_POST['pregunta'] ?? '');

// Validación de CSRF con respuesta JSON (este endpoint no puede usar el
// die() de texto plano de validarTokenCSRF(), rompería el fetch() del cliente).
if (
    empty($_SESSION['csrf_token']) ||
    empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'respuesta' => 'Sesión inválida o expirada. Recarga la página.']);
    exit();
}

if ($pregunta === '') {
    echo json_encode(['ok' => false, 'respuesta' => 'Escribe una pregunta.']);
    exit();
}

if (mb_strlen($pregunta) > 500) {
    echo json_encode(['ok' => false, 'respuesta' => 'La pregunta es demasiado larga. Intenta resumirla.']);
    exit();
}

$rol = $_SESSION['usuario_rol'];
$jacId = $_SESSION['jac_id'] ?? null;

try {
    /* ===========================
       Construir contexto institucional
       (solo lo mínimo necesario, RNF-016)
    =========================== */

    $contexto = "";

    // Últimas actas de la JAC del usuario (o generales si es Presidente General sin JAC asignada)
    if ($jacId) {
        $stmt = $pdo->prepare("SELECT titulo, fecha_reunion, acuerdos FROM actas WHERE jac_id = :jac_id ORDER BY fecha_reunion DESC LIMIT 5");
        $stmt->execute([':jac_id' => $jacId]);
    } else {
        $stmt = $pdo->query("SELECT titulo, fecha_reunion, acuerdos FROM actas ORDER BY fecha_reunion DESC LIMIT 5");
    }
    $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($actas) {
        $contexto .= "ACTAS RECIENTES:\n";
        foreach ($actas as $a) {
            $acuerdos = mb_substr($a['acuerdos'] ?? '', 0, 200);
            $contexto .= "- \"{$a['titulo']}\" ({$a['fecha_reunion']}): {$acuerdos}\n";
        }
        $contexto .= "\n";
    }

    // Próximos eventos de agenda
    if ($jacId) {
        $stmt = $pdo->prepare("SELECT titulo, fecha, hora FROM agenda WHERE jac_id = :jac_id AND fecha >= CURDATE() ORDER BY fecha ASC LIMIT 5");
        $stmt->execute([':jac_id' => $jacId]);
    } else {
        $stmt = $pdo->query("SELECT titulo, fecha, hora FROM agenda WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 5");
    }
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($eventos) {
        $contexto .= "PRÓXIMOS EVENTOS EN AGENDA:\n";
        foreach ($eventos as $e) {
            $contexto .= "- \"{$e['titulo']}\" el {$e['fecha']} a las {$e['hora']}\n";
        }
        $contexto .= "\n";
    }

    // Documentos pendientes (solo el conteo, no listado completo de archivos)
    if ($jacId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM documentos WHERE jac_id = :jac_id AND estado = 'Pendiente'");
        $stmt->execute([':jac_id' => $jacId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM documentos WHERE estado = 'Pendiente'");
    }
    $documentosPendientes = (int) $stmt->fetchColumn();
    $contexto .= "DOCUMENTOS PENDIENTES DE REVISIÓN: {$documentosPendientes}\n\n";

    // Resumen financiero — SOLO si el rol tiene permiso (mismo criterio que el resto del sistema)
    if (in_array($rol, ['Tesorería', 'Presidente General'])) {
        $sqlFin = "SELECT tipo_movimiento, SUM(monto) AS total
                   FROM recursos_financieros
                   WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        if ($jacId) {
            $sqlFin .= " AND jac_id = :jac_id";
        }
        $sqlFin .= " GROUP BY tipo_movimiento";

        $stmt = $pdo->prepare($sqlFin);
        $stmt->execute($jacId ? [':jac_id' => $jacId] : []);
        $movs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ingresos = 0; $gastos = 0;
        foreach ($movs as $m) {
            if ($m['tipo_movimiento'] === 'Ingreso') $ingresos = (float) $m['total'];
            if ($m['tipo_movimiento'] === 'Gasto') $gastos = (float) $m['total'];
        }
        $contexto .= "RESUMEN FINANCIERO DEL MES ACTUAL: Ingresos \${$ingresos}, Gastos \${$gastos}, Saldo \$" . ($ingresos - $gastos) . "\n\n";
    }

    if (trim($contexto) === '') {
        $contexto = "No hay información registrada todavía en el sistema para esta consulta.\n";
    }

    /* ===========================
       Consultar a la IA
    =========================== */

    $instruccionSistema = "Eres el asistente virtual de AsoJuntaSys, un sistema de gestión para Juntas de Acción Comunal (JAC) en Colombia. "
        . "Responde SIEMPRE en español, de forma breve y clara (máximo 4-5 líneas), dirigiéndote a un usuario con rol '{$rol}'. "
        . "Responde ÚNICAMENTE con base en el siguiente CONTEXTO institucional. "
        . "Si la pregunta no se puede responder con este contexto, dilo honestamente y sugiere dónde podría consultarlo dentro del sistema (por ejemplo, el módulo de Actas, Documentos, Agenda o Financiero). "
        . "Nunca inventes datos que no estén en el contexto.\n\n"
        . "CONTEXTO:\n{$contexto}";

    $resultado = consultarGemini($instruccionSistema, $pregunta);

    if ($resultado['ok']) {
        registrarAuditoria($pdo, 'crear', 'asistente_virtual', null, "Consulta: " . mb_substr($pregunta, 0, 100));
    }

    echo json_encode($resultado);

} catch (PDOException $e) {
    error_log('consultar_asistente.php: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'respuesta' => 'Ocurrió un error al preparar la respuesta.']);
}