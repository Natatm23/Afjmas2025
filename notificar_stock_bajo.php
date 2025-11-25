<?php
// Evitar reiniciar la sesión si ya está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$unidad = isset($_SESSION['unidad']) ? trim($_SESSION['unidad']) : null;

if (!$unidad) {
    die('Unidad no definida en sesión.');
}

$sql = "SELECT TOP 1 Descripcion, Existencia 
        FROM AFM_Stock_Vehiculos 
        WHERE IdUnidad = ? AND Existencia <= 5 
        ORDER BY Existencia ASC";
$params = array($unidad);
$result = sqlsrv_query($conn, $sql, $params);

if ($result && sqlsrv_has_rows($result)) {
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    $desc = $row['Descripcion'];
    $cant = $row['Existencia'];

    $token = "eZBfZOpGc7VZTgmaovHkhk:APA91bFaf5O_dxuDBBtqS2aCYx7PmssctQZqmqx_PeVSJXX9wuGRjjRHhkKwiWMVk19aW8q2WHCFo_cu5wMuKav7x0AVX9dm_hVyt29F79l8UU9OFsagmTA";
    $serverKey = "BBT6p4s4hmr0u0Gblji71leK0cohjge3JbBZW7EfEUrdHrdQ-bS7-B3FipNI8wdXL18Rxw35Xnyrsq0SyIUkaPA";

    $notification = [
        'title' => '⚠️ Stock bajo detectado',
        'body' => "El material '$desc' tiene solo $cant unidades disponibles.",
        'icon' => 'https://jmas.afjmas.fun/Afjmas2025/icono.png',
        'click_action' => 'https://jmas.afjmas.fun/Afjmas2025/'
    ];

    $data = [
        "to" => $token,
        "notification" => $notification
    ];

    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    curl_close($ch);

    echo "✅ Notificación enviada: $desc ($cant unidades)";
} else {
    echo "Sin materiales con stock bajo.";
}
?>
