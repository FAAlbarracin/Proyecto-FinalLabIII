<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 1) {
    header("Location: http://localhost/proyectofinal/frontend/pages/loginEmpleado.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestamos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <h1>Panel de Administración</h1>

    <div class="menu">
        <select onchange="location = this.value;">
            <option value="#">Seleccionar...</option>
            <option value="admin_dashboard.php">Home</option>
            <option value="autores.php">Autores</option>
            <option value="editoriales.php">Editoriales</option>
            <option value="libros.php">Libros</option>
            <option value="empleados.php">Empleados</option>
            <option value="socios.php">Socios</option>
        </select>
        <button><a href="logout.php">logout</a></button>
    </div>
    <div class="content">
        <?php
        echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['username']) . ".</p>";
        $id_empleado = htmlspecialchars($_SESSION['user_id']);
        ?>

        <div class="container">
            <h2>Agregar Prestamo</h2>
            <form method="post" action="prestamos.php">
                <div class="form-group">
                    <?php
                    echo "<label>ID del Empleado:</label>";
                    echo "<input type='text' class='form-control' value='$id_empleado' disabled>";
                    ?>
                </div>
                <div class="form-group">
                    <label for="id_libro">ID del Libro:</label>
                    <input type="text" id="id_libro" name="id_libro" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="id_socio">ID del Socio:</label>
                    <input type="text" id="id_socio" name="id_socio" class="form-control" required>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary">Agregar Prestamo</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $empleado = $id_empleado;
                $libro = htmlspecialchars($_POST['id_libro']);
                $socio = htmlspecialchars($_POST['id_socio']);

                $data = array(
                    'id_empleado' => $empleado,
                    'id_libro' => $libro,
                    'id_socio' => $socio,
                );

                $url = 'http://localhost/proyectofinal/backend/prestamos/agregar.php';
                $options = array(
                    'http' => array(
                        'header' => "Content-Type: application/json\r\n",
                        'method' => 'POST',
                        'content' => json_encode($data),
                    ),
                );

                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

                if ($result === FALSE) {
                    echo '<p>Error al agregar el libro.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                }
            }
            ?>



            <form method="get" action="prestamos.php">
                <div class="form-group">
                    <label for="id_empleado">ID del Empleado:</label>
                    <input type="text" id="id_empleado" name="id_empleado" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="id_libro">ID del Libro:</label>
                    <input type="text" id="id_libro" name="id_libro" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="id_socio">ID del Socio:</label>
                    <input type="text" id="id_socio" name="id_socio" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $empleado = isset($_GET['id_empleado']) ? $_GET['id_empleado'] : '';
            $libro = isset($_GET['id_libro']) ? $_GET['id_libro'] : '';
            $socio = isset($_GET['id_socio']) ? $_GET['id_socio'] : '';

            // Verificar si se usan filtros
            if (!empty($empleado) || !empty($libro) || !empty($socio)) {
                $url = 'http://localhost/proyectofinal/backend/prestamos/filtrar.php?id_empleado=' . urlencode($empleado) . '&id_libro=' . urlencode($libro) . '&id_socio=' . urlencode($socio);
            } else {
                $url = 'http://localhost/proyectofinal/backend/prestamos/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de prestamos.</p>';
            } else {
                $prestamos = json_decode($response, true);

                if (!empty($prestamos)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Empleado</th>';
                    echo '<th>Libro</th>';
                    echo '<th>Socio</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($prestamos as $prestamo) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($prestamo['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($prestamo['empleados_id']) . '</td>';
                        echo '<td>' . htmlspecialchars($prestamo['libros_id']) . '</td>';
                        echo '<td>' . htmlspecialchars($prestamo['socios_id']) . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay prestamos registrados.</p>';
                }
            }
            ?>
        </div>
    </div>
</body>

</html>