<?php
require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');

header('Content-Type: application/json');

$result = $conn->query("SELECT id, tipo_evento, descripcion, fecha FROM eventos_seguridad ORDER BY fecha DESC LIMIT 50");

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode($eventos);
