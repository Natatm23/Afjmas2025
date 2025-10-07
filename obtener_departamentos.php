<?php
$serverName = "10.10.1.144"; 
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

$sql = "SELECT NombreDepartamento FROM DeptoPHP";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['NombreDepartamento']) . '">' . htmlspecialchars($row['NombreDepartamento']) . '</option>';
    }
} else {
    echo '<option>Error al cargar departamentos</option>';
}

sqlsrv_close($conn);
?>
