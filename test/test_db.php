<?php
$host = "sql3.freesqldatabase.com";
$dbname = "sql3774738";
$username = "sql3774738";
$password = "Ypt8bDM35B";
$port = 3306;

try {
    // Crear la conexión usando PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "❌ Error en la conexión: " . $e->getMessage();
}
?>
