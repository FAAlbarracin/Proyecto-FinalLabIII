<?php
include_once '../db.php';

$json = file_get_contents('php://input');

// Decodificar el JSON en un array asociativo
$data = json_decode($json, true);

$database = new Database();
$db = $database->getConnection();

$nombre = $data['nombre'];
if (empty($nombre)) {
    echo json_encode(array('message' => 'Es obligatorio el nombre para modificar'));
    exit;
}
$genero = $data['genero'];
$nacion = $data['nacion'];

// Inicializamos el array para almacenar los cambios a realizar en el query de actualización
$updates = array();

// Verificamos si los valores no están vacíos y agregamos las modificaciones al array
if (!empty($genero)) {
    $updates[] = "genero = :genero";
}
if (!empty($nacion)) {
    $updates[] = "nacion = :nacion";
}

// Comprobamos si hay modificaciones para realizar
if (!empty($updates)) {
    // Construimos la parte SET del query de actualización usando implode para unir los elementos del array con comas
    $setClause = implode(", ", $updates);
    // Agregamos la condición WHERE para actualizar solo el empleado con el id proporcionado
    $query = "UPDATE autores SET $setClause WHERE nombre = :nombre";

    $stmt = $db->prepare($query);

    // Bindeamos los parámetros del array $data al statement

    if (!empty($genero)) {
        $stmt->bindParam(":genero", $genero, PDO::PARAM_STR);
    }
    if (!empty($nacion)) {
        $stmt->bindParam(":nacion", $nacion, PDO::PARAM_STR);
    }
    $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Empleado actualizado exitosamente."));
    } else {
        echo json_encode(array("message" => "Error al actualizar el empleado."));
    }
} else {
    echo json_encode(array("message" => "No se proporcionaron datos para actualizar."));
}
?>