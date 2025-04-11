<?php
header('Content-Type: application/json');
require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');

$input = json_decode(file_get_contents("php://input"), true);

if ($input && isset($input['uptime'], $input['connections'], $input['rssi'], $input['threatLevel'])) {
    $uptime = (int)$input['uptime'];
    $connections = (int)$input['connections'];
    $rssi = (int)$input['rssi'];
    $threatLevel = $input['threatLevel'];
    $attacksBlocked = isset($input['attacksBlocked']) ? (int)$input['attacksBlocked'] : 0;

    $stmt = $conn->prepare("INSERT INTO log_monitoreo (uptime, connections, attacksBlocked, threatLevel, rssi) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiisi", $uptime, $connections, $attacksBlocked, $threatLevel, $rssi);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error", "msg" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
}
?>
