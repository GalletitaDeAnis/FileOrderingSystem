<?php
include "header.php";
include "sidebarmenu.php";
include "conexionBD.php";

$conexion = new conexionBD();
$conexion->conectar();

// Obtener files disponibles (estado 'libre' o 'ocupado')
$query = "SELECT id_file, numero_item, nombre_empleado, documento_identidad, estado FROM File";
$result = $conexion->datos($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar'])) {
    $filesSeleccionados = isset($_POST['files']) ? explode(',', $_POST['files']) : []; // Convertir string en array
    $comentarios = $_POST['comentarios'] ?? '';
    $idEmpleado = 2; // ID del empleado (debes obtenerlo de la sesión o del formulario)

    if (!empty($filesSeleccionados)) {
        // Insertar nueva solicitud con comentarios
        $queryInsert = "INSERT INTO Solicitud (id_empleado, estado, fecha_entrega, fecha_devolucion, en_alerta, comentarios) 
                        VALUES (?, 'pendiente', NULL, NULL, FALSE, ?)";
        $stmt = $conexion->getConexion()->prepare($queryInsert);
        $stmt->bind_param("is", $idEmpleado, $comentarios);
        $stmt->execute();
        $idSolicitud = $stmt->insert_id; // Obtener el ID de la solicitud recién insertada

        // Asociar cada file a la solicitud
        $queryRelacion = "INSERT INTO SolicitudFile (id_solicitud, id_file) VALUES (?, ?)";
        $stmtRelacion = $conexion->getConexion()->prepare($queryRelacion);

        foreach ($filesSeleccionados as $idFile) {
            $stmtRelacion->bind_param("ii", $idSolicitud, $idFile);
            $stmtRelacion->execute();
        }

        echo "<script>alert('Solicitud enviada exitosamente.'); window.location='principal.php';</script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Solicitar Files</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="">
                <!-- Botón para abrir la ventana modal -->
                <button type="button" class="btn btn-secondary" onclick="abrirModal()">Seleccionar Files</button>

                <!-- Lista de files seleccionados -->
                <h4 class="mt-3">Files Seleccionados:</h4>
                <ul id="listaSeleccionados" class="list-group"></ul>

                <!-- Input oculto para almacenar los IDs de los files seleccionados -->
                <input type="hidden" name="files" id="filesSeleccionados">

                <!-- Campo de comentarios -->
                <div class="form-group mt-3">
                    <label for="comentarios">Comentarios:</label>
                    <textarea id="comentarios" name="comentarios" class="form-control" rows="3"></textarea>
                </div>

                <!-- Botón para enviar la solicitud -->
                <button type="submit" name="solicitar" class="btn btn-primary">Mandar Solicitud</button>
            </form>
        </div>
    </section>
</div>

<!-- Modal para selección de Files -->
<div id="modalFiles" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Seleccionar Files</h3>

        <!-- Campos de búsqueda -->
        <input type="text" id="buscarItem" placeholder="Buscar por Número de Item" onkeyup="filtrarTabla()">
        <input type="text" id="buscarDoc" placeholder="Buscar por Documento Identidad" onkeyup="filtrarTabla()">

        <!-- Tabla de Files -->
        <table class="table table-bordered" id="tablaFiles">
            <thead>
                <tr>
                    <th>Seleccionar</th>
                    <th>Número Item</th>
                    <th>Empleado</th>
                    <th>Documento Identidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($file = $result->fetch_assoc()): ?>
                    <tr style="background-color: <?php echo $file['estado'] == 'ocupado' ? '#ccc' : 'white'; ?>">
                        <td>
                            <input type="checkbox" 
                                   value="<?php echo $file['id_file']; ?>" 
                                   data-numero="<?php echo $file['numero_item']; ?>"
                                   data-empleado="<?php echo $file['nombre_empleado']; ?>"
                                   data-documento="<?php echo $file['documento_identidad']; ?>"
                                   <?php echo $file['estado'] == 'ocupado' ? 'disabled' : ''; ?>
                                   onclick="agregarFile(this)">
                        </td>
                        <td><?php echo $file['numero_item']; ?></td>
                        <td><?php echo $file['nombre_empleado']; ?></td>
                        <td><?php echo $file['documento_identidad']; ?></td>
                        <td><?php echo $file['estado']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById("modalFiles").style.display = "block";
}

function cerrarModal() {
    document.getElementById("modalFiles").style.display = "none";
}

function filtrarTabla() {
    let buscarItem = document.getElementById("buscarItem").value.toLowerCase();
    let buscarDoc = document.getElementById("buscarDoc").value.toLowerCase();
    let table = document.getElementById("tablaFiles");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        let cols = rows[i].getElementsByTagName("td");
        let numeroItem = cols[1].innerText.toLowerCase();
        let docIdentidad = cols[3].innerText.toLowerCase();

        if (numeroItem.includes(buscarItem) && docIdentidad.includes(buscarDoc)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

function agregarFile(checkbox) {
    if (checkbox.checked) {
        let id = checkbox.value;
        let numero = checkbox.getAttribute("data-numero");
        let empleado = checkbox.getAttribute("data-empleado");
        let documento = checkbox.getAttribute("data-documento");

        let lista = document.getElementById("listaSeleccionados");
        let item = document.createElement("li");
        item.className = "list-group-item";
        item.innerHTML = `${numero} - ${empleado} - ${documento} <button onclick="quitarFile(${id}, this)" class="btn btn-danger btn-sm">X</button>`;
        item.setAttribute("data-id", id);
        lista.appendChild(item);

        actualizarInputHidden();
    }
}

function quitarFile(id, boton) {
    let lista = document.getElementById("listaSeleccionados");
    let items = lista.getElementsByTagName("li");

    for (let i = 0; i < items.length; i++) {
        if (items[i].getAttribute("data-id") == id) {
            lista.removeChild(items[i]);
            break;
        }
    }

    document.querySelector(`input[value='${id}']`).checked = false;
    actualizarInputHidden();
}

function actualizarInputHidden() {
    let lista = document.getElementById("listaSeleccionados");
    let ids = [];
    lista.querySelectorAll("li").forEach(item => ids.push(item.getAttribute("data-id")));
    document.getElementById("filesSeleccionados").value = ids.join(",");
}
</script>

<?php 
$conexion->cerrarconexion();
include "footer.php";
?>
