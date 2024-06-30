<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$dni = isset($_GET['dni']) ? $_GET['dni'] : '';

$query = "SELECT * FROM empleados WHERE activo = :activo";

// Verificamos si se proporcionó el filtro por nombre
if (!empty($nombre)) {
    $query .= " AND nombre LIKE :nombre";
}

// Verificamos si se proporcionó el filtro por DNI
if (!empty($dni)) {
    $query .= " AND dni LIKE :dni";
}

$stmt = $db->prepare($query);
$stmt->bindValue(':activo', 1, PDO::PARAM_INT);

// Vinculamos los parámetros según corresponda
if (!empty($nombre)) {
    $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
}
if (!empty($dni)) {
    $stmt->bindValue(':dni', '%' . $dni . '%', PDO::PARAM_STR);
}

$stmt->execute();

$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($empleados);
?>
