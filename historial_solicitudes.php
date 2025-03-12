<?php
session_start(); 

include "header.php";
include "sidebarmenu.php";
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

$idEmpleado = $_SESSION['id_empleado'];
$rolUsuario = $_SESSION['rol'];

// Obtener filtros
$estadoFiltro = $_GET['estado'] ?? '';
$fechaEntregaFiltro = $_GET['fecha_entrega'] ?? '';
$itemFiltro = $_GET['item'] ?? '';
$empleadoFiltro = $_GET['empleado'] ?? '';

// Construcción de la consulta con filtros dinámicos
$query = "SELECT s.id_solicitud, s.estado, s.fecha_entrega, s.fecha_devolucion, s.en_alerta, s.comentarios,
                 e.nombre, e.apellidos, e.documento_identidad, d.nombre AS departamento, sub.nombre AS subnivel
          FROM Solicitud s
          JOIN Empleado e ON s.id_empleado = e.id_empleado
          JOIN Departamento d ON e.id_departamento = d.id_departamento
          JOIN Subnivel sub ON e.id_subnivel = sub.id_subnivel
          WHERE 1=1";

if ($rolUsuario == "empleado") {
    $query .= " AND s.id_empleado = '$idEmpleado'";
}
if ($estadoFiltro) {
    $query .= " AND s.estado = '$estadoFiltro'";
}
if ($fechaEntregaFiltro) {
    $query .= " AND s.fecha_entrega = '$fechaEntregaFiltro'";
}
if ($itemFiltro) {
    $query .= " AND s.id_solicitud IN (SELECT id_solicitud FROM SolicitudFile WHERE id_file IN 
               (SELECT id_file FROM File WHERE numero_item = '$itemFiltro'))";
}
if ($empleadoFiltro && $rolUsuario != "empleado") {
    $query .= " AND e.nombre LIKE '%$empleadoFiltro%'";
}

$result = $conexion->datos($query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Historial de Solicitudes</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3">
                        <label>Filtrar por Estado:</label>
                        <select name="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="activa">Activa</option>
                            <option value="concluida">Concluida</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="alerta">Alerta</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Fecha de Entrega:</label>
                        <input type="date" name="fecha_entrega" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Número de Ítem:</label>
                        <input type="text" name="item" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Nombre del Empleado:</label>
                        <input type="text" name="empleado" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
            </form>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID Solicitud</th>
                        <th>Empleado</th>
                        <th>Documento</th>
                        <th>Departamento</th>
                        <th>Subnivel</th>
                        <th>Estado</th>
                        <th>Fecha Entrega</th>
                        <th>Fecha Devolución</th>
                        <th>Comentarios</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($solicitud = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $solicitud['id_solicitud']; ?></td>
                            <td><?php echo $solicitud['nombre'] . ' ' . $solicitud['apellidos']; ?></td>
                            <td><?php echo $solicitud['documento_identidad']; ?></td>
                            <td><?php echo $solicitud['departamento']; ?></td>
                            <td><?php echo $solicitud['subnivel']; ?></td>
                            <td><?php echo ucfirst($solicitud['estado']); ?></td>
                            <td><?php echo $solicitud['fecha_entrega'] ?? 'N/A'; ?></td>
                            <td><?php echo $solicitud['fecha_devolucion'] ?? 'N/A'; ?></td>
                            <td>
                                <button class="btn btn-info" onclick="mostrarComentario('<?php echo addslashes($solicitud['comentarios']); ?>')">Ver</button>
                            </td>
                            <td>
                                <?php if ($rolUsuario != 'empleado' && ($solicitud['estado'] == 'activa' || $solicitud['estado'] == 'alerta')): ?>
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

<div id="comentarioModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Comentario</h2>
        <p id="comentarioTexto"></p>
    </div>
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

function mostrarComentario(comentario) {
    document.getElementById("comentarioTexto").innerText = comentario;
    document.getElementById("comentarioModal").style.display = "block";
}

function cerrarModal() {
    document.getElementById("comentarioModal").style.display = "none";
}
</script>

<?php 
$conexion->cerrarconexion();
include "footer.php";
?>