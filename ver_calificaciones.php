<?php
session_start();
include "conexion.php";

// ✅ Verificar si es un alumno logueado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'alumno' || !isset($_SESSION['alumno_id'])) {
    header("Location: login.php");
    exit;
}

$alumno_id = $_SESSION['alumno_id'];

// ✅ Obtener datos del alumno
$sql_alumno = $conexion->query("
    SELECT a.id, a.nombre, g.nombre AS grupo, c.nombre AS carrera
    FROM alumnos a
    JOIN grupos g ON a.grupo_id = g.id
    JOIN carreras c ON g.carrera_id = c.id
    WHERE a.id = $alumno_id
");

if (!$sql_alumno || $sql_alumno->num_rows == 0) {
    die("<p style='color:red; font-weight:bold;'>❌ No se encontraron datos del alumno.</p>");
}

$alumno = $sql_alumno->fetch_assoc();

// ✅ Buscar materias y calificaciones del alumno
$sql_materias = $conexion->query("
    SELECT m.nombre, cal.c1, cal.c2, cal.c3, cal.promedio
    FROM calificaciones cal
    JOIN materias m ON cal.materia_id = m.id
    WHERE cal.alumno_id = $alumno_id
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Calificaciones</title>
  <link rel="stylesheet" href="styles_calificaciones.css">
</head>
<body class="calificaciones">
  <div class="container">
    <h1>📘 Mis Calificaciones</h1>
    <div class="datos">
      <p><strong>👤 Nombre:</strong> <?= htmlspecialchars($alumno['nombre']) ?></p>
      <p><strong>🎓 Carrera:</strong> <?= htmlspecialchars($alumno['carrera']) ?></p>
      <p><strong>👥 Grupo:</strong> <?= htmlspecialchars($alumno['grupo']) ?></p>
    </div>

    <h2>Materias y Promedios</h2>
    <?php if ($sql_materias && $sql_materias->num_rows > 0): ?>
    <div class="tabla-container">
      <table>
        <thead>
          <tr>
            <th>📖 Materia</th>
            <th>C1</th>
            <th>C2</th>
            <th>C3</th>
            <th>Promedio</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($m = $sql_materias->fetch_assoc()):
              $color = $m['promedio'] < 6 ? "reprobado" : "aprobado";
          ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><?= $m['c1'] ?></td>
            <td><?= $m['c2'] ?></td>
            <td><?= $m['c3'] ?></td>
            <td class="<?= $color ?>"><?= $m['promedio'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p class="aviso">⚠️ Aún no hay calificaciones registradas.</p>
    <?php endif; ?>

    <button class="btn-salir" onclick="location.href='logout.php'">🚪 Cerrar Sesión</button>
  </div>
</body>
</html>
