<?php
header('Content-Type: application/json');

include_once '../db.php';

$database = new Database();
$db = $database->getConnection();
$devuelto = 1;

// Verificar que se reciben datos mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_libro = isset($_POST['id_libro']) ? $_POST['id_libro'] : null;
    $id_prestamo = isset($_POST['id_prestamo']) ? $_POST['id_prestamo'] : null;

    // Verificar que ambos campos están presentes y no son nulos
    if ($id_libro !== null && $id_prestamo !== null) {
        $db->beginTransaction();

        try {
            // Actualizar el estado del libro
            $query = "UPDATE libros SET estado = :estado WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':estado', $devuelto, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id_libro, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Actualizar el estado del préstamo
                $query = "UPDATE prestamo SET estado = :estado WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':estado', $devuelto, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id_prestamo, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $db->commit();
                    echo json_encode(array('message' => 'Estado del libro y del préstamo actualizados correctamente.'));
                    header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
                } else {
                    $db->rollBack();
                    echo json_encode(array('message' => 'Error al actualizar el estado del préstamo.'));
                    header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
                }
            } else {
                $db->rollBack();
                echo json_encode(array('message' => 'Error al actualizar el estado del libro.'));
                header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
            }
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(array('message' => 'Error: ' . $e->getMessage()));
            header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
        }
    } else {
        echo json_encode(array('message' => 'Datos incompletos.'));
        header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
    }
} else {
    echo json_encode(array('message' => 'Método de solicitud incorrecto.'));
    header("Location: http://localhost/proyectofinal/frontend/pages/employee_prestamos.php");
}
?>
