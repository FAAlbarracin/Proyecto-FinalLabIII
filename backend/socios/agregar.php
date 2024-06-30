<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['nombre']) && !empty($data['dni'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT id, activo FROM socios WHERE nombre = :nombre AND dni = :dni";
    $stmt = $db->prepare($queryFilter);
    $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
    $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        $query = "INSERT INTO socios (nombre, dni, activo) VALUES (:nombre, :dni, :activo)";
        $stmt = $db->prepare($query);

        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(":dni", $data['dni'], PDO::PARAM_STR);
        $stmt->bindValue(':activo', true, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Socio agregado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar al socio."));
        }
    } else {
        if (!$result['activo']) {
            // socio exists and is not active, proceed with the UPDATE
            $updateQuery = "UPDATE socios SET activo = :activo WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':activo', true, PDO::PARAM_BOOL);
            $updateStmt->bindValue(':id', $result['id'], PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Socio reactivado"));
            } else {
                echo json_encode(array("message" => "Error de reactivación"));
            }
        } else {
            // socio exists and is already active
            echo json_encode(array("message" => "El socio ya existe y está activo"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
