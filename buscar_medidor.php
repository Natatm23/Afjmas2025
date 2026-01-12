<?php 
require "conexion_lecturacel.php"; // conexión MySQL

$nombre = "";
$direccion = "";
$colonia = "";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $modo      = $_POST['modo_busqueda'] ?? '';
    $id        = $_POST['id_medidor'] ?? '';
    $idCuenta  = $_POST['id_cuenta'] ?? '';
    $cuenta    = $_POST['cuenta'] ?? '';

    // ==========================PENDEINTEEEEEEEEEEEEEEEEEEEEEEE lll
    // BUSCAR POR MEDIDOR (ID)
    // ==========================
    if ($modo == "id" && !empty($id)) {

        $sql = "SELECT nomb_us, mednume_us, dire_us, colo_us 
                FROM usuarios WHERE mednume_us = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

    // ==========================
    // BUSCAR POR ID CUENTA
    // ==========================
    } elseif ($modo == "id cuenta" && !empty($idCuenta)) {

        $sql = "SELECT nomb_us, cuent_us, dire_us, colo_us 
                FROM usuarios WHERE cuent_us = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $idCuenta);

    // ==========================
    // BUSCAR POR CUENTA
    // ==========================
    } elseif ($modo == "cuenta" && !empty($cuenta)) {

        $sql = "SELECT nomb_us, numcue_us, dire_us, colo_us 
                FROM usuarios WHERE numcue_us = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cuenta);

    } else {
        $mensaje = "⚠ Debes ingresar un valor para buscar.";
    }

    // ==========================
    // EJECUTAR CONSULTA
    // ==========================
    if (isset($stmt)) {

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $mensaje = "❌ No se encontró ningún registro.";
        } else {
            $data = $result->fetch_assoc();
            $nombre    = $data["nomb_us"];
            $direccion = $data["dire_us"];
            $colonia   = $data["colo_us"];
        }
    }
}

echo json_encode([
    "mensaje"   => $mensaje,
    "nombre"    => $nombre,
    "direccion" => $direccion,
    "colonia"   => $colonia
]);
?>  