<?php
include "conexion.php";
$id = $_GET['id'] ?? 0;

// ✅ Obtener la carrera actual
$carrera = $conexion->query("SELECT * FROM carreras WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $conexion->query("UPDATE carreras SET nombre='$nombre' WHERE id=$id");
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
  <title>Editar Carrera</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>✏ Editar Carrera</h1>

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post" style="text-align:center; margin-top:20px;">
    <input type="text" name="nombre" value="<?php echo $carrera['nombre']; ?>" required
           style="padding:8px; width:250px; border-radius:5px; border:1px solid #ccc;">
    <br><br>
    <button type="submit">✅ Actualizar</button>
    <button type="button" onclick="location.href='index.php'">🔙 Regresar</button>
  </form>
</body>
</html>
