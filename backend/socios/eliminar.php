<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (is_null($data)) {
    echo json_encode(array("message" => "Error de decodificación JSON"));
    exit;
}

if (!empty($data->id)) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT activo FROM socios WHERE id = :id";
    $stmtFilter = $db->prepare($queryFilter);
    $stmtFilter->bindValue(":id", $data->id, PDO::PARAM_INT);
    $stmtFilter->execute();
    $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo json_encode(array("message" => "El socio no existe o ya fue eliminado"));
    } else {
        $updateQuery = "UPDATE socios SET activo = :activo WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':id', $data->id, PDO::PARAM_INT);
        $updateStmt->bindValue(':activo', false, PDO::PARAM_BOOL);

        if ($updateStmt->execute()) {
            echo json_encode(array("message" => "socio eliminado"));
        } else {
            echo json_encode(array("message" => "Error de eliminación"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>
