<?php
session_start(); // Opcional, solo para usar sesiones

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

// Conexión a SQL Server
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

// Consulta para el login
$sql = "SELECT * FROM LoginAFMovil WHERE UsarioAFMovil = ? AND PassAFMovil = ?";
$params = array($usuario, $clave);

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error en la consulta: " . print_r(sqlsrv_errors(), true));
}

// Validar si encontró el usuario
if (sqlsrv_has_rows($stmt)) {
    
    echo "<script>alert('Inicio de sesión exitoso. ¡Bienvenido $usuario!'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Usuario o contraseña incorrectos.'); window.location.href='login.php';</script>";
}

sqlsrv_close($conn);
?>

