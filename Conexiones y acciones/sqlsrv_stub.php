<?php
// Stub para que VS Code reconozca funciones de SQLSRV
if (!function_exists('sqlsrv_connect')) {
    function sqlsrv_connect($serverName = null, $connectionInfo = null) {}
    function sqlsrv_query($conn, $sql, $params = [], $options = []) {}
    function sqlsrv_fetch_array($stmt, $fetchType = null) {}
    function sqlsrv_close($conn) {}
    function sqlsrv_errors() {}
    function sqlsrv_has_rows($stmt) {}
}

if (!defined('SQLSRV_FETCH_ASSOC')) {
    define('SQLSRV_FETCH_ASSOC', 2);
}
