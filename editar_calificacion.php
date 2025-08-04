
<?php include "conexion.php"; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Editar Calificación</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    form { max-width: 400px; margin: auto; }
    label { display: block; margin-top: 10px; }
    input[type="number"] { width: 100%; padding: 8px; margin-top: 5px; }
    button { margin-top: 15px; padding: 10px; width: 100%; background-color: #007BFF; color: white; border: none; cursor: pointer; }
    button:hover { background-color: #0056b3; }
  </style>
</head>
<body>
<?php
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  // Obtener datos actuales
  $stmt = $conexion->prepare("SELECT calif1, calif2, calif3 FROM calificaciones WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($calif1, $calif2, $calif3);
    if ($stmt->fetch()) {
      // Mostrar formulario
      ?>
      <h2>Editar Calificación</h2>
      <form method="post">
        <label>Calificación 1:
          <input type="number" name="calif1" value="<?= $calif1 ?>" required min="0" max="10" step="0.1">
        </label>
        <label>Calificación 2:
          <input type="number" name="calif2" value="<?= $calif2 ?>" required min="0" max="10" step="0.1">
        </label>
        <label>Calificación 3:
          <input type="number" name="calif3" value="<?= $calif3 ?>" required min="0" max="10" step="0.1">
        </label>
        <button type="submit" name="guardar">Guardar Cambios</button>
      </form>
      <?php
    } else {
      echo "<p>No se encontró la calificación.</p>";
    }
    $stmt->close();
  }
} elseif (isset($_POST['guardar']) && isset($_GET['id'])) {
  // Guardar cambios
  $id = intval($_GET['id']);
  $c1 = floatval($_POST['calif1']);
  $c2 = floatval($_POST['calif2']);
  $c3 = floatval($_POST['calif3']);

  $stmt = $conexion->prepare("UPDATE calificaciones SET calif1 = ?, calif2 = ?, calif3 = ? WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("dddi", $c1, $c2, $c3, $id);
    if ($stmt->execute()) {
      echo "<p>✅ Calificaciones actualizadas correctamente.</p>";
    } else {
      echo "<p>Error al actualizar: " . $stmt->error . "</p>";
    }
    $stmt->close();
    echo "<a href='javascript:history.back()'>🔙 Volver</a>";
  }
} else {
  echo "<p>ID inválido o datos incompletos.</p>";
}
?>
</body>
</html>
