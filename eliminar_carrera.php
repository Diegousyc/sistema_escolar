<?php
include "conexion.php";
$id = $_GET['id'] ?? 0;

// ✅ Borrar carrera y sus grupos/alumnos/materias en cascada
$conexion->query("DELETE FROM carreras WHERE id=$id");

header("Location: index.php");
exit;
?>
