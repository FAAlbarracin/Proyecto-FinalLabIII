<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$dni = isset($_GET['dni']) ? $_GET['dni'] : '';

$query = "SELECT * FROM socios WHERE activo = :activo";

if (!empty($nombre)) {
    $query .= " AND nombre LIKE :nombre";
}
if (!empty($dni)) {
    $query .= " AND dni LIKE :dni";
}

$stmt = $db->prepare($query);
$stmt->bindValue(':activo', 1, PDO::PARAM_INT);

if (!empty($nombre)) {
    $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
}
if (!empty($dni)) {
    $stmt->bindValue(':dni', '%' . $dni . '%', PDO::PARAM_STR);
}

$stmt->execute();

$socios = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($socios);
?>
