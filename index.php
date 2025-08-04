<?php
session_start();
include "conexion.php";

// ✅ Si ya hay sesión activa, redirigir directamente
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 'admin') {
        header("Location: panel_admin.php"); 
        exit;
    } elseif ($_SESSION['rol'] == 'alumno') {
        header("Location: ver_calificaciones.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tipo']) && $_POST['tipo'] == "usuario") {
        // ✅ Login de Administrador
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        $query = $conexion->prepare("SELECT * FROM usuarios WHERE usuario=?");
        $query->bind_param("s", $usuario);
        $query->execute();
        $resultado = $query->get_result();

        if ($resultado->num_rows > 0) {
            $user = $resultado->fetch_assoc();
            if (md5($contrasena) == $user['contrasena']) {
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['usuario_id'] = $user['id'];

                if ($user['rol'] == 'admin') {
                    header("Location: panel_admin.php");
                } else {
                    header("Location: ver_calificaciones.php");
                }
                exit;
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ Usuario no encontrado.";
        }
    } elseif (isset($_POST['tipo']) && $_POST['tipo'] == "alumno") {
        // ✅ Login de Alumno
        $alumno_id = $_POST['alumno_id'];
        if (!empty($alumno_id)) {
            $_SESSION['rol'] = 'alumno';
            $_SESSION['alumno_id'] = $alumno_id;
            header("Location: ver_calificaciones.php");
            exit;
        }
    }
}

$alumnos = $conexion->query("SELECT id, nombre FROM alumnos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistema de Calificaciones</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="login">
   <!-- ✅ Logo en la esquina superior izquierda -->
  <div class="logo-container">
    <img src="/img/GE_USYC.png" alt="Logo" class="logo">
  </div>
  <h1>Bienvenido al Sistema de Calificaciones</h1>

  <?php if (!empty($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

  <div class="login-container">
    <!-- ✅ Login Administrador -->
    <div class="login-card">
      <h2>👨‍💼 Administrador</h2>
      <form method="POST">
        <input type="hidden" name="tipo" value="usuario">
        <label>Usuario:</label>
        <input type="text" name="usuario" required>
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required>
        <button type="submit">Ingresar</button>
      </form>
    </div>

    <!-- ✅ Login Alumno -->
    <div class="login-card">
      <h2>🎓 Alumno</h2>
      <form method="POST">
        <input type="hidden" name="tipo" value="alumno">
        <label>Selecciona tu nombre:</label>
        <select name="alumno_id" required>
          <option value="">-- Selecciona --</option>
          <?php while ($a = $alumnos->fetch_assoc()): ?>
            <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
          <?php endwhile; ?>
        </select>
        <button type="submit">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
