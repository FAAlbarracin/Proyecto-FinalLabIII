<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['nombre']) && isset($data['dni']) && is_numeric($data['dni']) && $data['dni'] > 0 && intval($data['dni']) == $data['dni']) {
    $database = new Database();
    $db = $database->getConnection();
    
    $queryFilter = "SELECT id, activo FROM socios WHERE nombre = :nombre AND dni = :dni";
    $stmt = $db->prepare($queryFilter);
    $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
    $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        $query = "INSERT INTO socios (nombre, dni, activo) VALUES (:nombre, :dni, :activo)";
        $stmt = $db->prepare($query);

        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_INT);
        $stmt->bindValue(':activo', true, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Socio agregado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar al socio."));
        }
    } else {
        if (!$result['activo']) {
            // Socio existe y no está activo, proceder con la actualización
            $updateQuery = "UPDATE socios SET activo = :activo WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':activo', true, PDO::PARAM_BOOL);
            $updateStmt->bindValue(':id', $result['id'], PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Socio reactivado."));
            } else {
                echo json_encode(array("message" => "Error de reactivación."));
            }
        } else {
            // Socio existe y ya está activo
            echo json_encode(array("message" => "El socio ya existe y está activo."));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos o inválidos."));
}
?>
