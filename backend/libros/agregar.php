<?php
include_once '../db.php';

$json = file_get_contents('php://input');

// Decodificar el JSON en un array asociativo
$data = json_decode($json, true);

$titulo = $data['titulo'] ?? null;
$lanzamiento = isset($data['lanzamiento']) ? $data['lanzamiento'] : null;
$editorial = $data['editoriales_editorial'] ?? null;
$idioma = $data['idioma'] ?? null;
$genero = $data['genero'] ?? null;
$autor1 = $data['autor1'] ?? null;
$autor2 = $data['autor2'] ?? null;
$autor3 = $data['autor3'] ?? null;

// Validar que lanzamiento sea un número entero positivo
if (!empty($lanzamiento) && is_numeric($lanzamiento) && $lanzamiento > 0 && intval($lanzamiento) == $lanzamiento) {
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
        $autores = array_filter([$autor1, $autor2, $autor3]);
        if (count($autores) > 0) {
            $placeholders = implode(',', array_fill(0, count($autores), '?'));
            $queryAutores = "SELECT * FROM autores WHERE activo = 1 AND nombre IN ($placeholders)";
            $stmtAutores = $db->prepare($queryAutores);
            $stmtAutores->execute($autores);
            $autoresRows = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

            if (count($autoresRows) !== count($autores)) {
                echo json_encode(array('message' => 'Uno o más de los autores ingresados no existen o no están activos.'));
                exit;
            }
        } else {
            echo json_encode(array('message' => 'Debes ingresar al menos un autor.'));
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
                $queryLibroAutor = "INSERT INTO libros_autores (libros_id, autores_nombre) VALUES (:libro_id, :autor_nombre)";
                $stmtLibroAutor = $db->prepare($queryLibroAutor);

                foreach ($autoresRows as $autor) {
                    $stmtLibroAutor->bindValue(':libro_id', $libroId, PDO::PARAM_INT);
                    $stmtLibroAutor->bindValue(':autor_nombre', $autor['nombre'], PDO::PARAM_STR);
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
} else {
    echo json_encode(array("message" => "El campo 'lanzamiento' debe ser un número entero positivo."));
}
?>
