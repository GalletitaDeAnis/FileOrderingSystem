<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">Sistema de Control de Files</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="asset/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Paolo Pizarro Arispe</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <!-- Solicitudes -->
        <li class="nav-item">
          <a href="solicitar_files.php" class="nav-link">
            <i class="nav-icon fas fa-folder-plus"></i>
            <p>Solicitar Files</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="historial_solicitudes.php" class="nav-link">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Historial de Solicitudes</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="solicitudes_pendientes.php" class="nav-link">
            <i class="nav-icon fas fa-clock"></i>
            <p>Solicitudes Pendientes</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="solicitudes_retrasadas.php" class="nav-link">
            <i class="nav-icon fas fa-exclamation-triangle text-danger"></i>
            <p>Solicitudes en Alerta</p>
          </a>
        </li>

        <!-- Sección de Estadísticas -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Estadísticas <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="reporte.php" class="nav-link">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Generar Reportes</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="factura.php" class="nav-link">
                <i class="nav-icon fas fa-receipt"></i>
                <p>Facturación</p>
              </a>
            </li>
          </ul>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

