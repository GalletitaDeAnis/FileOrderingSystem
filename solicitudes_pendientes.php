<?php
session_start();
include "header.php";
include "sidebarmenu.php";
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

// Obtener todas las solicitudes pendientes con sus archivos asociados
$query = "
    SELECT s.id_solicitud, s.id_empleado, s.estado, s.fecha_entrega, s.fecha_devolucion, s.en_alerta, s.comentarios,
           GROUP_CONCAT(f.numero_item SEPARATOR ', ') AS files
    FROM Solicitud s
    LEFT JOIN SolicitudFile sf ON s.id_solicitud = sf.id_solicitud
    LEFT JOIN File f ON sf.id_file = f.id_file
    WHERE s.estado = 'pendiente'
    GROUP BY s.id_solicitud
";
$solicitudes = $conexion->datos($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['rol']) && $_SESSION['rol'] !== 'empleado') {
    $idSolicitud = $_POST['id_solicitud'];

    if (isset($_POST['aceptar'])) {
        $fechaEntrega = date('Y-m-d H:i:s');
                
        // Actualizar estado de la solicitud a "activa" y establecer fecha de entrega
        $queryUpdateSolicitud = "UPDATE Solicitud SET estado = 'activa', fecha_entrega = ? WHERE id_solicitud = ?";
        $conexion->ejecutarConsultaPreparada($queryUpdateSolicitud, "si", $fechaEntrega, $idSolicitud);
        
        // Obtener los archivos asociados a la solicitud
        $queryArchivos = "SELECT id_file FROM SolicitudFile WHERE id_solicitud = ?";
        $resultado = $conexion->ejecutarConsultaPreparada($queryArchivos, "i", $idSolicitud);
        
        while ($row = $resultado->fetch_assoc()) {
            // Cambiar estado de cada archivo a "ocupado"
            $queryUpdateFile = "UPDATE File SET estado = 'ocupado' WHERE id_file = ?";
            $conexion->ejecutarConsultaPreparada($queryUpdateFile, "i", $row['id_file']);
        }
        
        echo "<script>alert('Solicitud aceptada exitosamente.'); window.location='solicitudes_pendientes.php';</script>";
    }

    if (isset($_POST['rechazar'])) {
        $queryUpdateSolicitud = "UPDATE Solicitud SET estado = 'rechazada' WHERE id_solicitud = ?";
        $conexion->ejecutarConsultaPreparada($queryUpdateSolicitud, "i", $idSolicitud);
        
        echo "<script>alert('Solicitud rechazada.'); window.location='solicitudes_pendientes.php';</script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Solicitudes Pendientes</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID Solicitud</th>
                        <th>ID Empleado</th>
                        <th>Estado</th>
                        <th>Fecha Entrega</th>
                        <th>En Alerta</th>
                        <th>Comentarios</th>
                        <th>Files Solicitados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($solicitud = $solicitudes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $solicitud['id_solicitud']; ?></td>
                            <td><?php echo $solicitud['id_empleado']; ?></td>
                            <td><?php echo ucfirst($solicitud['estado']); ?></td>
                            <td><?php echo $solicitud['fecha_entrega'] ?? 'No asignada'; ?></td>
                            <td><?php echo $solicitud['en_alerta'] ? 'Sí' : 'No'; ?></td>
                            <td><?php echo $solicitud['comentarios']; ?></td>
                            <td><?php echo $solicitud['files'] ?: 'Ninguno'; ?></td>
                            <td>
                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'empleado'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_solicitud" value="<?php echo $solicitud['id_solicitud']; ?>">
                                        <button type="submit" name="aceptar" class="btn btn-success">Aceptar</button>
                                        <button type="submit" name="rechazar" class="btn btn-danger">Rechazar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php 
$conexion->cerrarconexion();
include "footer.php";
?>

