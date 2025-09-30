<?php
require '../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id]);

    header("Location: listar_usuario.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        :root {
            --bg-color: #81c784;
            --form-bg: #ffffff;
            --primary-color: #2e7d32;
            --secondary-color: #fbc02d;
            --text-color: #81c784;
            --input-bg: #fff4c9;
            --focus-border: #2e7d32;
            --shadow-light:#2e7d32;
            --shadow-dark: #fbc02d ;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: var(--form-bg);
            padding: 40px;
            border-radius: 15px;
            box-shadow:
                0 10px 10px var(--shadow-dark),
                0 -10px 10px var(--shadow-light),
                inset 0 0 10px #424242;
            width: 100%;
            max-width: 400px;
        }

        h3 {
            text-align: center;
            color: var(--text-color);
        }

        label {
            display: block;
            margin: 10px 0 1px;
            font-size: 15px;
            color: var(--text-color);
        }

        input[type="text"], input[type="email"] {
            width: 95%;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background-color: var(--input-bg);
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            outline: none;
            border-color: var(--focus-border);
            box-shadow: 0 0 8px var(--secondary-color);
        }

        .botones {
            display: flex;
            justify-content: space-between;
        }

        button {
            flex: 1;
            padding: 12px;
            margin: 0 5px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 3px 3px 6px rgba(0,0,0,0.2);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            opacity: 0.9;
        }

        button:active {
            transform: scale(0.97);
        }

        .mensaje {
            text-align: center;
            margin-top: 20px;
            color: green;
        }

        .error {
            text-align: center;
            margin-top: 20px;
            color: red;
        }
    </style>
</head>
<body>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        <h3>Editar Usuario</h3>

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= $usuario['email'] ?>" required>

        <div class="botones">
            <button type="submit">Actualizar</button>
            <button type="button" onclick="window.location.href='listar_usuario.php'">Regresar</button>
        </div>
    </form>
</body>
</html>
