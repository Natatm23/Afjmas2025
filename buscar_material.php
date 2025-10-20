<?php
include("conexion_stock.php");

if (isset($_GET['term'])) {
    $term = trim($_GET['term']);

    // Consulta los primeros 10 materiales que coincidan por código o descripción
    $sql = "SELECT TOP 5 codigo, descripcion 
            FROM SI_AL_Cat_Articulos 
            WHERE codigo LIKE ? OR descripcion LIKE ?";
    $params = ["%$term%", "%$term%"];
    $stmt = sqlsrv_query($conn, $sql, $params);

    $materiales = [];
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            
            $materiales[] = $row['codigo'] . ' — ' . $row['descripcion'];
        }
    }

    echo json_encode($materiales);
}
?>
