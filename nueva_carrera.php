<?php
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre)) {
        $conexion->query("INSERT INTO carreras (nombre) VALUES ('$nombre')");
        header("Location: index.php");
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
  <title>Nueva Carrera</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>➕ Agregar Nueva Carrera</h1>
  
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post" style="text-align:center; margin-top:20px;">
    <input type="text" name="nombre" placeholder="Nombre de la carrera" required
           style="padding:8px; width:250px; border-radius:5px; border:1px solid #ccc;">
    <br><br>
    <button type="submit">✅ Guardar</button>
    <button type="button" onclick="location.href='index.php'">🔙 Regresar</button>
  </form>
</body>
</html>
