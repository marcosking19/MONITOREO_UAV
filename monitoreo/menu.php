<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Firewall UAV - Menú Inicial</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="start-menu" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;">
        <div class="start-menu-box">
            <h2>Firewall UAV - Sistema de Control</h2>
            <form action="simulacion.php" method="post">
                <button type="submit">Ver Simulación</button>
            </form>
            <form action="uav\conectar.php" method="get" style="text-align:center; margin-top: 50px;">
                <button type="submit">Conectarse a un dron</button>
            </form>


        </div>
    </div>

</body>
</html>
