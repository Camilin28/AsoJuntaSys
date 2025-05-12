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

    echo "✅ Usuario actualizado con éxito.";
    header("Location: listar_usuario.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        /* Estilo del formulario */
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
            color: #333;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1), inset -2px -2px 5px rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1), inset -2px -2px 5px rgba(255, 255, 255, 0.5), 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            width: 50%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2), -5px -5px 10px rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:active {
            transform: scale(0.98);
        }

        .botones {
            display: flex;
            gap: 0.5rem;
        }

        /* Mensajes de éxito o error */
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
    <div>
        <h2>Editar Usuario</h2>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $usuario['email'] ?>" required>

            <div class="botones">
                <button type="submit">Actualizar</button>
                <button type="submit">Regresar</button>
            </div>
        </form>
        
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "<p class='mensaje'>✅ Usuario actualizado con éxito.</p>";
        }
        ?>
    </div>
</body>
</html>
