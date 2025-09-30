<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Movimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f9fbe7, #fffde7);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-card {
            max-width: 750px;
            margin: 50px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 8px 8px 25px rgba(0,0,0,0.15);
            padding: 40px;
        }

        .form-card h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #2E7D32;
        }

        .form-label i {
            margin-right: 6px;
            color: #2E7D32;
        }

        .form-control, .form-select, textarea {
            border-radius: 10px;
            border: 1px solid #ccc;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 6px rgba(46,125,50,0.6);
        }

        .btn-primary {
            background: #2E7D32;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #1B5E20;
        }

        .btn-secondary {
            background-color: #FBC02D;
            border: none;
            color: #000;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: bold;
        }

        .btn-secondary:hover {
            background-color: #F9A825;
            color: #fff;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-card">
        <h2><i class="bi bi-cash-stack"></i> Registrar Movimiento Financiero</h2>
        <form action="../controllers/guardar_movimiento.php" method="POST">
            <div class="mb-3">
                <label for="descripcion" class="form-label">
                    <i class="bi bi-card-text"></i> Descripción
                </label>
                <input type="text" class="form-control" name="descripcion" required>
            </div>

            <div class="mb-3">
                <label for="tipo_movimiento" class="form-label">
                    <i class="bi bi-arrow-left-right"></i> Tipo de Movimiento
                </label>
                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                    <option value="">Seleccione...</option>
                    <option value="Ingreso">Ingreso</option>
                    <option value="Gasto">Gasto</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Donacion">Donación</option>
                    <option value="Subsidio">Subsidio</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="mb-3 hidden" id="otroClasificacionDiv">
                <label for="otro_clasificacion" class="form-label">
                    <i class="bi bi-diagram-3"></i> Clasificación de "Otro"
                </label>
                <select class="form-select" name="otro_clasificacion">
                    <option value="">Seleccione...</option>
                    <option value="Ingreso">Ingreso</option>
                    <option value="Egreso">Egreso</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="monto" class="form-label">
                    <i class="bi bi-currency-dollar"></i> Monto
                </label>
                <input type="number" class="form-control" name="monto" min="1" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">
                    <i class="bi bi-calendar-event"></i> Fecha
                </label>
                <input type="date" class="form-control" name="fecha" required>
            </div>

            <div class="mb-3">
                <label for="responsable" class="form-label">
                    <i class="bi bi-person-circle"></i> Responsable
                </label>
                <input type="text" class="form-control" name="responsable" 
                       value="<?= htmlspecialchars($nombre) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">
                    <i class="bi bi-pencil-square"></i> Observaciones
                </label>
                <textarea class="form-control" name="observaciones" rows="3"></textarea>
            </div>

            <div class="form-footer">
                <a href="dashboard_tesoreria.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('tipo_movimiento').addEventListener('change', function () {
        const otroDiv = document.getElementById('otroClasificacionDiv');
        if (this.value === 'Otro') {
            otroDiv.classList.remove('hidden');
        } else {
            otroDiv.classList.add('hidden');
        }
    });
</script>
</body>
</html>
