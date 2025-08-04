<?php
include "conexion.php";
$id = $_GET['id'] ?? 0;
$grupo_id = $_GET['grupo_id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

$conexion->query("DELETE FROM alumnos WHERE id=$id");

header("Location: alumnos.php?grupo_id=$grupo_id&carrera_id=$carrera_id");
exit;
?>
