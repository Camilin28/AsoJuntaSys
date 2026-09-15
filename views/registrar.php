<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';
requireRole(['Presidente General']);
// Cargar JAC disponibles (solo activas, para no asignar usuarios a una JAC desactivada)
$jacs = $pdo->query("
    SELECT id, nombre 
    FROM juntas 
    WHERE estado = 'Activa'
    ORDER BY nombre ASC
")->fetchAll();
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
        /* =========================
           PALETA ASOJUNTASYS
        ========================= */
        :root{
            --verde-principal: #2E7D32;
            --amarillo-principal: #FBC02D;
            --blanco: #FFFFFF;
            --verde-secundario: #81C784;
            --amarillo-suave: #FFF9C4;
            --gris-oscuro: #424242;
        }
        /* =========================
           BODY
        ========================= */
        body{
            background: #f5f5f5;
            font-family: 'Segoe UI', sans-serif;
            color: var(--gris-oscuro);
        }
        /* =========================
           HEADER
        ========================= */
        .page-header{
            background: linear-gradient(
                135deg,
                var(--verde-principal),
                #1B5E20
            );
            color: var(--blanco);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(46,125,50,0.18);
        }
        .icon-box{
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;

        }
        /* =========================
           CARD
        ========================= */
        .main-card{
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .card-header-custom{
            background: var(--amarillo-suave);
            border-bottom: 2px solid var(--amarillo-principal);
            padding: 20px 25px;
        }
        .card-body{
            padding: 35px;
            background: var(--blanco);
        }
        /* =========================
           LABELS
        ========================= */
        .form-label{
            font-weight: 600;
            color: var(--gris-oscuro);
        }
        /* =========================
           INPUTS
        ========================= */
        .form-control,
        .form-select{
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #d6d6d6;
            transition: 0.2s ease;
            background: #fff;
        }
        .form-control:focus,
        .form-select:focus{
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 0.15rem rgba(46,125,50,0.15);
        }
        /* =========================
           BOTÓN PRINCIPAL
        ========================= */
        .btn-primary{
            background: var(--verde-principal);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.2s ease;
        }
        .btn-primary:hover{

            background: #1B5E20;

            transform: translateY(-1px);

        }

        /* =========================
           BOTÓN VOLVER
        ========================= */

        .btn-secondary-custom{

            background: var(--amarillo-principal);

            border: none;

            border-radius: 12px;

            padding: 10px 16px;

            color: var(--gris-oscuro);

            text-decoration: none;

            font-weight: 600;

            transition: 0.2s ease;

        }

        .btn-secondary-custom:hover{

            background: #f9b800;

            color: #000;

        }

        /* =========================
           ALERTAS
        ========================= */

        .alert{

            border-radius: 12px;

            border: none;

        }

        .alert-danger{

            background: #ffebee;

            color: #b71c1c;

        }

        .alert-success{

            background: #e8f5e9;

            color: #1b5e20;

        }

        /* =========================
           TEXTOS
        ========================= */

        .form-text{

            color: #666;

        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px){

            .page-header{
                text-align: center;
            }

            .card-body{
                padding: 25px;
            }

        }

    </style>

</head>

<body>

<div class="container py-4">

    <!-- HEADER -->

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">

        <div class="d-flex align-items-center gap-3">

            <div class="icon-box">

                <i class="fa-solid fa-user-plus"></i>

            </div>

            <div>

                <h2 class="mb-1">
                    Crear Usuario
                </h2>

                <p class="mb-0 opacity-75">
                    Registro de nuevos usuarios del sistema AsoJuntaSys
                </p>

            </div>

        </div>

        <a href="dashboard_presidente.php" class="btn-secondary-custom mt-3 mt-md-0">

            <i class="fa-solid fa-arrow-left me-2"></i>

            Volver al panel

        </a>

    </div>

    <!-- MENSAJES -->

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

    <!-- CARD PRINCIPAL -->

    <div class="card main-card">

        <div class="card-header-custom">

            <h5 class="mb-0">

                <i class="fa-solid fa-id-card me-2" style="color: #2E7D32;"></i>

                Información del Usuario

            </h5>

        </div>

        <div class="card-body">

            <form method="POST" action="../public/procesar_registro.php">

                <!-- NOMBRE -->

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

                <!-- EMAIL -->

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

                <!-- PASSWORD -->

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

                <!-- ROL -->

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

                <!-- BOTÓN -->

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