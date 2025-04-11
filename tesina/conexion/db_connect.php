<?php
// Configuración de la conexión a la base de datos
$host = "localhost";      // Servidor de la base de datos
$username = "root";       // Usuario de la base de datos
$password = "12345678";           // Contraseña del usuario (vacía por defecto en XAMPP/AppServ)
$database = "firewall_uav"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar el conjunto de caracteres a utf8
$conn->set_charset("utf8");

// Función para sanitizar entradas (prevenir inyección SQL)
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}
?>