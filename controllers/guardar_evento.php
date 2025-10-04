<?php
require('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? "guardar";
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";
    $fecha = $_POST['fecha'] ?? "";
    $hora = $_POST['hora'] ?? "";
    $color = $_POST['color'] ?? "#3788d8"; // valor predeterminado

    try {
        if ($accion == "guardar") {
            if (empty($id)) {
                // Nuevo evento
                $sql = "INSERT INTO agenda (titulo, descripcion, fecha, hora, color) 
                        VALUES (:titulo, :descripcion, :fecha, :hora, :color)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':fecha' => $fecha,
                    ':hora' => $hora,
                    ':color' => $color
                ]);
                header("Location: ../views/agenda.php?msg=add");
            } else {
                // Editar evento
                $sql = "UPDATE agenda SET titulo=:titulo, descripcion=:descripcion, fecha=:fecha, hora=:hora, color=:color WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':fecha' => $fecha,
                    ':hora' => $hora,
                    ':color' => $color,
                    ':id' => $id
                ]);
                header("Location: ../views/agenda.php?msg=edit");
            }
            exit();
        } elseif ($accion == "eliminar") {
            $sql = "DELETE FROM agenda WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            header("Location: ../views/agenda.php?msg=delete");
            exit();
        }
    } catch (PDOException $e) {
        die("❌ Error en la operación: " . $e->getMessage());
    }
} else {
    header("Location: ../views/agenda.php");
    exit();
}
