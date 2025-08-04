<link rel="stylesheet" href="style.css">
<?php
include "conexion.php";
$alumno_id = intval($_POST['alumno_id']);
$materia_id = intval($_POST['materia_id']);
$c1 = floatval($_POST['c1']);
$c2 = floatval($_POST['c2']);
$c3 = floatval($_POST['c3']);
$promedio = floatval($_POST['promedio']);

$existe = $conexion->query("SELECT id FROM calificaciones WHERE alumno_id=$alumno_id AND materia_id=$materia_id")->num_rows;

if($existe){
    $conexion->query("UPDATE calificaciones SET c1=$c1, c2=$c2, c3=$c3, promedio=$promedio WHERE alumno_id=$alumno_id AND materia_id=$materia_id");
}else{
    $conexion->query("INSERT INTO calificaciones (alumno_id, materia_id, c1, c2, c3, promedio)
                      VALUES ($alumno_id, $materia_id, $c1, $c2, $c3, $promedio)");
}
echo "ok";
?>
