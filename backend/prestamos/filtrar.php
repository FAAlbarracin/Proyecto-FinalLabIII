<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$libro = isset($_GET['libro']) ? $_GET['libro'] : '';
$empleado = isset($_GET['empleado']) ? $_GET['empleado'] : '';
$socio = isset($_GET['socio']) ? $_GET['socio'] : '';

$query = "SELECT * FROM prestamos WHERE 1=1";
$params = [];

if (!empty($libro)) {
    $query .= " AND libro LIKE :libro";
    $params[':libro'] = '%' . $libro . '%';
}
if (!empty($empleado)) {
    $query .= " AND empleado LIKE :empleado";
    $params[':empleado'] = '%' . $empleado . '%';
}
if (!empty($socio)) {
    $query .= " AND socio LIKE :socio";
    $params[':socio'] = '%' . $socio . '%';
}

try {
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();

    $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($prestamos);
} catch (PDOException $e) {
    // Log the error and send a generic error message
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while processing your request.']);
}