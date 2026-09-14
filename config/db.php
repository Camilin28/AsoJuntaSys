<?php
$required = [
    'MYSQLHOST',
    'MYSQL_DATABASE',
    'MYSQLUSER',
    'MYSQL_ROOT_PASSWORD',
    'MYSQLPORT',
];

$missing = [];
foreach ($required as $var) {
    $value = getenv($var);
    if ($value === false || $value === '') {
        $missing[] = $var;
    }
}

if (!empty($missing)) {
    error_log('db.php: faltan variables de entorno: ' . implode(', ', $missing));
    die("❌ Error de configuración del servidor. Contacta al administrador.");
}

$host   = getenv('MYSQLHOST');
$dbname = getenv('MYSQL_DATABASE');
$user   = getenv('MYSQLUSER');
$pass   = getenv('MYSQL_ROOT_PASSWORD');
$port   = getenv('MYSQLPORT');

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // El detalle real queda en los logs del servidor (Railway), nunca visible al usuario.
    error_log('db.php PDO connection error: ' . $e->getMessage());
    die("❌ No se pudo conectar a la base de datos.");
}