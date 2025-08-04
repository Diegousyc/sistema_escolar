<?php
include "conexion.php";
$carrera_id = $_GET['carrera_id'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $conexion->query("INSERT INTO grupos (nombre, carrera_id) VALUES ('$nombre', $carrera_id)");
        header("Location: grupos.php?carrera_id=$carrera_id");
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
  <title>Nuevo Grupo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>➕ Agregar Nuevo Grupo</h1>

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post" style="text-align:center; margin-top:20px;">
    <input type="text" name="nombre" placeholder="Nombre del grupo" required
           style="padding:8px; width:250px; border-radius:5px; border:1px solid #ccc;">
    <br><br>
    <button type="submit">✅ Guardar</button>
    <button type="button" onclick="location.href='grupos.php?carrera_id=<?php echo $carrera_id; ?>'">🔙 Regresar</button>
  </form>
</body>
</html>
