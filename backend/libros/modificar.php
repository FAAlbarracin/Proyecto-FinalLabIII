<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$titulo = $data['titulo'] ?? null;
$lanzamiento = $data['lanzamiento'] ?? null;
$editorial = $data['editorial'] ?? null;
$idioma = $data['idioma'] ?? null;
$genero = $data['genero'] ?? null;
$estado = $data['estado'] ?? null;

if (!empty($id)) {
    // Verificar si el lanzamiento es un número válido y mayor que cero
    if (!is_numeric($lanzamiento) || $lanzamiento <= 0) {
        echo json_encode(array("message" => "El campo 'lanzamiento' debe ser un número válido y mayor que cero."));
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    // Verifica si el libro existe
    $queryLibro = "SELECT * FROM libros WHERE id = :id";
    $stmtLibro = $db->prepare($queryLibro);
    $stmtLibro->bindValue(":id", $id, PDO::PARAM_INT);
    $stmtLibro->execute();
    $libro = $stmtLibro->fetch(PDO::FETCH_ASSOC);

    if ($libro) {
        // Construye la query de actualización dinámicamente
        $queryUpdate = "UPDATE libros SET";
        $params = [];

        if (!empty($titulo)) {
            $queryUpdate .= " titulo = :titulo,";
            $params[':titulo'] = $titulo;
        }

        if (!empty($lanzamiento)) {
            $queryUpdate .= " lanzamiento = :lanzamiento,";
            $params[':lanzamiento'] = $lanzamiento;
        }

        if (!empty($editorial)) {
            $queryUpdate .= " editoriales_editorial = :editorial,";
            $params[':editorial'] = $editorial;
        }

        if (!empty($idioma)) {
            $queryUpdate .= " idioma = :idioma,";
            $params[':idioma'] = $idioma;
        }

        if (!empty($genero)) {
            $queryUpdate .= " genero = :genero,";
            $params[':genero'] = $genero;
        }

        if (isset($estado)) {
            $queryUpdate .= " estado = :estado,";
            $params[':estado'] = $estado;
        }

        // Elimina la última coma y añade la condición WHERE
        $queryUpdate = rtrim($queryUpdate, ",");
        $queryUpdate .= " WHERE id = :id";

        $stmtUpdate = $db->prepare($queryUpdate);

        foreach ($params as $key => $value) {
            $stmtUpdate->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmtUpdate->bindValue(":id", $id, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            echo json_encode(array("message" => "Libro actualizado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al actualizar el libro."));
        }
    } else {
        echo json_encode(array("message" => "El libro no existe."));
    }
} else {
    echo json_encode(array("message" => "ID del libro no proporcionado."));
}
?>
