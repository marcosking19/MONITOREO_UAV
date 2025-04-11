<?php
require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');

$input = json_decode(file_get_contents("php://input"), true);

if ($input) {
    $uptime = (int)$input['uptime'];
    $connections = (int)$input['connections'];
    $rssi = (int)$input['rssi'];
    $threatLevel = $input['threatLevel'];

    $stmt = $conn->prepare("INSERT INTO log_monitoreo (uptime, connections, attacksBlocked, threatLevel) VALUES (?, ?, 0, ?)");
    $stmt->bind_param("iis", $uptime, $connections, $threatLevel);
    $stmt->execute();
    $stmt->close();
}
?>
