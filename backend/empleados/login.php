<?php
session_start();
include_once '../db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verificar que los campos no estén vacíos
    if (!empty($username) && !empty($password)) {
        // Conectar a la base de datos
        $database = new Database();
        $db = $database->getConnection();

        // Consultar en la base de datos
        $query = "SELECT id, nombre, pass, rol FROM empleados WHERE nombre = :username AND activo = 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el usuario y si la contraseña es correcta
        if ($user && $password === $user['pass']) {
            // Establecer variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            // Redireccionar según el rol del usuario
            if ($user['rol'] === 1) {
                header("Location: ../../frontend/pages/admin_dashboard.php");
            } else {
                header("Location: ../../frontend/pages/employee_dashboard.php");
            }
            exit;
        } else {
            echo json_encode(array("message" => "Invalid username or password."));
        }
    } else {
        echo json_encode(array("message" => "Please fill in all fields."));
    }
} else {
    // Manejo de caso donde no se realizó una solicitud POST válida
    echo json_encode(array("message" => "Invalid request method."));
}
?>
