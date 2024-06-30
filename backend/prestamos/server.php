<?php
// Verificar el método de solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Dependiendo del método de solicitud, realizar las operaciones apropiadas
switch ($method) {
    case 'GET':
        include_once './mostrar.php';
        break;
    case 'POST':
        include_once './agregar.php';
        break;
    default:
        // Si se recibe un método no admitido, retornar un mensaje de error
        echo json_encode(array('message' => 'Método no admitido'));
        break;
}
?>
