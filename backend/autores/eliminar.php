<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['nombre'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT activo FROM autores WHERE nombre = :nombre AND activo = :activo";
    $stmtFilter = $db->prepare($queryFilter);
    $stmtFilter->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
    $stmtFilter->bindValue(":activo", true, PDO::PARAM_BOOL);
    $stmtFilter->execute();
    $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo json_encode(array("message" => "El autor no existe o ya fue eliminado"));
    } else {
        $updateQuery = "UPDATE autores SET activo = :activo WHERE nombre = :nombre";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':nombre', $data['nombre'], PDO::PARAM_STR);
        $updateStmt->bindValue(':activo', false, PDO::PARAM_BOOL);

        if ($updateStmt->execute()) {
            echo json_encode(array("message" => "Autor eliminado"));
        } else {
            echo json_encode(array("message" => "Error de eliminación"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
