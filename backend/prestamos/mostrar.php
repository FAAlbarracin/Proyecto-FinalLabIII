<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM prestamo";
$stmt = $db->prepare($query);
$stmt->execute();

$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($prestamos);
?>
