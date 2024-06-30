<?php
include_once '../db.php';

$database = new Database();
$db = $database->getConnection();

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$genero = isset($_GET['genero']) ? $_GET['genero']: '';
$nacion = isset($_GET['nacion']) ? $_GET['nacion'] : '';

$query = "SELECT * FROM autores WHERE activo = :activo";
$params = [':activo' => 1];

if (!empty($nombre)) {
    $query .= " AND nombre LIKE :nombre";
    $params[':nombre'] = '%' . $nombre . '%';
}
if (!empty($genero)) {
    $query .= " AND genero LIKE :genero";
    $params[':genero'] = '%' . $genero . '%';
}
if (!empty($nacion)) {
    $query .= " AND nacion LIKE :nacion";
    $params[':nacion'] = '%' . $nacion . '%';
}

$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->execute();
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($autores);
?>
