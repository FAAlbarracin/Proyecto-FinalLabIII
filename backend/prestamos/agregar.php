<?
include_once 'db.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true); // Decodificar como array asociativo

$empleado = $data['id_empleado'];
$libro = $data['id_libro'];
$socio = $data['id_socio'];

$query = "INSERT INTO prestamo ( libros_id, empleados_id, socios_id) VALUES (:libro, :empleado, :socio)";
$stmt = $db->prepare($query);
$stmt->bindValue(':libro', $libro, PDO::PARAM_INT);
$stmt->bindValue(':empleado', $empleado, PDO::PARAM_INT);
$stmt->bindValue(':socio', $socio, PDO::PARAM_INT);
if ($stmt->execute()) {
    echo json_encode(array("message" => "Prestamo registrado exitosamente."));
} else {
    echo json_encode(array("message" => "Error al registrar el prestamo."));
}