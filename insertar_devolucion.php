<?php
// Conexión a SQL Server
$serverName = "10.10.1.144";
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Conexión fallida: " . print_r(sqlsrv_errors(), true));
}

// Recibir datos del formulario
$numeroDevolucion = $_POST['numeroDevolucion'];
$departamento = $_POST['departamento'];
$fecha = $_POST['fecha'];

// Traer filas de la tabla de devoluciones
$filas = [];
for ($i = 1; $i <= 10; $i++) {
    $mat = isset($_POST['material' . $i]) ? trim($_POST['material' . $i]) : null;
    $cant = isset($_POST['cantidad' . $i]) ? trim($_POST['cantidad' . $i]) : null;
    $just = isset($_POST['justificacion' . $i]) ? trim($_POST['justificacion' . $i]) : null;

    // Solo agregar si los tres campos tienen valor
    if ($mat !== null && $cant !== null && $just !== null && $mat !== "" && $just !== "") {
        $filas[] = [
            'Material' => $mat,
            'Cantidad' => $cant,
            'Justificacion' => $just
        ];
    }
}

// Obtener el último número de devolución
$sqlUltimo = "SELECT MAX(numeroDevolucion) AS maxNum FROM AFM_Devolucion_Material";
$stmt = sqlsrv_query($conn, $sqlUltimo);

$contadorInicial = 0;
if ($stmt) {
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row['maxNum'] !== null) {
        $contadorInicial = (int)$row['maxNum'];
    }
}
    
// Validar que al menos exista la primera fila (Material1)
if (count($filas) == 0) {
    die("Debe agregar al menos un material para la devolución.");
}

// Preparar campos y parámetros para SQL
$campos = ["FechaElaboracion", "Departamento"];
$valores = ["?", "?"];
$params = [$fecha, $departamento];

foreach ($filas as $index => $fila) {
    $i = $index + 1; // Para Material1, Material2…
    $campos[] = "Material$i";
    $campos[] = "Justificacion$i";
    $campos[] = "Cantidad$i";

    $valores[] = "?";
    $valores[] = "?";
    $valores[] = "?";

    $params[] = $fila['Material'];
    $params[] = $fila['Justificacion'];
    $params[] = $fila['Cantidad'];
}

$campos_str = implode(", ", $campos);
$valores_str = implode(", ", $valores);
$sql = "INSERT INTO AFM_Devolucion_Material ($campos_str) VALUES ($valores_str)";

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error al insertar la devolución: " . print_r(sqlsrv_errors(), true));
} else {
    echo "Devolución registrada correctamente";
}
?>
