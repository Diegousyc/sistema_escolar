<?php
session_start();
include "conexion.php";

// ✅ Verificar que el usuario sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php"); // redirige al login
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - Sistema de Calificaciones</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body.index {
      font-family: Arial, sans-serif;
      background: #f0f4f8;
      color: #333;
      text-align: center;
      padding: 20px;
    }
    h1 {
      color: #2c3e50;
      margin-bottom: 20px;
    }
    ul#listaCarreras {
      list-style: none;
      padding: 0;
      max-width: 400px;
      margin: 0 auto 20px auto;
    }
    ul#listaCarreras li {
      background: #ffffff;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    ul#listaCarreras a {
      font-size: 18px;
      color: #2980b9;
      text-decoration: none;
      font-weight: bold;
    }
    ul#listaCarreras button {
      margin: 5px 2px;
      padding: 6px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    ul#listaCarreras button:nth-child(2) {
      background: #f39c12;
      color: #fff;
    }
    ul#listaCarreras button:nth-child(3) {
      background: #e74c3c;
      color: #fff;
    }
    .container-btn {
      margin-top: 20px;
    }
    #agregarCarrera {
      background: #27ae60;
      color: #fff;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      margin-right: 10px;
    }
    #salir {
      background: #c0392b;
      color: #fff;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    #agregarCarrera:hover {
      background: #2ecc71;
    }
    #salir:hover {
      background: #e74c3c;
    }
  </style>
</head>
<body class="index">
  <h1>📘 Panel de Administración</h1>
  <p><strong>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> (Administrador)</strong></p>

  <ul id="listaCarreras">
    <?php
    $carreras = $conexion->query("SELECT * FROM carreras");
    while($c = $carreras->fetch_assoc()) {
        echo "<li>
                <a href='grupos.php?carrera_id={$c['id']}'>".$c['nombre']."</a><br>
                <button onclick=\"location.href='editar_carrera.php?id={$c['id']}'\">✏ Editar</button>
                <button onclick=\"if(confirm('¿Seguro de eliminar esta carrera?')) location.href='eliminar_carrera.php?id={$c['id']}'\">🗑 Borrar</button>
              </li>";
    }
    ?>
  </ul>

  <div class="container-btn">
    <button id="agregarCarrera" onclick="location.href='nueva_carrera.php'">➕ Agregar Carrera</button>
    <button id="salir" onclick="location.href='logout.php'">🚪 Cerrar Sesión</button>
  </div>
</body>
</html>
