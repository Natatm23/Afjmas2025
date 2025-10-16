<?php
session_start(); 

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a SQL Server
$serverName = "10.10.1.144";
$connectionOptions = [
    "Database" => "Adm_JMAS",
    "Uid" => "sa",
    "PWD" => "Mrrobot2025"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("❌ Error: No se pudo conectar a la base de datos.");
}

// Variables principales
$numeroDevolucion = $_POST['numeroDevolucion'] ?? null;
$departamento     = $_POST['departamento'] ?? $_SESSION['NombreDepartamento'] ?? null;
$fecha            = $_POST['fecha'] ?? null;
$usuario          = $_POST['usuario'] ?? null;
$idEmpleado       = $_POST['idEmpleado'] ?? $_SESSION['id_empleado'] ?? null;

// Recolectar materiales
$filas = [];
for ($i = 1; $i <= 10; $i++) {
    $mat  = $_POST['material' . $i] ?? null;
    $cant = $_POST['cantidad' . $i] ?? null;
    $just = $_POST['justificacion' . $i] ?? null;

    if ($mat && $cant && $just) {
        $filas[] = [
            'Material' => trim($mat),
            'Cantidad' => trim($cant),
            'Justificacion' => trim($just)
        ];
    }
}

// Validar que al menos haya una fila
if (count($filas) == 0) {
    die("Debe agregar al menos un material para la devolución.");
}

// Columnas base
$columns = [
    'IdEmpleado', 'Usuario', 'FechaElaboracion', 'Departamento',
    'Material1','Cantidad1','Justificacion1',
    'Material2','Cantidad2','Justificacion2',
    'Material3','Cantidad3','Justificacion3',
    'Material4','Cantidad4','Justificacion4',
    'Material5','Cantidad5','Justificacion5',
    'Material6','Cantidad6','Justificacion6',
    'Material7','Cantidad7','Justificacion7',
    'Material8','Cantidad8','Justificacion8',
    'Material9','Cantidad9','Justificacion9',
    'Material10','Cantidad10','Justificacion10'
];

// Placeholders
$placeholders = array_fill(0, count($columns), '?');

// Parametros
$params = [];
$params[] = $idEmpleado;
$params[] = $usuario;
$params[] = $fecha;
$params[] = $departamento;

for ($i = 1; $i <= 10; $i++) {
    $params[] = $_POST["material{$i}"] ?? null;
    $params[] = $_POST["cantidad{$i}"] ?? null;
    $params[] = $_POST["justificacion{$i}"] ?? null;
}

// SQL
$sql = "INSERT INTO AFM_Devolucion_Material (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

// Ejecutar query
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    echo "✅ Devolución registrada correctamente.";
} else {
    echo "❌ Error al insertar la devolución.";
    $errs = sqlsrv_errors();
    if ($errs) {
        foreach ($errs as $err) {
            echo " SQLSTATE: ".$err['SQLSTATE']."; Code: ".$err['code']."; Message: ".$err['message']."\n";
        }
    }
}
?>
