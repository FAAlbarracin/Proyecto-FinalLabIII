<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nombre) && !empty($data->dni)) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT activo FROM socios WHERE nombre = :nombre AND dni = :dni";
    $stmtFilter = $db->prepare($queryFilter);
    $stmtFilter->bindValue(":nombre", $data->nombre, PDO::PARAM_STR);
    $stmtFilter->bindValue(":dni", $data->dni, PDO::PARAM_INT);
    $stmtFilter->execute();
    $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo json_encode(array("message" => "El socio no existe o ya fue eliminado"));
    } else {
        $updateQuery = "UPDATE socios SET activo = :activo WHERE nombre = :nombre AND dni = :dni";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':nombre', $data->nombre, PDO::PARAM_STR);
        $updateStmt->bindValue(':dni', $data->dni, PDO::PARAM_INT);
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