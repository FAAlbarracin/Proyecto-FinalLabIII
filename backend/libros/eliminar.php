<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data['titulo'])) {
    $database = new Database();
    $db = $database->getConnection();
    $queryFilter = "SELECT activo FROM libros WHERE titulo = :titulo AND activo = :activo";
    $stmtFilter = $db->prepare($queryFilter);
    $stmtFilter->bindValue(":titulo", $data['titulo'], PDO::PARAM_STR);
    $stmtFilter->bindValue(":activo", true, PDO::PARAM_BOOL);
    $stmtFilter->execute();
    $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo json_encode(array("message" => "El libro no existe o ya fue eliminado"));
    } else {
        $updateQuery = "UPDATE libros SET activo = :activo WHERE titulo = :titulo";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':titulo', $data['titulo'], PDO::PARAM_STR);
        $updateStmt->bindValue(':activo', false, PDO::PARAM_BOOL);

        if ($updateStmt->execute()) {
            echo json_encode(array("message" => "libro eliminado"));
        } else {
            echo json_encode(array("message" => "Error de eliminación"));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos."));
}
?>