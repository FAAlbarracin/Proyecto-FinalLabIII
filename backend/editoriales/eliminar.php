<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['editorial'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT activa FROM editoriales WHERE editorial = :editorial";
    $stmtFilter = $db->prepare($queryFilter);
    $stmtFilter->bindValue(":editorial", $data['editorial'], PDO::PARAM_STR);
    $stmtFilter->execute();
    $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo json_encode(array("message" => "La editorial no existe o ya fue eliminado"));
    } else {
        if (!$result['activa']) {
            echo json_encode(array('message' => 'La editorial ya fue eliminada'));
        } else {
            $updateQuery = "UPDATE editoriales SET activa = :activa WHERE editorial = :editorial";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':editorial', $data['editorial'], PDO::PARAM_STR);
            $updateStmt->bindValue(':activa', false, PDO::PARAM_BOOL);

            if ($updateStmt->execute()) {
                echo json_encode(array("message" => "Editorial eliminado"));
            } else {
                echo json_encode(array("message" => "Error de eliminación"));
            }

        }

    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>