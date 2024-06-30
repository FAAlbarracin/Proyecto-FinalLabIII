<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM editoriales WHERE activa = :activa";
$stmt = $db->prepare($query);
$stmt->bindValue(':activa', '1', PDO::PARAM_INT);
$stmt->execute();

$editoriales = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($editoriales);