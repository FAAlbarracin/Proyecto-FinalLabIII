<?php
include_once '../db.php';

// Decodificar datos JSON si es una solicitud JSON
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibió el ID del empleado a eliminar
if (isset($data->id)) {
    $id = $data->id;

    try {
        $database = new Database();
        $db = $database->getConnection();

        // Verificar si el empleado existe y está activo
        $query = "SELECT * FROM empleados WHERE id = :id AND activo = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            // El empleado no existe o ya fue eliminado
            echo json_encode(array("message" => "El empleado no existe o ya fue eliminado."));
        } else {
            // Realizar la eliminación cambiando el estado a inactivo
            $queryDelete = "UPDATE empleados SET activo = 0 WHERE id = :id";
            $stmtDelete = $db->prepare($queryDelete);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmtDelete->execute()) {
                // Éxito al eliminar el empleado
                echo json_encode(array("message" => "Empleado eliminado correctamente."));
            } else {
                // Error al eliminar el empleado
                echo json_encode(array("message" => "Error al eliminar el empleado."));
            }
        }
    } catch (PDOException $e) {
        // Error de base de datos
        echo json_encode(array("message" => "Error de base de datos: " . $e->getMessage()));
    }
} else {
    // No se proporcionó un ID válido para eliminar el empleado
    echo json_encode(array("message" => "No se proporcionó un ID válido para eliminar el empleado."));
}
?>
