<?php
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$titulo = $data['titulo'] ?? null;
$lanzamiento = $data['lanzamiento'] ?? null;
$editorial = $data['editoriales_editorial'] ?? null;
$idioma = $data['idioma'] ?? null;
$genero = $data['genero'] ?? null;
$autor1 = $data['autor1'] ?? null;
$autor2 = $data['autor2'] ?? null;
$autor3 = $data['autor3'] ?? null;

if (!empty($titulo) && !empty($lanzamiento) && !empty($editorial) && !empty($idioma) && !empty($genero) && !empty($autor1)) {
    $database = new Database();
    $db = $database->getConnection();

    // Verifica la existencia de la editorial
    $queryEditorial = "SELECT * FROM editoriales WHERE editorial = :editorial";
    $stmtEditorial = $db->prepare($queryEditorial);
    $stmtEditorial->bindValue(":editorial", $editorial, PDO::PARAM_STR);
    $stmtEditorial->execute();
    $editorialRow = $stmtEditorial->fetch(PDO::FETCH_ASSOC);

    if (!$editorialRow) {
        echo json_encode(array("message" => "La editorial ingresada no existe"));
        exit;
    }

    // Verifica la existencia de los autores
    $autoresIds = array();
    $queryAutores = "SELECT * FROM autores WHERE activo = :activo AND nombre IN (:autor1, :autor2, :autor3)";
    $params = array(':activo' => 1, ':autor1' => $autor1, ':autor2' => $autor2, ':autor3' => $autor3);
    $stmtAutores = $db->prepare($queryAutores);
    $stmtAutores->bindValue(':activo', 1, PDO::PARAM_INT);

    // Vincula los autores que están presentes en la solicitud
    foreach (array(':autor1', ':autor2', ':autor3') as $param) {
        if (!empty($data[substr($param, 1)])) {
            $stmtAutores->bindValue($param, $data[substr($param, 1)], PDO::PARAM_STR);
        } else {
            $stmtAutores->bindValue($param, null, PDO::PARAM_NULL);
        }
    }

    $stmtAutores->execute();
    $autores = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

    if (count($autores) !== count(array_filter([$autor1, $autor2, $autor3]))) {
        echo json_encode(array('message' => 'Uno o más de los autores ingresados no existen o no están activos.'));
        exit;
    }

    // Verifica si el libro ya existe
    $queryLibro = "SELECT * FROM libros WHERE titulo = :titulo";
    $stmtLibro = $db->prepare($queryLibro);
    $stmtLibro->bindValue(":titulo", $titulo, PDO::PARAM_STR);
    $stmtLibro->execute();
    $libro = $stmtLibro->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        // El libro no existe, procede con la inserción
        $queryInsert = "INSERT INTO libros (titulo, lanzamiento, editoriales_editorial, idioma, genero, estado, activo) 
                        VALUES (:titulo, :lanzamiento, :editorial, :idioma, :genero, :estado, :activo)";
        $stmtInsert = $db->prepare($queryInsert);

        $stmtInsert->bindValue(":titulo", $titulo, PDO::PARAM_STR);
        $stmtInsert->bindValue(":lanzamiento", $lanzamiento, PDO::PARAM_INT);
        $stmtInsert->bindValue(":editorial", $editorial, PDO::PARAM_STR);
        $stmtInsert->bindValue(":idioma", $idioma, PDO::PARAM_STR);
        $stmtInsert->bindValue(":genero", $genero, PDO::PARAM_STR);
        $stmtInsert->bindValue(":estado", true, PDO::PARAM_BOOL);
        $stmtInsert->bindValue(':activo', true, PDO::PARAM_BOOL);

        if ($stmtInsert->execute()) {
            // Obtén el ID del libro recién insertado
            $libroId = $db->lastInsertId();

            // Inserta en la tabla libros_autores
            $queryLibroAutor = "INSERT INTO libros_autores (libros_id, autores_id) VALUES (:libro_id, :autor_id)";
            $stmtLibroAutor = $db->prepare($queryLibroAutor);

            foreach ($autores as $autor) {
                $stmtLibroAutor->bindValue(':libro_id', $libroId, PDO::PARAM_INT);
                $stmtLibroAutor->bindValue(':autor_id', $autor['id'], PDO::PARAM_INT);
                $stmtLibroAutor->execute();
            }

            echo json_encode(array("message" => "Libro agregado exitosamente."));
        } else {
            echo json_encode(array("message" => "Error al agregar el libro."));
        }
    } else {
        if (!$libro['activo']) {
            // El libro existe pero no está activo, procede con la activación
            $queryUpdate = "UPDATE libros SET activo = :activo WHERE id = :id";
            $stmtUpdate = $db->prepare($queryUpdate);
            $stmtUpdate->bindValue(':activo', true, PDO::PARAM_BOOL);
            $stmtUpdate->bindValue(':id', $libro['id'], PDO::PARAM_INT);

            if ($stmtUpdate->execute()) {
                echo json_encode(array("message" => "Libro reactivado exitosamente."));
            } else {
                echo json_encode(array("message" => "Error al reactivar el libro."));
            }
        } else {
            // El libro ya existe y está activo
            echo json_encode(array("message" => "El libro ya existe y está activo."));
        }
    }
} else {
    echo json_encode(array("message" => "Datos incompletos. Por favor, asegúrate de proporcionar todos los campos necesarios."));
}
?>
