<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 0) {
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
            <option value="employee_dashboard.php">Home</option>
            <option value="employee_libros.php">Libros</option>
            <option value="employee_prestamos.php">Prestamos</option>
        </select>
        <button><a href="logout.php">logout</a></button>
    </div>
    <div class="content">
        <?php
        echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['username']) . ".</p>";
        ?>

        <div class="container">
            <h2>Agregar Socio</h2>
            <form method="post" action="employee_socios.php">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" class="form-control" required>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary">Agregar Socio</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $nombre = htmlspecialchars($_POST['nombre']);
                $dni = htmlspecialchars($_POST['dni']);

                $data = array(
                    'nombre' => $nombre,
                    'dni' => $dni
                );

                $url = 'http://localhost/proyectofinal/backend/socios/agregar.php';
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



            <form method="get" action="employee_socios.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" class="form-control" >
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
            $dni = isset($_GET['dni']) ? $_GET['dni'] : '';

            // Verificar si se usan filtros
            if (!empty($nombre) || !empty($dni)) {
                $url = 'http://localhost/proyectofinal/backend/socios/filtrar.php?nombre=' . urlencode($nombre) . '&dni=' . urlencode($dni);
            } else {
                $url = 'http://localhost/proyectofinal/backend/socios/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de prestamos.</p>';
            } else {
                $socios = json_decode($response, true);

                if (!empty($socios)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Nombre</th>';
                    echo '<th>DNI</th>';
                    echo '<th>Estado</th>';
                    echo '<th>Acciones</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($socios as $socio) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($socio['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($socio['nombre']) . '</td>';
                        echo '<td>' . htmlspecialchars($socio['dni']) . '</td>';
                        echo '<td>' . htmlspecialchars($socio['activo']) . '</td>';
                        echo '<td>
                        <form method="post" action="employee_socios.php" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar este socio?\');" style="display:inline;">
                            <input type="hidden" name="id" value="' . htmlspecialchars($socio['id']) . '">
                            <button name="delete" type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                data-id="' . htmlspecialchars($socio['id']) . '" 
                                data-nombre="' . htmlspecialchars($socio['nombre']) . '" 
                                data-dni="' . htmlspecialchars($socio['dni']) . '" 
                                data-rol="' . htmlspecialchars($socio['activo']) . '">Actualizar</button>
                      </td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay socios registrados.</p>';
                }
            }


            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
                $id = $_POST['id'];
                $url = 'http://localhost/proyectofinal/backend/socios/eliminar.php';

                $data = array('id' => $id);
                $options = array(
                    'http' => array(
                        'header' => "Content-Type: application/json\r\n",
                        'method' => 'DELETE',
                        'content' => json_encode($data),
                    ),
                );

                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

                if ($result === FALSE) {
                    $error = error_get_last();
                    echo '<div class="alert alert-danger">Error al eliminar el libro: ' . $error['message'] . '</div>';
                } else {
                    $response = json_decode($result, true);
                    echo '<div class="alert alert-success">' . htmlspecialchars($response['message']) . '</div>';
                    // Recargar la página para reflejar los cambios
                    echo '<meta http-equiv="refresh" content="0">';
                }
            } elseif (isset($_POST['update'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $dni = $_POST['dni'];
                $estado = $_POST['activo'];

                $url = 'http://localhost/proyectofinal/backend/socios/modificar.php';

                $data = array(
                    'id' => $id,
                    'nombre' => $nombre,
                    'dni' => $dni,
                    'estado' => $estado
                );
                $options = array(
                    'http' => array(
                        'header' => "Content-type: application/json\r\n",
                        'method' => 'PUT',
                        'content' => json_encode($data),
                    ),
                );

                $context = stream_context_create($options);
                $result = @file_get_contents($url, false, $context);

                if ($result === FALSE) {
                    echo '<p>Error al actualizar el libro.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                    // Recargar la página para reflejar los cambios
                    echo '<meta http-equiv="refresh" content="0">';
                }
            }
            ?>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Actualizar Libros</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="employee_socios.php">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="form-group">
                                <label for="edit-nombre">Nombre</label>
                                <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-dni">DNI</label>
                                <input type="number" class="form-control" id="edit-dni" name="dni" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-activo">Activo</label>
                                <input type="text" class="form-control" id="edit-activo" name="activo" required>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var nombre = button.data('nombre');
                var dni = button.data('dni');
                var activo = button.data('activo');


                var modal = $(this);
                modal.find('#edit-id').val(id);
                modal.find('#edit-nombre').val(nombre);
                modal.find('#edit-dni').val(dni);
                modal.find('#edit-activo').val(activo);

            });
        </script>
    </div>
</body>

</html>