<?php
header('Content-Type: application/json; charset=utf-8');
require "conexion_lecturacel.php";

$nombre = "";
$direccion = "";
$colonia = "";
$mensaje = "";
$lecturas = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $modo = $_POST['modo_busqueda'] ?? '';
    $id   = $_POST['id_medidor'] ?? '';

    if ($modo === "id" && !empty($id)) {

        $sql = "
            SELECT 
                u.nomb_us,
                u.dire_us,
                u.colo_us,
                l.fech_le,
                l.lant_le,
                l.lact_le,
                l.lect_le
            FROM usuarios u
            LEFT JOIN lecturas l 
                ON u.Id = l.IdUsuario
            WHERE u.mednume_us = ?
            ORDER BY l.fech_le DESC
            LIMIT 5
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(["mensaje" => "❌ Error en prepare"]);
            exit;
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $mensaje = "❌ No se encontraron lecturas";
        } else {

            $primero = true;

            while ($row = $result->fetch_assoc()) {

                if ($primero) {
                    $nombre    = $row["nomb_us"];
                    $direccion = $row["dire_us"];
                    $colonia   = $row["colo_us"];
                    $primero = false;
                }

                if ($row["fech_le"] !== null) {

                    $ant = (int)$row["lant_le"];
                    $act = (int)$row["lact_le"];
                    $real = (int)$row["lect_le"];

                    $lecturas[] = [
                        "fecha_registro"   => date("d-m-Y", strtotime($row["fech_le"])),
                        "lectura_anterior" => $ant,
                        "lectura_actual"   => $act,
                        "consumo"          => $act - $ant,
                        "lectura_real"     => $real
                    ];
                }
            }
        }
    } else {
        $mensaje = "⚠ Debes ingresar un medidor";
    }
}

echo json_encode([
    "mensaje"   => $mensaje,
    "nombre"    => $nombre,
    "direccion" => $direccion,
    "colonia"   => $colonia,
    "lecturas"  => $lecturas
]);
