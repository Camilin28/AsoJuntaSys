<?php
$host = "sql8.freesqldatabase.com";
$dbname = "sql8771202";
$username = "sql8771202";
$password = "wzusHXWCn4";
$port = 3306;

try {
    // Crear la conexión usando PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Verificar la conexión
    // echo "✅ Conexión exitosa a la base de datos."; // (Puedes descomentar esta línea para probar)
} catch (PDOException $e) {
    die("❌ Error en la conexión: " . $e->getMessage());
}
?>
