<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM empleados WHERE activo = :activo";
$stmt = $db->prepare($query);
$stmt->bindValue(':activo', '1', PDO::PARAM_INT);
$stmt->execute();

$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($empleados);
?>