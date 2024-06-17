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

// Consulta SQL para obtener la cantidad de camiones por alquiladora
$sqlBarra = "
    SELECT a.nombre AS alquiladora, COUNT(c.id) AS cantidad_camiones
    FROM alquiladoras a
    LEFT JOIN camiones c ON c.id = a.id
    GROUP BY a.nombre
";
$resultBarra = $conn->query($sqlBarra);

// Consulta SQL para obtener el estado de los camiones
$sqlPastel = "
    SELECT e.estado AS estado, COUNT(c.id) AS cantidad
    FROM estados e
    LEFT JOIN camiones c ON c.estado_id = e.id
    GROUP BY e.estado
";
$resultPastel = $conn->query($sqlPastel);

$data = [
    'barra' => [],
    'pastel' => []
];

if ($resultBarra->num_rows > 0) {
    while ($row = $resultBarra->fetch_assoc()) {
        $data['barra'][] = [
            'alquiladora' => $row['alquiladora'],
            'cantidad_camiones' => (int) $row['cantidad_camiones']
        ];
    }
}

if ($resultPastel->num_rows > 0) {
    while ($row = $resultPastel->fetch_assoc()) {
        $data['pastel'][] = [
            'estado' => $row['estado'],
            'cantidad' => (int) $row['cantidad']
        ];
    }
}

echo json_encode($data);

// Cerrar conexión
$conn->close();
