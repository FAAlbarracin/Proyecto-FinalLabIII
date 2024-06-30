<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['editorial'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT editorial, activa FROM editoriales WHERE editorial = :editorial";
    $stmt = $db->prepare($queryFilter);
    $stmt->bindValue(":editorial", $data['editorial'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        $query = "INSERT INTO editoriales (editorial, activa) VALUES (:editorial, :activa)";
        $stmt = $db->prepare($query);

        $stmt->bindValue(":editorial", $data['editorial'], PDO::PARAM_STR);
        $stmt->bindValue(':activa', true, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Editorial agregada exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar la editorial."));
        }
    } else {
        if (!$result['activa']) {
            // Editorial existe y no está activa, proceder con la actualización
            $updateQuery = "UPDATE editoriales SET activa = :activa WHERE editorial = :editorial";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':activa', true, PDO::PARAM_BOOL);
            $updateStmt->bindValue(':editorial', $result['editorial'], PDO::PARAM_STR);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Editorial reactivada"));
            } else {
                echo json_encode(array("message" => "Error al reactivar la editorial"));
            }
        } else {
            // Editorial existe y ya está activa
            echo json_encode(array("message" => "La editorial ya existe y está activa"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
