<?php
include "conexion.php";
$grupo_id = $_GET['grupo_id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $conexion->query("INSERT INTO alumnos (nombre, grupo_id) VALUES ('$nombre', $grupo_id)");
        header("Location: alumnos.php?grupo_id=$grupo_id&carrera_id=$carrera_id");
        exit;
    } else {
        $error = "El nombre no puede estar vacío.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Alumno</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>➕ Agregar Alumno</h1>

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post" style="text-align:center; margin-top:20px;">
    <input type="text" name="nombre" placeholder="Nombre del alumno" required
           style="padding:8px; width:250px; border-radius:5px; border:1px solid #ccc;">
    <br><br>
    <button type="submit">✅ Guardar</button>
    <button type="button" onclick="location.href='alumnos.php?grupo_id=<?php echo $grupo_id; ?>&carrera_id=<?php echo $carrera_id; ?>'">🔙 Regresar</button>
  </form>
</body>
</html>
