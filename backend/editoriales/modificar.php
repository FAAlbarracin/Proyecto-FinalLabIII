<?php
include_once '../db.php';

$json = file_get_contents('php://input');

// Decodificar el JSON en un array asociativo
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array("message" => "Error al decodificar el JSON: " . json_last_error_msg()));
    exit;
}

if (json_last_error() === JSON_ERROR_NONE && !empty($data['editorialOriginal']) && !empty($data['editorialFinal'])) {
    $original = $data['editorialOriginal'];
    $final = $data['editorialFinal'];

    $database = new Database();
    $db = $database->getConnection();
    $queryBusqueda = "SELECT * FROM editoriales WHERE editorial = :editorialOriginal";
    $stmt = $db->prepare($queryBusqueda);
    $stmt->bindParam(':editorialOriginal', $original);
    $stmt->execute();
    $resultadoBusqueda = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultadoBusqueda)) {
        $query = "UPDATE editoriales SET editorial = :editorialFinal WHERE editorial = :editorialOriginal";
        $stmt2 = $db->prepare($query);
        $stmt2->bindParam(":editorialFinal", $final);
        $stmt2->bindParam(":editorialOriginal", $original);
        if ($stmt2->execute()) {
            echo json_encode(array("message" => "editorial actualizada exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al actualizar la editorial."));
        }
    } else {
        echo json_encode(array("message" => "Datos incompletos."));
    }
} else {
    echo json_encode(array("message" => "No existe la editorial que se desea modificar"));
}