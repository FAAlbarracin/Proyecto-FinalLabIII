<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado (por ejemplo, rol 1 para administrador)
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
    <title>Agregar Empleado</title>
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
            <option value="prestamos.php">Préstamos</option>
            <option value="socios.php">Socios</option>
        </select>
    </div>
    <div class="content">
        <?php
        // Aquí podrías incluir contenido dinámico dependiendo de la opción seleccionada del menú desplegable
        echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['username']) . ".</p>";
        ?>

        <div class="container">
            <h2>Agregar Empleado</h2>
            <form method="post" action="empleados.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="pass">Contraseña:</label>
                    <input type="text" id="pass" name="pass" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="1">Administrador</option>
                        <option value="0">Empleado</option>
                    </select>
                </div>

                <button type="submit" name="agregar" class="btn btn-primary">Agregar Empleado</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $nombre = $_POST['nombre'];
                $pass = $_POST['pass'];
                $dni = $_POST['dni'];
                $rol = $_POST['rol'];

                $data = array(
                    'nombre' => $nombre,
                    'pass' => $pass,
                    'dni' => $dni,
                    'rol' => $rol
                );

                $url = 'http://localhost/proyectofinal/backend/empleados/agregar.php'; // Ajusta la URL al archivo empleados.php correcto
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
                    echo '<p>Error al agregar el empleado.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                }
            }
            ?>

            <form method="get" action="empleados.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control">
                </div>
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
            $dni = isset($_GET['dni']) ? $_GET['dni'] : '';

            // Verificar si se usan filtros
            if (!empty($nombre) || !empty($dni)) {
                $url = 'http://localhost/proyectofinal/backend/empleados/filtrar.php?nombre=' . urlencode($nombre) . '&dni=' . urlencode($dni);
            } else {
                $url = 'http://localhost/proyectofinal/backend/empleados/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de empleados.</p>';
            } else {
                $empleados = json_decode($response, true);

                if (!empty($empleados)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Nombre</th>';
                    echo '<th>DNI</th>';
                    echo '<th>Rol</th>';
                    echo '<th>Acciones</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($empleados as $empleado) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($empleado['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($empleado['nombre']) . '</td>';
                        echo '<td>' . htmlspecialchars($empleado['dni']) . '</td>';
                        echo '<td>' . htmlspecialchars($empleado['rol']) . '</td>';
                        echo '<td>
                                <form method="post" action="empleados.php" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar este empleado?\');" style="display:inline;">
                                    <input type="hidden" name="id" value="' . htmlspecialchars($empleado['id']) . '">
                                    <button name="delete" type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                        data-id="' . htmlspecialchars($empleado['id']) . '" 
                                        data-nombre="' . htmlspecialchars($empleado['nombre']) . '" 
                                        data-pass="' . htmlspecialchars($empleado['pass']) . '" 
                                        data-dni="' . htmlspecialchars($empleado['dni']) . '" 
                                        data-rol="' . htmlspecialchars($empleado['rol']) . '">Actualizar</button>
                              </td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay empleados registrados.</p>';
                }
            }

            // Manejo de la solicitud de eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
                $id = $_POST['id'];
                $url = 'http://localhost/proyectofinal/backend/empleados/eliminar.php';

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
                    echo '<p>Error al eliminar el empleado.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                    // Recargar la página para reflejar los cambios
                    echo '<meta http-equiv="refresh" content="0">';
                }
            } elseif (isset($_POST['update'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $pass = $_POST['pass'];
                $dni = $_POST['dni'];
                $rol = $_POST['rol'];
                $url = 'http://localhost/proyectofinal/backend/empleados/modificar.php';

                $data = array(
                    'id' => $id,
                    'nombre' => $nombre,
                    'pass' => $pass,
                    'dni' => $dni,
                    'rol' => $rol
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
                    echo '<p>Error al actualizar el empleado.</p>';
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
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Actualizar Empleado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="empleados.php">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="form-group">
                                <label for="edit-nombre">Nombre</label>
                                <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-pass">Contraseña</label>
                                <input type="text" class="form-control" id="edit-pass" name="pass" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-dni">DNI</label>
                                <input type="text" class="form-control" id="edit-dni" name="dni" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-rol">Rol</label>
                                <select name="rol" class="form-control" id="edit-rol" required>
                                    <option value="#">Seleccione</option>
                                    <option value="1">Administrador</option>
                                    <option value="0">Empleado</option>
                                </select>
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
                var pass = button.data('pass');
                var dni = button.data('dni');
                var rol = button.data('rol');

                var modal = $(this);
                modal.find('#edit-id').val(id);
                modal.find('#edit-nombre').val(nombre);
                modal.find('#edit-pass').val(pass);
                modal.find('#edit-dni').val(dni);
                modal.find('#edit-rol').val(rol);
            });
        </script>
    </div>
</body>
</html>
