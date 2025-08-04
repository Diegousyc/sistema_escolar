<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}
include "conexion.php";
require_once "fpdf.php";// ✅ Asegúrate de tener fpdf.php en tu proyecto

$alumno_id = $_GET['alumno_id'] ?? 0;
$grupo_id = $_GET['grupo_id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

// ✅ Validar parámetros
if (!$alumno_id || !$grupo_id || !$carrera_id) {
    header("Location: index.php");
    exit();
}

// ✅ Datos del alumno
$alumno = $conexion->query("
    SELECT a.nombre AS alumno, g.nombre AS grupo, c.nombre AS carrera
    FROM alumnos a
    JOIN grupos g ON a.grupo_id = g.id
    JOIN carreras c ON g.carrera_id = c.id
    WHERE a.id = $alumno_id
")->fetch_assoc();

// ✅ Eliminar materia
if (isset($_GET['borrar_materia'])) {
    $materia_id = intval($_GET['borrar_materia']);
    $conexion->query("DELETE FROM materias WHERE id=$materia_id AND carrera_id=$carrera_id");
    $conexion->query("DELETE FROM calificaciones WHERE materia_id=$materia_id");
    header("Location: info.php?alumno_id=$alumno_id&grupo_id=$grupo_id&carrera_id=$carrera_id&materia_borrada=1");
    exit();
}

// ✅ Agregar nueva materia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_materia'])) {
    $nueva_materia = trim($_POST['nueva_materia']);
    if (!empty($nueva_materia)) {
        $existe = $conexion->query("SELECT * FROM materias WHERE nombre='$nueva_materia' AND carrera_id=$carrera_id");
        if ($existe->num_rows == 0) {
            $conexion->query("INSERT INTO materias(nombre, carrera_id) VALUES('$nueva_materia', $carrera_id)");
            header("Location: info.php?alumno_id=$alumno_id&grupo_id=$grupo_id&carrera_id=$carrera_id&materia_agregada=1");
            exit();
        } else {
            echo "<script>alert('⚠️ La materia ya existe en esta carrera.');</script>";
        }
    }
}

// ✅ Guardar calificaciones
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['materias'])) {
    foreach ($_POST['materias'] as $materia_id => $cal) {
        $c1 = floatval($cal['c1']);
        $c2 = floatval($cal['c2']);
        $c3 = floatval($cal['c3']);
        $promedio = round(($c1 + $c2 + $c3) / 3, 1);

        $existe = $conexion->query("SELECT * FROM calificaciones WHERE alumno_id=$alumno_id AND materia_id=$materia_id");
        if ($existe->num_rows > 0) {
            $conexion->query("UPDATE calificaciones SET c1=$c1, c2=$c2, c3=$c3, promedio=$promedio WHERE alumno_id=$alumno_id AND materia_id=$materia_id");
        } else {
            $conexion->query("INSERT INTO calificaciones(alumno_id, materia_id, c1, c2, c3, promedio)
                              VALUES($alumno_id, $materia_id, $c1, $c2, $c3, $promedio)");
        }
    }

    header("Location: info.php?alumno_id=$alumno_id&grupo_id=$grupo_id&carrera_id=$carrera_id&guardado=1");
    exit();
}

// ✅ Descargar PDF
if (isset($_GET['pdf'])) {
    $materias = $conexion->query("SELECT * FROM materias WHERE carrera_id=$carrera_id");
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Reporte de Calificaciones"), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, utf8_decode("Alumno: " . $alumno['alumno']), 0, 1);
    $pdf->Cell(0, 8, utf8_decode("Carrera: " . $alumno['carrera']), 0, 1);
    $pdf->Cell(0, 8, utf8_decode("Grupo: " . $alumno['grupo']), 0, 1);
    $pdf->Ln(5);

    // Encabezado tabla
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(60, 8, "Materia", 1, 0, 'C');
    $pdf->Cell(20, 8, "C1", 1, 0, 'C');
    $pdf->Cell(20, 8, "C2", 1, 0, 'C');
    $pdf->Cell(20, 8, "C3", 1, 0, 'C');
    $pdf->Cell(30, 8, "Promedio", 1, 1, 'C');

    $pdf->SetFont('Arial', '', 10);
    $materias = $conexion->query("SELECT * FROM materias WHERE carrera_id=$carrera_id");
    while ($m = $materias->fetch_assoc()) {
        $cal = $conexion->query("SELECT * FROM calificaciones WHERE alumno_id=$alumno_id AND materia_id={$m['id']}")->fetch_assoc();
        $c1 = $cal['c1'] ?? 0;
        $c2 = $cal['c2'] ?? 0;
        $c3 = $cal['c3'] ?? 0;
        $promedio = $cal['promedio'] ?? 0;

        $pdf->Cell(60, 8, utf8_decode($m['nombre']), 1);
        $pdf->Cell(20, 8, $c1, 1, 0, 'C');
        $pdf->Cell(20, 8, $c2, 1, 0, 'C');
        $pdf->Cell(20, 8, $c3, 1, 0, 'C');
        $pdf->Cell(30, 8, $promedio, 1, 1, 'C');
    }

    $pdf->Output('D', 'reporte_alumno.pdf');
    exit();
}

// ✅ Materias de la carrera
$materias = $conexion->query("SELECT * FROM materias WHERE carrera_id=$carrera_id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información del Alumno</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f0f8ff; }
        table { margin: auto; border-collapse: collapse; width: 85%; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        .aprobado { color: green; font-weight: bold; }
        .reprobado { color: red; font-weight: bold; }
        .btn { background: #2a9df4; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; margin: 2px; }
        .btn:hover { background: #0d6efd; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #a71d2a; }
        input[type="number"], input[type="text"] { width: 60px; text-align: center; }
        .container-btn { margin-top: 15px; }
    </style>
</head>
<body>
    <h1>📘 Información del Alumno</h1>
    <p><strong>Nombre:</strong> <?= $alumno['alumno']; ?></p>
    <p><strong>Carrera:</strong> <?= $alumno['carrera']; ?></p>
    <p><strong>Grupo:</strong> <?= $alumno['grupo']; ?></p>

    <?php if (isset($_GET['guardado'])): ?>
        <p style="color:green; font-weight:bold;">✅ Calificaciones guardadas correctamente.</p>
    <?php elseif (isset($_GET['materia_agregada'])): ?>
        <p style="color:blue; font-weight:bold;">➕ Materia agregada correctamente.</p>
    <?php elseif (isset($_GET['materia_borrada'])): ?>
        <p style="color:red; font-weight:bold;">🗑 Materia eliminada correctamente.</p>
    <?php endif; ?>

    <h2>Materias y Calificaciones</h2>
    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>C1</th>
                    <th>C2</th>
                    <th>C3</th>
                    <th>Promedio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = $materias->fetch_assoc()):
                    $cal = $conexion->query("SELECT * FROM calificaciones WHERE alumno_id=$alumno_id AND materia_id={$m['id']}")->fetch_assoc();
                    $c1 = $cal['c1'] ?? 0;
                    $c2 = $cal['c2'] ?? 0;
                    $c3 = $cal['c3'] ?? 0;
                    $promedio = $cal['promedio'] ?? 0;
                    $color = $promedio < 6 ? "reprobado" : "aprobado";
                ?>
                    <tr>
                        <td><?= $m['nombre'] ?></td>
                        <td><input type="number" name="materias[<?= $m['id'] ?>][c1]" value="<?= $c1 ?>" min="0" max="10" step="0.1"></td>
                        <td><input type="number" name="materias[<?= $m['id'] ?>][c2]" value="<?= $c2 ?>" min="0" max="10" step="0.1"></td>
                        <td><input type="number" name="materias[<?= $m['id'] ?>][c3]" value="<?= $c3 ?>" min="0" max="10" step="0.1"></td>
                        <td class="<?= $color ?>"><?= $promedio ?></td>
                        <td>
                            <a href="info.php?alumno_id=<?= $alumno_id ?>&grupo_id=<?= $grupo_id ?>&carrera_id=<?= $carrera_id ?>&borrar_materia=<?= $m['id'] ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('¿Seguro que deseas eliminar esta materia?');">🗑 Borrar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="container-btn">
            <button type="submit" class="btn">💾 Guardar Calificaciones</button>
            <button type="button" class="btn" onclick="location.href='alumnos.php?grupo_id=<?= $grupo_id ?>&carrera_id=<?= $carrera_id ?>'">🔙 Regresar</button>
            <button type="button" class="btn" onclick="location.href='info.php?alumno_id=<?= $alumno_id ?>&grupo_id=<?= $grupo_id ?>&carrera_id=<?= $carrera_id ?>&pdf=1'">📄 Descargar PDF</button>
        </div>
    </form>

    <h3>➕ Agregar Nueva Materia</h3>
    <form method="post" style="margin-top:10px;">
        <input type="text" name="nueva_materia" placeholder="Nombre de la nueva materia" required>
        <button type="submit" class="btn">Agregar Materia</button>
    </form>
</body>
</html>
