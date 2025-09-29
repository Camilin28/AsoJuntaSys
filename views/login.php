<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - AsoJuntaSys</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #2E7D32 40%, #FBC02D 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      display: flex;
      width: 800px;
      height: 500px;
      background: #ff0000ff;
      border-radius: 50px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
      overflow: hidden;
    }

    /* Panel izquierdo (formulario) */
    .left-panel {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
    }

    .left-panel h2 {
      color: #2E7D32;
      margin-bottom: 20px;
    }

    .left-panel label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
      color: #333;
    }

    .left-panel input {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 2px solid #C8E6C9;
      border-radius: 8px;
      outline: none;
      transition: border 0.3s ease;
    }

    .left-panel input:focus {
      border-color: #FBC02D;
    }

    .left-panel button {
      margin-top: 20px;
      padding: 12px;
      background-color: #2E7D32;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
      width: 110%;
    }

    .left-panel button:hover {
      background-color: #FBC02D;
      color: #333;
    }

    .signup-link {
      margin-top: 20px;
      font-size: 14px;
      color: #333;
    }

    .signup-link a {
      color: #2E7D32;
      text-decoration: none;
      font-weight: bold;
    }

    .signup-link a:hover {
      color: #FBC02D;
    }

    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }

    /* Panel derecho (logo) */
.right-panel {
  flex: 1;
  background: radial-gradient(
    circle at 48% 45%, /* mueve el centro (X%, Y%) */
    #ffffff 0% 30%,   /* tamaño del blanco */
    #2E7D32 10%,      /* verde alrededor */
    #FBC02D 40%      /* amarillo en los bordes */
  );
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  text-align: center;
}

.right-panel img {
  max-width: 500px;
  height: 300px;
  z-index: 1;
  filter: drop-shadow(2px 2px 8px rgba(0,0,0,0.4));
}


  </style>
</head>
<body>
  <div class="container">
    <!-- Panel izquierdo -->
    <div class="left-panel">
      <h2>LOGIN</h2>

      <?php
      if (isset($_SESSION['error'])) {
          echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
          unset($_SESSION['error']);
      }
      ?>

      <form action="../public/procesar_login.php" method="POST">
        <label for="email">Usuario</label>
        <input type="email" name="email" required placeholder="Correo electrónico">

        <label for="password">Contraseña</label>
        <input type="password" name="password" required placeholder="Introduce tu contraseña">

        <button type="submit">Iniciar Sesión</button>
      </form>

      <p class="signup-link">¿No tienes cuenta? 
        <a href="../views/registrar.php">Por favor, Regístrate</a>
      </p>
    </div>

    <!-- Panel derecho con el logo -->
    <div class="right-panel">
      <img src="../imagenes/logo1.png" alt="Logo AsoJuntaSys">
    </div>
  </div>
</body>
</html>
