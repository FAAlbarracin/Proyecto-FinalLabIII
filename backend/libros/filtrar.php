<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$genero = isset($_GET['genero']) ? $_GET['genero'] : '';
$idioma = isset($_GET['idioma']) ? $_GET['idioma'] : '';

$query = "SELECT * FROM libros WHERE activo = :activo";

$conditions = [];
$params = ['activo' => 1];

if (!empty($titulo)) {
    $conditions[] = "titulo LIKE :titulo";
    $params['titulo'] = '%' . $titulo . '%';
}
if (!empty($genero)) {
    $conditions[] = "genero LIKE :genero";
    $params['genero'] = '%' . $genero . '%';
}
if (!empty($idioma)) {
    $conditions[] = "idioma LIKE :idioma";
    $params['idioma'] = '%' . $idioma . '%';
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$stmt = $db->prepare($query);
$stmt->execute($params);

$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($libros);
?>
