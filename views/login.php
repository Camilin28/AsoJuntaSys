<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
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
      width: 900px;
      height: 500px;
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
      width: 105%;
    }

    .left-panel button:hover {
      background-color: #FBC02D;
      color: #333;
    }

    .signup-link {
      margin-top: 20px;
      font-size: 17px;
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

    .terminos-check {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 15px;
  font-size: 14px;
  color: #333;
}

.terminos-check input[type="checkbox"] {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.terminos-check a {
  color: #2E7D32;
  font-weight: bold;
  text-decoration: none;
}

.terminos-check a:hover {
  color: #FBC02D;
  text-decoration: underline;
}

.error-box {
  margin: 12px 0;
  padding: 10px;
  background-color: #ffebee;
  color: #c62828;
  border-left: 5px solid #c62828;
  border-radius: 6px;
  font-size: 14px;
  text-align: center;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
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
    echo "<div class='error-box'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['info_login'])) {
    echo "<div class='error-box' style='background:#e8f5e9;color:#2E7D32;'>" . htmlspecialchars($_SESSION['info_login']) . "</div>";
    unset($_SESSION['info_login']);
}
?>
      <form action="../public/procesar_login.php" method="POST">
        <label for="email">Usuario</label>
        <input type="email" name="email" required placeholder="Correo electrónico">

        <label for="password">Contraseña</label>
        <input type="password" name="password" required placeholder="Introduce tu contraseña">
  <div class="terminos-check">
    <input type="checkbox" id="acepta" name="acepta_terminos" required>
    <label for="acepta">
        He leído y acepto los 
        <a href="terminos.php" target="_blank">
            Términos y Condiciones
        </a>
    </label>
</div>
        <button type="submit">Iniciar Sesión</button>
        <a href="recuperar_password.php" style="display:block; text-align:center; margin-top:10px;">¿Olvidaste tu contraseña?</a>
        
      </form>

      
        </div>
    

    <!-- Panel derecho con el logo -->
    <div class="right-panel">
      <img src="../imagenes/Logo_AsojuntaSys.png" alt="Logo AsoJuntaSys">
    </div>
  </div>
  
</body>
</html>
