<?php 
session_start();
include "conexionBD.php"; // Incluir la clase de conexión

$conexion = new conexionBD();
$conexion->conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documento_identidad = $_POST['username'];
    $numero_item = $_POST['password']; // Se usa como "contraseña" en este caso

    // Consulta preparada para verificar el documento de identidad y el número de item
    $sql = "SELECT * FROM Empleado WHERE documento_identidad = ? AND numero_item = ?";
    $result = $conexion->ejecutarConsultaPreparada($sql, "ss", $documento_identidad, $numero_item);

    if ($result && $result->num_rows > 0) {
        $empleado = $result->fetch_assoc();

        // Almacenar datos en sesión
        $_SESSION['documento_identidad'] = $documento_identidad;
        $_SESSION['nombre'] = $empleado['nombre'];
        $_SESSION['apellidos'] = $empleado['apellidos'];
        $_SESSION['rol'] = $empleado['rol'];
        
        // Redirigir a la página principal
        header('Location: principal.php');
        exit();
    } else {
        // Credenciales incorrectas
        $error_message = 'Documento de identidad o número de ítem incorrectos.';
    }
}

$conexion->cerrarconexion();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio de sesión</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="asset/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="asset/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>SISTEMA DE CONTROL DE PERSONAL</b></a>
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <form action="login.php" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Documento de Identidad" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-id-card"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Número de Ítem" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
          </div>
        </div>
      </form>
      <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
    </div>
  </div>
</div>
<script src="asset/plugins/jquery/jquery.min.js"></script>
<script src="asset/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="asset/dist/js/adminlte.min.js"></script>
</body>
</html>
