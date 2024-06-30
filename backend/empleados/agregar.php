<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true); // Decodificar como array asociativo

if (!empty($data['nombre']) && !empty($data['dni'])) { // Verificar que nombre y dni no estén vacíos
    $database = new Database();
    $db = $database->getConnection();

    $queryFilter = "SELECT id, activo FROM empleados WHERE nombre = :nombre AND dni = :dni";
    $stmt = $db->prepare($queryFilter);
    $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
    $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        $query = "INSERT INTO empleados (nombre, pass, dni, rol, activo) VALUES (:nombre, :pass, :dni, :rol, :activo)";
        $stmt = $db->prepare($query);

        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(':pass', $data['pass'], PDO::PARAM_STR);
        $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_INT);
        $stmt->bindValue(":rol", $data['rol'], PDO::PARAM_BOOL);
        $stmt->bindValue(':activo', true, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Empleado agregado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar el empleado."));
        }
    } else {
        if (!$result['activo']) {
            // El empleado existe y no está activo, proceder con el UPDATE
            $updateQuery = "UPDATE empleados SET activo = :activo WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':activo', true, PDO::PARAM_BOOL);
            $updateStmt->bindValue(':id', $result['id'], PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Empleado reactivado"));
            } else {
                echo json_encode(array("message" => "Error de reactivación"));
            }
        } else {
            // El empleado existe y ya está activo
            echo json_encode(array("message" => "El empleado ya existe y está activo"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
