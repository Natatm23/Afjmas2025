<?php
require "conexion_lecturacel.php"; // conexión MySQL

$direccion = "";
$colonia = "";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $modo   = $_POST['modo_busqueda'] ?? '';
    $id     = $_POST['id_medidor'] ?? '';
    $nombre = $_POST['nombre_cliente'] ?? '';

    if ($modo == "id" && !empty($id)) {

        $sql = "SELECT mednume_us, dire_us, colo_us 
                FROM usuarios WHERE mednume_us = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

    } elseif ($modo == "nombre" && !empty($nombre)) {

        $sql = "SELECT mednume_us, dire_us, colo_us 
                FROM usuarios WHERE nomb_us LIKE ?";
        $like = "%".$nombre."%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $like);

    } else {
        $mensaje = "⚠ Debes ingresar un valor para buscar.";
    }

    if (isset($stmt)) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $mensaje = "❌ No se encontró ningún medidor.";
        } else {
            $data = $result->fetch_assoc();
            $direccion = $data["dire_us"];
            $colonia   = $data["colo_us"];
        }
    }
}

echo json_encode([
    "mensaje"   => $mensaje,
    "direccion" => $direccion,
    "colonia"   => $colonia
]);
?>
