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

// Consulta para obtener el total de camiones
$sql_total_camiones = "SELECT COUNT(id) AS Total_Camiones FROM Camiones";
$result_total_camiones = $conn->query($sql_total_camiones);
$total_camiones = $result_total_camiones->fetch_assoc()['Total_Camiones'];

// Consulta para obtener el total de camiones no disponibles
$sql_no_disponibles = "SELECT COUNT(c.id) AS Total_Camiones_No_Disponibles
                       FROM Camiones c
                       JOIN Estados e ON c.estado_id = e.id
                       WHERE e.estado = 'No disponible'";
$result_no_disponibles = $conn->query($sql_no_disponibles);
$total_camiones_no_disponibles = $result_no_disponibles->fetch_assoc()['Total_Camiones_No_Disponibles'];

// Consulta para obtener el total de camiones por líneas
$sql_camiones_por_linea = "SELECT l.nombre AS Linea, COUNT(c.id) AS Total_Camiones
                           FROM Camiones c
                           JOIN Lineas l ON c.linea_id = l.id
                           GROUP BY l.nombre
                           ORDER BY l.nombre";
$result_camiones_por_linea = $conn->query($sql_camiones_por_linea);

$camiones_por_linea = array();
while ($row = $result_camiones_por_linea->fetch_assoc()) {
    $camiones_por_linea[] = $row;
}

$response = array(
    'total_camiones' => $total_camiones,
    'total_camiones_no_disponibles' => $total_camiones_no_disponibles,
    'camiones_por_linea' => $camiones_por_linea
);

// Convertir a formato JSON y devolver los datos
echo json_encode($response);

// Cerrar conexión
$conn->close();
