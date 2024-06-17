<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; // Nombre del servidor de la base de datos
$username = "root"; // Nombre de usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$database = "gestion_patio"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    // Manejo de errores y registro en el archivo de registro
    $errorLogPath = getErrorLogPath();
    error_log("Error de conexión a la base de datos: " . $conn->connect_error, 3, $errorLogPath);
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener los datos de cantidad de camiones por alquiladora
$sql = "
    SELECT a.nombre AS alquiladora, COUNT(c.id) AS cantidad_camiones
    FROM alquiladoras a
    LEFT JOIN camiones c ON c.id = a.id
    GROUP BY a.nombre
";

$result = $conn->query($sql);

if ($result === false) {
    // Si hay un error en la consulta SQL
    $errorLogPath = getErrorLogPath();
    error_log("Error en la consulta SQL: " . $conn->error, 3, $errorLogPath);
    die("Error en la consulta SQL: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Array para almacenar los datos
    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            "alquiladora" => $row["alquiladora"],
            "cantidad_camiones" => (int) $row["cantidad_camiones"]
        );
    }

    // Convertir a formato JSON y devolver los datos
    echo json_encode($data);
} else {
    echo "No se encontraron resultados.";
}

// Cerrar conexión
$conn->close();
