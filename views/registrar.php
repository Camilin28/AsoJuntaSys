<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole(['Presidente General']);

// Cargar JAC disponibles
$jacs = $pdo->query("SELECT id, nombre FROM juntas ORDER BY nombre ASC")->fetchAll();

$error   = $_SESSION['error']   ?? null;
$mensaje = $_SESSION['mensaje'] ?? null;

unset($_SESSION['error'], $_SESSION['mensaje']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario | AsoJuntaSys</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body{
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-header{
            background: linear-gradient(135deg, #15fd0d, #229808);
            color: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .main-card{
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .card-header-custom{
            background: white;
            border-bottom: 1px solid #f5f917;
            padding: 20px 25px;
        }

        .card-body{
            padding: 35px;
        }

        .form-label{
            font-weight: 600;
            color: #495057;
        }

        .form-control,
        .form-select{
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #d1dace;
            transition: 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus{
            border-color: #0dfd25;
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
        }

        .btn-primary{
            background: #fde50d;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .btn-primary:hover{
            background: #d0d70b;
            transform: translateY(-1px);
        }

        .btn-secondary-custom{
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 10px 16px;
            color: #495057;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .btn-secondary-custom:hover{
            background: #f1f3f5;
            color: #000;
        }

        .alert{
            border-radius: 12px;
        }

        .icon-box{
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

    </style>
</head>

<body>

<div class="container py-4">

    <!-- Encabezado -->
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">

        <div class="d-flex align-items-center gap-3">
            <div class="icon-box">
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <div>
                <h2 class="mb-1">Crear Usuario</h2>
                <p class="mb-0 opacity-75">
                    Registro de nuevos usuarios para el sistema AsoJuntaSys
                </p>
            </div>
        </div>

        <a href="dashboard_presidente.php" class="btn-secondary-custom mt-3 mt-md-0">
            <i class="fa-solid fa-arrow-left"></i>
            Volver al panel
        </a>

    </div>

    <!-- Mensajes -->
    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="alert alert-success shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i>
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Card principal -->
    <div class="card main-card">

        <div class="card-header-custom">
            <h5 class="mb-0">
                <i class="fa-solid fa-id-card me-2 text-primary"></i>
                Información del Usuario
            </h5>
        </div>

        <div class="card-body">

            <form method="POST" action="../public/procesar_registro.php">

                <!-- Nombre -->
                <div class="mb-4">
                    <label class="form-label">
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        placeholder="Ingrese el nombre completo"
                        required
                    >
                </div>

                <!-- Correo -->
                <div class="mb-4">
                    <label class="form-label">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="correo@ejemplo.com"
                        required
                    >
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label class="form-label">
                        Contraseña temporal
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="8"
                        placeholder="Mínimo 8 caracteres"
                        required
                    >

                    <div class="form-text mt-2">
                        El usuario podrá cambiar esta contraseña después.
                    </div>
                </div>

                <!-- Rol -->
                <div class="mb-4">
                    <label class="form-label">
                        Rol del usuario
                    </label>

                    <select name="rol" class="form-select" required>

                        <option value="">
                            — Selecciona un rol —
                        </option>

                        <option value="Presidentes de JAC">
                            Presidente de JAC
                        </option>

                        <option value="Secretaría">
                            Secretaría
                        </option>

                        <option value="Tesorería">
                            Tesorería
                        </option>

                    </select>
                </div>

                <!-- JAC -->
                <div class="mb-4">
                    <label class="form-label">
                        Junta de Acción Comunal
                    </label>

                    <select name="jac_id" class="form-select">

                        <option value="">
                            — Sin asignar —
                        </option>

                        <?php foreach ($jacs as $jac): ?>

                            <option value="<?= $jac['id'] ?>">
                                <?= htmlspecialchars($jac['nombre']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div class="form-text mt-2">
                        Puedes asignar o modificar la JAC posteriormente.
                    </div>
                </div>

                <!-- Botón -->
                <div class="d-grid mt-4">

                    <button type="submit" class="btn btn-primary">

                        <i class="fa-solid fa-user-plus me-2"></i>
                        Crear Usuario

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>