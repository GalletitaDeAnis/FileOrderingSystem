<?php
session_start();
include "header.php";
include "sidebarmenu.php";
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

$fechaEntregaFiltro = $_GET['fecha_entrega'] ?? '';
$itemFiltro = $_GET['item'] ?? '';
$empleadoFiltro = $_GET['empleado'] ?? '';

$query = "SELECT s.id_solicitud, s.id_empleado, s.estado, s.fecha_entrega, s.fecha_devolucion, s.en_alerta, s.comentarios, e.nombre
          FROM Solicitud s
          JOIN Empleado e ON s.id_empleado = e.id_empleado
          WHERE s.estado = 'alerta'";

if ($fechaEntregaFiltro) {
    $query .= " AND s.fecha_entrega = '$fechaEntregaFiltro'";
}
if ($itemFiltro) {
    $query .= " AND s.id_solicitud IN (SELECT id_solicitud FROM SolicitudFile WHERE id_file IN 
               (SELECT id_file FROM File WHERE numero_item = '$itemFiltro'))";
}
if ($empleadoFiltro) {
    $query .= " AND e.nombre LIKE '%$empleadoFiltro%'";
}

$result = $conexion->datos($query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Solicitudes Retrasadas</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID Solicitud</th>
                        <th>Empleado</th>
                        <th>Fecha Entrega</th>
                        <th>Fecha Devolución</th>
                        <th>Comentarios</th>
                        <th>Archivos Asociados</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($solicitud = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $solicitud['id_solicitud']; ?></td>
                            <td><?php echo $solicitud['nombre']; ?></td>
                            <td><?php echo $solicitud['fecha_entrega'] ?? 'N/A'; ?></td>
                            <td><?php echo $solicitud['fecha_devolucion'] ?? 'N/A'; ?></td>
                            <td><?php echo $solicitud['comentarios']; ?></td>
                            <td>
                                <?php
                                $idSolicitud = $solicitud['id_solicitud'];
                                $queryFiles = "SELECT f.numero_item FROM File f 
                                               JOIN SolicitudFile sf ON f.id_file = sf.id_file 
                                               WHERE sf.id_solicitud = $idSolicitud";
                                $filesResult = $conexion->datos($queryFiles);
                                while ($file = $filesResult->fetch_assoc()) {
                                    echo $file['numero_item'] . ', ';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'empleado'): ?>
                                    <button class="btn btn-success" onclick="marcarDevuelto(<?php echo $solicitud['id_solicitud']; ?>)">Devuelto</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
function marcarDevuelto(idSolicitud) {
    if (confirm("¿Confirmar devolución de la solicitud?")) {
        fetch("actualizar_solicitud.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id_solicitud=" + idSolicitud
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
}
</script>

<?php 
$conexion->cerrarconexion();
include "footer.php";
?>
