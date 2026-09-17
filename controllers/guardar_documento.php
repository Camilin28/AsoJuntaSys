<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validarTokenCSRF($_POST['csrf_token'] ?? '');

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? null;
    $categoria_id = $_POST['categoria_id'];
    $usuario_id = $_SESSION['usuario_id'];

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
