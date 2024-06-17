<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "gestion_patio";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener los totales por estado y alquiladora
$sql = "
    SELECT a.nombre AS alquiladora, e.estado AS estado, COUNT(c.id) AS cantidad
    FROM alquiladoras a
    JOIN lineas l ON a.id = l.alquiladora_id
    JOIN camiones c ON l.id = c.linea_id
    JOIN estados e ON c.estado_id = e.id
    GROUP BY a.nombre, e.estado
    ORDER BY a.nombre, e.estado;
";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $alquiladora = $row['alquiladora'];
        $estado = $row['estado'];
        $cantidad = (int) $row['cantidad'];

        if (!isset($data[$alquiladora])) {
            $data[$alquiladora] = [];
        }
        $data[$alquiladora][$estado] = $cantidad;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

// Cerrar conexión
$conn->close();
