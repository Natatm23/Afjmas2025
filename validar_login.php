<?php
session_start();

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$serverName = "10.10.1.144"; 
$connectionOptions = array(
    "Database" => "Adm_JMAS",
    "Uid" => "sa", 
    "PWD" => "Mrrobot2025"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}


$sql = "
SELECT 
    L.UsarioAFMovil,
    L.PassAFMovil,
    L.IdEmpleado,
    L.IdDepto,
    L.Unidad,
    D.NombreDepartamento
FROM 
    LoginAFMovil AS L
INNER JOIN 
    DeptoPHP AS D 
    ON L.IdDepto = D.IdDepartamento
WHERE 
    L.UsarioAFMovil = ? AND L.PassAFMovil = ?
";

$params = array($usuario, $clave);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error en la consulta: " . print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($stmt)) {
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Se guarda en sesión los datos importantes del usuario
    $_SESSION['usuario'] = $row['UsarioAFMovil'];
    $_SESSION['id_empleado'] = $row['IdEmpleado'];
    $_SESSION['id_depto'] = $row['IdDepto'];
    $_SESSION['unidad'] = $row['Unidad'];
    $_SESSION['nombre_departamento'] = $row['NombreDepartamento'];

    echo "<script>
        alert('Inicio de sesión exitoso. ¡Bienvenido {$row['UsarioAFMovil']}!');
        window.location.href='dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Usuario o contraseña incorrectos.');
        window.location.href='login.php';
    </script>";
}

sqlsrv_close($conn);
?>
