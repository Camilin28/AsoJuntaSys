<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireLogin();

// Si el archivo excede post_max_size, PHP descarta todo el POST antes de
// que este script se ejecute — detectamos ese caso para dar un mensaje
// claro en vez de un error confuso.
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_POST) && empty($_FILES)) {
    $_SESSION['error_documento'] = "El archivo es demasiado grande. El tamaño máximo permitido es 10 MB.";
    header("Location: ../views/documentos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validarTokenCSRF($_POST['csrf_token'] ?? '');

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? null;
    $categoria_id = $_POST['categoria_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Errores específicos de subida (archivo individual > upload_max_filesize, etc.)
    if (!empty($_FILES['archivo']['error']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        $codigo = $_FILES['archivo']['error'];
        if (in_array($codigo, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
            $_SESSION['error_documento'] = "El archivo es demasiado grande. El tamaño máximo permitido es 10 MB.";
        } else {
            $_SESSION['error_documento'] = "Ocurrió un error al subir el archivo. Intenta de nuevo.";
        }
        header("Location: ../views/documentos.php");
        exit();
    }

    if (!empty($_FILES['archivo']['name'])) {
        $nombreArchivo = time() . '_' . basename($_FILES['archivo']['name']);
        $rutaDestino = "../uploads/documentos/" . $nombreArchivo;

        if (!is_dir("../uploads/documentos/")) {
            mkdir("../uploads/documentos/", 0777, true);
        }

        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
            $sql = "INSERT INTO documentos (titulo, descripcion, archivo, categoria_id, usuario_id, estado, jac_id)
                    VALUES (:titulo, :descripcion, :archivo, :categoria_id, :usuario_id, 'Pendiente', :jac_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':descripcion' => $descripcion,
                ':archivo' => $nombreArchivo,
                ':categoria_id' => $categoria_id,
                ':usuario_id' => $usuario_id,
                ':jac_id' => $_SESSION['jac_id'] ?? null
            ]);

            registrarAuditoria($pdo, 'crear', 'documento', (int) $pdo->lastInsertId(), $titulo);

            header("Location: ../views/documentos.php?success=1");
            exit();
        } else {
            die("Error al subir el archivo.");
        }
    } else {
        die("Debes seleccionar un archivo.");
    }
} else {
    header("Location: ../views/documentos.php");
    exit();
}
?>
