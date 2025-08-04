<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}
include "conexion.php";

$grupo_id = $_GET['grupo_id'] ?? 0;
$carrera_id = $_GET['carrera_id'] ?? 0;

// ✅ Si no hay grupo, regresa a grupos
if (!$grupo_id || !$carrera_id) {
    header("Location: index.php");
    exit();
}

// ✅ Datos del grupo
$grupo = $conexion->query("SELECT * FROM grupos WHERE id=$grupo_id")->fetch_assoc();
$carrera = $conexion->query("SELECT * FROM carreras WHERE id=$carrera_id")->fetch_assoc();

// ✅ Lista de alumnos
$alumnos = $conexion->query("SELECT * FROM alumnos WHERE grupo_id=$grupo_id");

// ✅ Agregar alumno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nuevo_alumno'])) {
    $nuevo_alumno = trim($_POST['nuevo_alumno']);
    if (!empty($nuevo_alumno)) {
        $conexion->query("INSERT INTO alumnos(nombre, grupo_id) VALUES('$nuevo_alumno', $grupo_id)");
        header("Location: alumnos.php?grupo_id=$grupo_id&carrera_id=$carrera_id");
        exit();
    }
}

// ✅ Eliminar alumno
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM alumnos WHERE id=$id");
    header("Location: alumnos.php?grupo_id=$grupo_id&carrera_id=$carrera_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos de <?= $grupo['nombre'] ?></title>
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
    <h1>👨‍🎓 Alumnos de <?= $grupo['nombre'] ?> (<?= $carrera['nombre'] ?>)</h1>

    <table>
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($alumnos->num_rows == 0): ?>
                <tr><td colspan="2">⚠️ No hay alumnos registrados.</td></tr>
            <?php else: while ($a = $alumnos->fetch_assoc()): ?>
                <tr>
                    <td><?= $a['nombre'] ?></td>
                    <td>
                        <button class="btn" onclick="location.href='info.php?alumno_id=<?= $a['id'] ?>&grupo_id=<?= $grupo_id ?>&carrera_id=<?= $carrera_id ?>'">📘 Ver Calificaciones</button>
                        <button class="btn" onclick="if(confirm('¿Eliminar este alumno?')) location.href='alumnos.php?grupo_id=<?= $grupo_id ?>&carrera_id=<?= $carrera_id ?>&eliminar=<?= $a['id'] ?>'">🗑 Eliminar</button>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>

    <div class="container-btn">
        <form method="post" style="display:inline;">
            <input type="text" name="nuevo_alumno" placeholder="Nuevo alumno" required>
            <button class="btn" type="submit">➕ Agregar Alumno</button>
        </form>
        <button class="btn" onclick="location.href='grupos.php?carrera_id=<?= $carrera_id ?>'">🔙 Regresar</button>
    </div>
</body>
</html>
