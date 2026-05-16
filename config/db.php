<?php
$host   = getenv('DB_HOST')              ?: 'mysql.railway.internal';
$dbname = getenv('DB_NAME')              ?: 'railway';
$user   = getenv('MYSQLUSER')            ?: 'root';
$pass   = getenv('MYSQL_ROOT_PASSWORD')  ?: 'BjJnNBPbpmyxmxCjYhXyrxYyzKnHSSkq';
$port   = getenv('MYSQLPORT')            ?: 3306;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("❌ No se pudo conectar a la base de datos.");
}
?>