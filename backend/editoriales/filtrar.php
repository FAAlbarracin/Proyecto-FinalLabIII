<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$nombre = isset($_GET['editorial']) ? $_GET['editorial'] : '';

$query = "SELECT * FROM editoriales WHERE editorial LIKE :editorial AND activa = :activa";
$stmt = $db->prepare($query);
$stmt->bindValue(':editorial', '%' . $nombre . '%', PDO::PARAM_STR);
$stmt->bindValue(':activa', 1, PDO::PARAM_INT);
$stmt->execute();

$editoriales = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($editoriales);
?>
