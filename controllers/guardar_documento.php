<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            $sql = "INSERT INTO documentos (titulo, descripcion, archivo, categoria_id, usuario_id, estado)
                    VALUES (:titulo, :descripcion, :archivo, :categoria_id, :usuario_id, 'Pendiente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':descripcion' => $descripcion,
                ':archivo' => $nombreArchivo,
                ':categoria_id' => $categoria_id,
                ':usuario_id' => $usuario_id
            ]);

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
