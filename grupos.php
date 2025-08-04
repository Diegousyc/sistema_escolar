<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}
include "conexion.php";

$carrera_id = $_GET['carrera_id'] ?? 0;

// ✅ Si no hay carrera seleccionada, redirige al index
if (!$carrera_id) {
    header("Location: index.php");
    exit();
}

// ✅ Nombre de la carrera
$carrera = $conexion->query("SELECT * FROM carreras WHERE id=$carrera_id")->fetch_assoc();

// ✅ Lista de grupos
$grupos = $conexion->query("SELECT * FROM grupos WHERE carrera_id=$carrera_id");

// ✅ Agregar nuevo grupo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nuevo_grupo'])) {
    $nuevo_grupo = trim($_POST['nuevo_grupo']);
    if (!empty($nuevo_grupo)) {
        $existe = $conexion->query("SELECT * FROM grupos WHERE nombre='$nuevo_grupo' AND carrera_id=$carrera_id");
        if ($existe->num_rows == 0) {
            $conexion->query("INSERT INTO grupos(nombre, carrera_id) VALUES('$nuevo_grupo', $carrera_id)");
            header("Location: grupos.php?carrera_id=$carrera_id");
            exit();
        } else {
            echo "<script>alert('⚠️ El grupo ya existe en esta carrera.');</script>";
        }
    }
}

// ✅ Eliminar grupo
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM grupos WHERE id=$id");
    header("Location: grupos.php?carrera_id=$carrera_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Grupos de <?= $carrera['nombre'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f0f8ff; }
        h1 { color: #2a9df4; }
        table { margin: 1rem auto; border-collapse: collapse; width: 60%; background: #fff; box-shadow: 0 0 5px #ccc; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        .btn { background: #2a9df4; color: #fff; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0d6efd; }
        .container-btn { margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>📘 Grupos de <?= $carrera['nombre'] ?></h1>

    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($grupos->num_rows == 0): ?>
                <tr><td colspan="2">⚠️ No hay grupos registrados.</td></tr>
            <?php else: while ($g = $grupos->fetch_assoc()): ?>
                <tr>
                    <td><?= $g['nombre'] ?></td>
                    <td>
                        <button class="btn" onclick="location.href='alumnos.php?grupo_id=<?= $g['id'] ?>&carrera_id=<?= $carrera_id ?>'">👨‍🎓 Ver Alumnos</button>
                        <button class="btn" onclick="if(confirm('¿Eliminar este grupo?')) location.href='grupos.php?carrera_id=<?= $carrera_id ?>&eliminar=<?= $g['id'] ?>'">🗑 Eliminar</button>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>

    <div class="container-btn">
        <form method="post" style="display:inline;">
            <input type="text" name="nuevo_grupo" placeholder="Nuevo grupo" required>
            <button class="btn" type="submit">➕ Agregar Grupo</button>
        </form>
        <button class="btn" onclick="location.href='index.php'">🔙 Regresar</button>
    </div>
</body>
</html>
