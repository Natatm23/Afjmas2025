<?php
$serverName = "10.10.1.144";       
$connectionInfo = array(
    "Database" => "Adm_JMAS", 
    "UID" => "sa",            
    "PWD" => "Mrrobot2025",           
    "CharacterSet" => "UTF-8"         
);

// Crear conexión
$conn = sqlsrv_connect($serverName, $connectionInfo);

// Verificar conexión
if(!$conn){
    die("Error de conexión a la base de datos: " . print_r(sqlsrv_errors(), true));
}
?>
