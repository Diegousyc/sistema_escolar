<?php
session_start();
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alumno_id = $_POST['alumno_id'];

    $_SESSION['rol'] = 'alumno';
    $_SESSION['alumno_id'] = $alumno_id;

    header("Location: ver_calificaciones.php");
    exit;
}

$alumnos = $conexion->query("SELECT id, nombre FROM alumnos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Alumno</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login">
  <h1>🔐 Ingresar como Alumno</h1>
  <form method="POST">
    <label>Selecciona tu nombre:</label>
    <select name="alumno_id" required>
      <option value="">-- Selecciona --</option>
      <?php while ($a = $alumnos->fetch_assoc()): ?>
        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
      <?php endwhile; ?>
    </select>
    <br><br>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>
