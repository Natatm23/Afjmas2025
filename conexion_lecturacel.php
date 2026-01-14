<?php
$serverName = "10.10.1.247";   // IP del servidor MySQL
$username   = "lectura";       // Usuario MySQL
$password   = "lectura";       // Contraseña MySQL
$database   = "comercial_cuauhtemoc"; // Base de datos MySQL

// Crear conexión
$conn = new mysqli($serverName, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode([
        "mensaje" => "❌ Error de conexión a la base de datos"
    ]);
    exit;
}


// Forzar UTF-8
$conn->set_charset("utf8");

?>
