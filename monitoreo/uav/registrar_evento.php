<?php
header('Content-Type: application/json');
require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');

$input = json_decode(file_get_contents("php://input"), true);

if ($input && isset($input['tipo']) && isset($input['descripcion'])) {
    $tipo = trim($input['tipo']);
    $descripcion = trim($input['descripcion']);

    if (!empty($tipo) && !empty($descripcion)) {
        $stmt = $conn->prepare("INSERT INTO eventos_seguridad (tipo_evento, descripcion) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $tipo, $descripcion);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "msg" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "msg" => "Campos vacíos"]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
}
?>
