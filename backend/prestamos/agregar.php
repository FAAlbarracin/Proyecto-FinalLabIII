<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (is_null($data)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid JSON."));
    exit;
}

if (!isset($data['id_empleado'], $data['id_libro'], $data['id_socio'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Missing required data."));
    exit;
}

$empleado = (int) $data['id_empleado'];
$libro = (int) $data['id_libro'];
$socio = (int) $data['id_socio'];

try {
    $db->beginTransaction();

    $query = "INSERT INTO prestamo (libros_id, empleados_id, socios_id) VALUES (:libro, :empleado, :socio)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':libro', $libro, PDO::PARAM_INT);
    $stmt->bindValue(':empleado', $empleado, PDO::PARAM_INT);
    $stmt->bindValue(':socio', $socio, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $queryUpdate = "UPDATE libros SET estado = :estado WHERE id = :id_libro";
        $stmtUpdate = $db->prepare($queryUpdate);
        $stmtUpdate->bindValue(':estado', false, PDO::PARAM_BOOL);
        $stmtUpdate->bindValue(':id_libro', $libro, PDO::PARAM_INT);
        
        if ($stmtUpdate->execute()) {
            $db->commit();
            http_response_code(200);
            echo json_encode(array("message" => "Prestamo registrado exitosamente."));
        } else {
            throw new Exception("Error al actualizar el estado del libro.");
        }
    } else {
        throw new Exception("Error al registrar el prestamo.");
    }
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
?>
