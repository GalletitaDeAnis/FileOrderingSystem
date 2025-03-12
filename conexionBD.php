<?php
class conexionBD{
    private $servername = "localhost";
    private $user = "root";
    private $contraseña = "new_password"; // Si tienes una contraseña, ponla aquí
    private $bd = "gestionArchivos";
    private $conexion;

    public function conectar() {
        $this->conexion = new mysqli($this->servername, $this->user, $this->contraseña, $this->bd);
        
        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }    

    public function datos($sql) {
        return $this->conexion->query($sql);
    }

    
    public function ejecutarConsultaPreparada($sql, $tipos, ...$parametros) {
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $this->conexion->error);
        }

        $stmt->bind_param($tipos, ...$parametros);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function cerrarconexion() {
        $this->conexion->close();
    }
    
}
?>