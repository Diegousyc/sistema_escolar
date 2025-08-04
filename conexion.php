<?php
$host = "localhost";
$user = "root";   // Cambia por el usuario de MySQL en Neubox
$pass = ""; // Cambia por la contraseña de MySQL
$db = "sistema_calificaciones";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}
?>
