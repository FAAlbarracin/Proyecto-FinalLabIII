<?php
include_once '../db.php';
session_start(); // Iniciar sesión para acceder a la variable $_SESSION['user_id']

$json = file_get_contents('php://input');

// Decodificar el JSON en un array asociativo
$data = json_decode($json, true);

$database = new Database();
$db = $database->getConnection();

$id = $data['id'];
if (empty($id)) {
    echo json_encode(array('message' => 'Es obligatorio el id para modificar'));
    exit;
}

$nombre = $data['nombre'];
$dni = isset($data['dni']) ? $data['dni'] : null;
$rol = isset($data['rol']) ? $data['rol'] : null;

// Inicializamos el array para almacenar los cambios a realizar en el query de actualización
$updates = array();

// Verificamos si los valores no están vacíos y agregamos las modificaciones al array
if (!empty($nombre)) {
    $updates[] = "nombre = :nombre";
}
if (isset($dni) && is_numeric($dni) && $dni > 0 && intval($dni) == $dni) {
    $updates[] = "dni = :dni";
}
// Si el usuario activo es el que se está modificando y no es el usuario con ID 1, permitir cambiar el rol
if (!empty($rol)) {
    if ($id == 3) {
        echo json_encode(array('message' => 'No se puede modificar al superadmin'));
        exit;
    }
    if ($id != $_SESSION['user_id']) {
        $updates[] = "rol = :rol";
    }
}

// Comprobamos si hay modificaciones para realizar
if (!empty($updates)) {
    // Construimos la parte SET del query de actualización usando implode para unir los elementos del array con comas
    $setClause = implode(", ", $updates);
    // Agregamos la condición WHERE para actualizar solo el empleado con el id proporcionado
    $query = "UPDATE empleados SET $setClause WHERE id = :id";
    
    $stmt = $db->prepare($query);

    // Bindeamos los parámetros del array $data al statement
    if (!empty($nombre)) {
        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
    }
    if (isset($dni) && is_numeric($dni) && $dni > 0 && intval($dni) == $dni) {
        $stmt->bindParam(":dni", $dni, PDO::PARAM_INT);
    }
    if (!empty($rol) && $id != $_SESSION['user_id']) {
        $stmt->bindParam(":rol", $rol, PDO::PARAM_INT);
    }
    
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Empleado actualizado exitosamente."));
    } else {
        echo json_encode(array("message" => "Error al actualizar el empleado."));
    }
} else {
    echo json_encode(array("message" => "No se proporcionaron datos para actualizar."));
}
?>
