<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "gestion_patio";

// Crear conexión
$conn = new mysqli($server, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa";
