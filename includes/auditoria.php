<?php
/**
 * RF-024: Auditoría y registro de actividad.
 * El sistema registra acciones relevantes (login, modificaciones,
 * eliminaciones) para trazabilidad y detección de uso indebido.
 *
 * La auditoría NUNCA debe romper la funcionalidad principal: si falla
 * el registro, se anota en el log de errores del servidor y la
 * operación original continúa con normalidad.
 */

function registrarAuditoria(PDO $pdo, string $accion, ?string $entidad = null, ?int $entidadId = null, ?string $detalles = null): void {
    try {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        $usuarioNombre = $_SESSION['usuario_nombre'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO auditoria (usuario_id, usuario_nombre, accion, entidad, entidad_id, detalles, ip)
            VALUES (:usuario_id, :usuario_nombre, :accion, :entidad, :entidad_id, :detalles, :ip)
        ");
        $stmt->execute([
            ':usuario_id'     => $usuarioId,
            ':usuario_nombre' => $usuarioNombre,
            ':accion'         => $accion,
            ':entidad'        => $entidad,
            ':entidad_id'     => $entidadId,
            ':detalles'       => $detalles,
            ':ip'             => $ip,
        ]);
    } catch (PDOException $e) {
        error_log('auditoria: ' . $e->getMessage());
    }
}
