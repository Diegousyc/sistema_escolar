<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}
include "conexion.php";
$id = $_GET['id'] ?? 0;
$grupo_id = $_GET['grupo_id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

$alumno = $conexion->query("SELECT * FROM alumnos WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $conexion->query("UPDATE alumnos SET nombre='$nombre' WHERE id=$id");
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
  <title>Editar Alumno</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>✏ Editar Alumno</h1>

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post" style="text-align:center; margin-top:20px;">
    <input type="text" name="nombre" value="<?php echo $alumno['nombre']; ?>" required
           style="padding:8px; width:250px; border-radius:5px; border:1px solid #ccc;">
    <br><br>
    <button type="submit">✅ Actualizar</button>
    <button type="button" onclick="location.href='alumnos.php?grupo_id=<?php echo $grupo_id; ?>&carrera_id=<?php echo $carrera_id; ?>'">🔙 Regresar</button>
  </form>
</body>
</html>
