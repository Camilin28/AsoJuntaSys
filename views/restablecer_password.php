<?php
session_start();
require_once '../config/db.php';

$token = $_GET['token'] ?? '';
$tokenValido = false;

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_token_expira > NOW()");
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $tokenValido = (bool) $usuario;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">
<meta charset="UTF-8">
<title>Restablecer contraseña - AsoJuntaSys</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
  }
  .card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 380px;
  }
  h2 { text-align: center; color: #2E7D32; }
  label { display: block; margin-top: 15px; font-weight: bold; }
  input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    box-sizing: border-box;
  }
  button {
    width: 100%;
    margin-top: 20px;
    padding: 10px;
    background: #2E7D32;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  .error-box {
    background: #fdecea;
    color: #c62828;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
  }
  .volver { display: block; text-align: center; margin-top: 15px; }
</style>
</head>
<body>
  <div class="card">
    <h2>Restablecer contraseña</h2>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error-box"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!$tokenValido): ?>
      <div class="error-box">
        Este enlace ya no es válido. Puede haber expirado (60 minutos) o ya haber sido usado.
      </div>
      <a class="volver" href="recuperar_password.php">Solicitar un nuevo enlace</a>
    <?php else: ?>
      <form action="../controllers/procesar_restablecer_password.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">Nueva contraseña</label>
        <input type="password" name="password" required minlength="8" placeholder="Mínimo 8 caracteres">

        <label for="password_confirmar">Confirmar nueva contraseña</label>
        <input type="password" name="password_confirmar" required minlength="8" placeholder="Repite la contraseña">

        <button type="submit">Cambiar contraseña</button>
      </form>
    <?php endif; ?>

    <a class="volver" href="login.php">Volver al inicio de sesión</a>
  </div>
</body>
</html>
