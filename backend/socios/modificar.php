<?php
include_once '../db.php';

$json = file_get_contents('php://input');

// Decodificar el JSON en un array asociativo
$data = json_decode($json, true);

$database = new Database();
$db = $database->getConnection();

$id = $data['id'];
if (empty($id)) {
    echo json_encode(array('message' => 'Es obligatorio el id para nodificar'));
    exit;
}
$nombre = $data['nombre'];
$dni = $data['dni'];

// Inicializamos el array para almacenar los cambios a realizar en el query de actualización
$updates = array();

// Verificamos si los valores no están vacíos y agregamos las modificaciones al array
if (!empty($nombre)) {
    $updates[] = "nombre = :nombre";
}
if (!empty($dni)) {
    $updates[] = "dni = :dni";
}

// Comprobamos si hay modificaciones para realizar
if (!empty($updates)) {
    // Construimos la parte SET del query de actualización usando implode para unir los elementos del array con comas
    $setClause = implode(", ", $updates);
    // Agregamos la condición WHERE para actualizar solo el empleado con el id proporcionado
    $query = "UPDATE socios SET $setClause WHERE id = :id";
    
    $stmt = $db->prepare($query);

    // Bindeamos los parámetros del array $data al statement
    if (!empty($nombre)) {
        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
    }
    if (!empty($dni)) {
        $stmt->bindParam(":dni", $dni, PDO::PARAM_STR);
    }

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Socio actualizado exitosamente."));
    } else {
        echo json_encode(array("message" => "Error al actualizar el socio."));
    }
} else {
    echo json_encode(array("message" => "No se proporcionaron datos para actualizar."));
}
?>