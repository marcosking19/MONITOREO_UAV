<?php
// Agrega esto al inicio para depurar
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include 'conexion/db_connect.php';

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["status" => "error", "msg" => "Sin datos válidos"]);
        exit;
    }

    $attacks = $data['attacksBlocked'] ?? 0;
    $conns = $data['connections'] ?? 0;
    $uptime = $data['uptime'] ?? 0;
    $threat = $data['threatLevel'] ?? 'Desconocido';

    $sql = "INSERT INTO log_monitoreo (attacksBlocked, connections, uptime, threatLevel)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $attacks, $conns, $uptime, $threat);

    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error", "msg" => $conn->error]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
?>
