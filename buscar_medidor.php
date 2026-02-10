<?php
header('Content-Type: application/json; charset=utf-8');
require "conexion_lecturacel.php";

$nombre = "";
$direccion = "";
$colonia = "";
$mensaje = "";
$lecturas = [];
$observaciones = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $modo = $_POST['modo_busqueda'] ?? '';
    $id   = $_POST['id_medidor'] ?? '';

    if ($modo === "id" && !empty($id)) {

        /* ===============================
           1️⃣ OBTENER USUARIO + LECTURAS
        =============================== */
        $sqlLecturas = "
            SELECT 
                u.Id AS IdUsuario,
                u.nomb_us,
                u.dire_us,
                u.colo_us,
                l.fech_le,
                l.lant_le,
                l.lact_le,
                l.lect_le,
                l.nota_le
            FROM usuarios u
            LEFT JOIN lecturas l 
                ON u.Id = l.IdUsuario
            WHERE u.mednume_us = ?
            ORDER BY l.fech_le DESC
            LIMIT 5";

        $stmt = $conn->prepare($sqlLecturas);
        if (!$stmt) {
            echo json_encode(["mensaje" => "❌ Error en prepare lecturas"]);
            exit;
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $mensaje = "❌ No se encontraron datos para el medidor";
        } else {

            $IdUsuario = null;
            $primero = true;

            while ($row = $result->fetch_assoc()) {

                if ($primero) {
                    $nombre    = $row["nomb_us"];
                    $direccion = $row["dire_us"];
                    $colonia   = $row["colo_us"];
                    $IdUsuario = $row["IdUsuario"];
                    $primero = false;
                }

                if ($row["fech_le"] !== null) {
                    $ant  = (int)$row["lant_le"];
                    $act  = (int)$row["lact_le"];
                    $real = (int)$row["lect_le"];

                    $lecturas[] = [
                        "fecha"              => date("d-m-Y", strtotime($row["fech_le"])),
                        "lectura_anterior"   => $ant,
                        "lectura_actual"     => $act,
                        "consumo"            => $act - $ant,
                        "lectura_real"       => $real,
                        "nota"               => $row["nota_le"] ?? ""
                    ];
                }
            }

        /* ===============================
            2️⃣ OBTENER OBSERVACIONES
        =============================== */
        if ($IdUsuario !== null) {

            $sqlObs = "
            SELECT 
                o.fech_ob,
                o.obsr_ob,
                o.Concepto,
                c.desc_cc
            FROM observaciones o
            INNER JOIN conceptos c
                ON o.Concepto = c.clav_cc
            WHERE o.IdUsuario = ?
            ORDER BY o.fech_ob DESC
            LIMIT 5";

        $stmtObs = $conn->prepare($sqlObs);
        if ($stmtObs) {

            $stmtObs->bind_param("i", $IdUsuario);
            $stmtObs->execute();
            $resObs = $stmtObs->get_result();

        while ($o = $resObs->fetch_assoc()) {
            $observaciones[] = [
                "fecha"        => date("d-m-Y", strtotime($o["fech_ob"])),
                "observacion"  => $o["obsr_ob"],
                "concepto"     => $o["Concepto"],
                "descripcion"  => $o["desc_cc"]
            ];
        }
    }
}
        }

    } else {
        $mensaje = "⚠ Debes ingresar un número de medidor";
    }
}

echo json_encode([
    "mensaje"        => $mensaje,
    "nombre"         => $nombre,
    "direccion"      => $direccion,
    "colonia"        => $colonia,
    "lecturas"       => $lecturas,
    "observaciones"  => $observaciones
]);
