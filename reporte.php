<link rel="stylesheet" href="style.css">
<?php
include "conexion.php";
$alumno_id = $_GET['alumno_id'];

$alumno = $conexion->query("
SELECT a.nombre AS alumno, g.nombre AS grupo, c.nombre AS carrera
FROM alumnos a
JOIN grupos g ON a.grupo_id=g.id
JOIN carreras c ON g.carrera_id=c.id
WHERE a.id=$alumno_id")->fetch_assoc();

$materias = $conexion->query("SELECT m.nombre, cal.c1, cal.c2, cal.c3, cal.promedio
                              FROM calificaciones cal
                              JOIN materias m ON cal.materia_id=m.id
                              WHERE cal.alumno_id=$alumno_id");

$total = 0; $contador = 0;
while($tmp = $materias->fetch_assoc()) {
    $total += $tmp['promedio'];
    $contador++;
    $dataMaterias[] = $tmp;
}
$promedioGeneral = ($contador > 0) ? $total / $contador : 0;
$liberado = $promedioGeneral >= 6 ? "✅ Liberado" : "❌ No Liberado";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de <?php echo $alumno['alumno']; ?></title>
  <link rel="stylesheet" href="style-reporte.css">
</head>
<body class="reporte">
  <h1>📊 Reporte de Calificaciones</h1>
  <p><strong>Alumno:</strong> <?php echo $alumno['alumno']; ?></p>
  <p><strong>Carrera:</strong> <?php echo $alumno['carrera']; ?></p>
  <p><strong>Grupo:</strong> <?php echo $alumno['grupo']; ?></p>

  <table>
    <tr><th>Materia</th><th>C1</th><th>C2</th><th>C3</th><th>Promedio</th></tr>
    <?php
    foreach($dataMaterias as $m) {
        $color = $m['promedio'] < 6 ? "style='color:red;'" : "style='color:green;'";
        echo "<tr>
          <td>{$m['nombre']}</td>
          <td>{$m['c1']}</td>
          <td>{$m['c2']}</td>
          <td>{$m['c3']}</td>
          <td $color>".number_format($m['promedio'],1)."</td>
        </tr>";
    }
    ?>
  </table>

  <p class="promedio">Promedio General: <strong><?php echo number_format($promedioGeneral,2); ?></strong></p>
  <p class="promedio"><?php echo $liberado; ?></p>

  <div class="container-btn">
    <button onclick="location.href='reporte_pdf.php?alumno_id=<?php echo $alumno_id; ?>'">⬇ Descargar PDF</button>
    <button onclick="history.back()">🔙 Regresar</button>
  </div>
</body>
</html>
