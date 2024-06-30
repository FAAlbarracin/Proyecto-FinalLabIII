<?php
include_once '../db.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->titulo)) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Iniciar transacción
        $db->beginTransaction();

        $queryFilter = "SELECT activo FROM libros WHERE titulo = :titulo";
        $stmtFilter = $db->prepare($queryFilter);
        $stmtFilter->bindValue(":titulo", $data->titulo, PDO::PARAM_STR);
        $stmtFilter->execute();
        $result = $stmtFilter->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new Exception("El libro no existe.");
        } elseif ($result['activo'] == false) {
            throw new Exception("El libro ya fue eliminado anteriormente.");
        } else {
            $updateQuery = "UPDATE libros SET activo = :activo WHERE titulo = :titulo";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindValue(':titulo', $data->titulo, PDO::PARAM_STR);
            $updateStmt->bindValue(':activo', false, PDO::PARAM_BOOL);

            if (!$updateStmt->execute()) {
                throw new Exception("Error al eliminar el libro.");
            }
        }

        // Confirmar transacción
        $db->commit();
        
        http_response_code(200);
        echo json_encode(array("message" => "Libro eliminado exitosamente."));
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        http_response_code(400);
        echo json_encode(array("message" => $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Datos incompletos. Se requiere el título del libro."));
}
?>