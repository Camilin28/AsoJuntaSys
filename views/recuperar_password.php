<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar contraseña - AsoJuntaSys</title>
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
  h2 {
    text-align: center;
    color: #2E7D32;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }
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
  .info-box {
    background: #e8f5e9;
    color: #2E7D32;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
  }
  .error-box {
    background: #fdecea;
    color: #c62828;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
  }
  .volver {
    display: block;
    text-align: center;
    margin-top: 15px;
  }
</style>
</head>
<body>
  <div class="card">
    <h2>Recuperar contraseña</h2>

    <?php if (isset($_SESSION['info'])): ?>
      <div class="info-box"><?= htmlspecialchars($_SESSION['info']) ?></div>
      <?php unset($_SESSION['info']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error-box"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="../controllers/procesar_recuperar_password.php" method="POST">
      <label for="email">Correo electrónico</label>
      <input type="email" name="email" required placeholder="Tu correo registrado">
      <button type="submit">Enviar enlace de recuperación</button>
    </form>

    <a class="volver" href="login.php">Volver al inicio de sesión</a>
  </div>
</body>
</html>