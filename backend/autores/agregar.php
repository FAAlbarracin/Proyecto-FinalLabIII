<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['nombre']) && !empty($data['genero']) && !empty($data['nacion'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT nombre, activo FROM autores WHERE nombre = :nombre";
    $stmt = $db->prepare($queryFilter);
    $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result === false) {
        $query = "INSERT INTO autores (nombre, genero, nacion, activo) VALUES (:nombre, :genero, :nacion, :activo)";
        $stmt = $db->prepare($query);

        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(":genero", $data['genero'], PDO::PARAM_STR);
        $stmt->bindValue(":nacion", $data['nacion'], PDO::PARAM_STR);
        $stmt->bindValue(':activo', true, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Autor agregado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar el autor."));
        }
    } else {
        if (!$result['activo']) {
            // Autor existe y no está activo, proceder con la actualización
            $updateQuery = "UPDATE autores SET activo = :activo WHERE nombre = :nombre";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':activo', true, PDO::PARAM_BOOL);
            $updateStmt->bindValue(':nombre', $result['nombre'], PDO::PARAM_STR);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Autor reactivado"));
            } else {
                echo json_encode(array("message" => "Error al reactivar el autor"));
            }
        } else {
            // Autor existe y ya está activo
            echo json_encode(array("message" => "El autor ya existe y está activo"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
