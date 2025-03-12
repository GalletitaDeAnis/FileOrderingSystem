<?php
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_solicitud'])) {
    $idSolicitud = $_POST['id_solicitud'];
    $fechaActual = date("Y-m-d");

    // Actualizar solicitud a "concluida" con la fecha de devolución
    $queryUpdateSolicitud = "UPDATE Solicitud SET estado = 'concluida', fecha_devolucion = '$fechaActual' WHERE id_solicitud = ?";
    $stmt = $conexion->getConexion()->prepare($queryUpdateSolicitud);
    $stmt->bind_param("i", $idSolicitud);
    $stmt->execute();

    // Obtener los files de la solicitud
    $queryFiles = "SELECT id_file FROM SolicitudFile WHERE id_solicitud = ?";
    $stmtFiles = $conexion->getConexion()->prepare($queryFiles);
    $stmtFiles->bind_param("i", $idSolicitud);
    $stmtFiles->execute();
    $resultFiles = $stmtFiles->get_result();

    // Cambiar estado de los archivos a "libre"
    while ($file = $resultFiles->fetch_assoc()) {
        $queryUpdateFile = "UPDATE File SET estado = 'libre' WHERE id_file = ?";
        $stmtUpdateFile = $conexion->getConexion()->prepare($queryUpdateFile);
        $stmtUpdateFile->bind_param("i", $file['id_file']);
        $stmtUpdateFile->execute();
    }

    echo "Solicitud marcada como devuelta correctamente.";
}
?>
