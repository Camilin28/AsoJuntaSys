<?php
// DEBUG TEMPORAL — quitar antes de entregar
$host   = getenv('MYSQL_HOST')           ?: getenv('DB_HOST')   ?: 'mysql.railway.internal';
$dbname = getenv('MYSQL_DATABASE')       ?: getenv('DB_NAME')   ?: 'railway';
$user   = getenv('MYSQLUSER')            ?: getenv('MYSQL_USER') ?: 'root';
$pass   = getenv('MYSQL_ROOT_PASSWORD')  ?: getenv('MYSQL_PASSWORD') ?: '';
$port   = getenv('MYSQLPORT')            ?: getenv('MYSQL_PORT') ?: 3306;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Muestra el error real solo hasta resolver el problema
    die("❌ Error de conexión: " . $e->getMessage() . 
        "<br>Host: $host | DB: $dbname | User: $user | Port: $port");
}
?>