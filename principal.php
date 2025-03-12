<?php 
include "header.php";
include "sidebarmenu.php";
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

// Total de solicitudes activas
$totalSolicitudesActivas = $conexion->datos("SELECT COUNT(*) as total FROM Solicitud WHERE estado = 'activa'")
    ->fetch_assoc()['total'];

// Total de solicitudes concluidas
$totalHistorialSolicitudes = $conexion->datos("SELECT COUNT(*) as total FROM Solicitud WHERE estado = 'concluida'")->fetch_assoc()['total'];

// Total de solicitudes pendientes
$totalSolicitudesPendientes = $conexion->datos("SELECT COUNT(*) as total FROM Solicitud WHERE estado = 'pendiente'")
    ->fetch_assoc()['total'];

// Total de solicitudes en alerta
$totalSolicitudesAlerta = $conexion->datos("SELECT COUNT(DISTINCT id_empleado) as total FROM Solicitud WHERE en_alerta = TRUE")
    ->fetch_assoc()['total'];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Control de Archivos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Solicitudes -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $totalSolicitudesActivas; ?></h3>
              <p>Solicitar Files</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="solicitar_files.php" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- Historial -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?php echo $totalHistorialSolicitudes; ?></h3>
              <p>Historial de Solicitudes</p>
            </div>
            <div class="icon">
              <i class="fas fa-file-alt"></i>
            </div>
            <a href="historial_solicitudes.php" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- Pendientes -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo $totalSolicitudesPendientes; ?></h3>
              <p>Solicitudes Pendientes</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-plus"></i>
            </div>
            <a href="solicitudes_pendientes.php" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- Alerta -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?php echo $totalSolicitudesAlerta; ?></h3>
              <p>Solicitudes en Alerta</p>
            </div>
            <div class="icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="solicitudes_retrasadas.php" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php 
$conexion->cerrarconexion();
include "footer.php";
?>