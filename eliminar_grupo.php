<?php
include "conexion.php";
$id = $_GET['id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

$conexion->query("DELETE FROM grupos WHERE id=$id");

header("Location: grupos.php?carrera_id=$carrera_id");
exit;
?>
