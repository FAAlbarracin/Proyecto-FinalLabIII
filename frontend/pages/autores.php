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
    <title>Agregar Autor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <h1>Panel de Administración</h1>

    <div class="menu">
        <select onchange="location = this.value;">
            <option value="#">Seleccionar...</option>
            <option value="admin_dashboard.php">Home</option>
            <option value="editoriales.php">Editoriales</option>
            <option value="empleados.php">Empleados</option>
            <option value="libros.php">Libros</option>
            <option value="prestamos.php">Préstamos</option>
            <option value="socios.php">Socios</option>
        </select>
    </div>
    <div class="content">
        <?php
        // Aquí podrías incluir contenido dinámico dependiendo de la opción seleccionada del menú desplegable
        echo "<p>Bienvenido, " . $_SESSION['username'] . ".</p>";
        ?>

        <div class="container">
            <h2>Agregar Autor</h2>
            <form method="post" action="autores.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="genero">Género:</label>
                    <input type="text" id="genero" name="genero" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nacion">Nacionalidad:</label>
                    <input type="text" id="nacion" name="nacion" class="form-control">
                </div>

                <button type="submit" name="agregar" class="btn btn-primary">Agregar Autor</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $nombre = $_POST['nombre'];
                $genero = $_POST['genero'];
                $nacion = isset($_POST['nacion']) ? $_POST['nacion'] : '';

                $data = array(
                    'nombre' => $nombre,
                    'genero' => $genero,
                    'nacion' => $nacion
                );

                $url = 'http://localhost/proyectofinal/backend/autores/agregar.php'; // Ajusta la URL al archivo autores.php correcto
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
                    echo '<p>Error al agregar el autor.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                }
            }
            ?>

            <form method="get" action="autores.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control">
                </div>

                <div class="form-group">
                    <label for="genero">Género:</label>
                    <input type="text" id="genero" name="genero" class="form-control">
                </div>

                <div class="form-group">
                    <label for="nacion">Nacionalidad:</label>
                    <input type="text" id="nacion" name="nacion" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
            $genero = isset($_GET['genero']) ? $_GET['genero'] : '';
            $nacion = isset($_GET['nacion']) ? $_GET['nacion'] : '';

            // Verificar si se usan filtros
            if (!empty($nombre) || !empty($genero) || !empty($nacion)) {
                $url = 'http://localhost/proyectofinal/backend/autores/filtrar.php?nombre=' . urlencode($nombre) . '&genero=' . urlencode($genero) . '&nacion=' . urlencode($nacion);
            } else {
                $url = 'http://localhost/proyectofinal/backend/autores/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de autores.</p>';
            } else {
                $autores = json_decode($response, true);

                if (!empty($autores)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Nombre</th>';
                    echo '<th>Género</th>';
                    echo '<th>Nacionalidad</th>';
                    echo '<th>Acciones</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($autores as $autor) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($autor['nombre']) . '</td>';
                        echo '<td>' . htmlspecialchars($autor['genero']) . '</td>';
                        echo '<td>' . htmlspecialchars($autor['nacion']) . '</td>';
                        echo '<td>
                            <form method="post" action="autores.php"]); ?" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar este autor?\');" style="display:inline;">
                                <input type="hidden" name="nombre" value="' . htmlspecialchars($autor['nombre']) . '">
                                <button name="delete" type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                    data-nombre="' . htmlspecialchars($autor['nombre']) . '" 
                                    data-genero="' . htmlspecialchars($autor['genero']) . '" 
                                    data-nacion="' . htmlspecialchars($autor['nacion']) . '">Actualizar</button>
                          </td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay autores registrados.</p>';
                }
            }

            // Manejo de la solicitud de eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['nombre'])) {
                $nombre = $_POST['nombre'];
                $url = 'http://localhost/proyectofinal/backend/autores/eliminar.php';

                $data = array('nombre' => $nombre);
                $options = array(
                    'http' => array(
                        'header' => "Content-type: application/json\r\n",
                        'method' => 'DELETE',
                        'content' => json_encode($data),
                    ),
                );

                $context = stream_context_create($options);
                $result = @file_get_contents($url, false, $context);

                if ($result === FALSE) {
                    echo '<p>Error al eliminar el autor.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                    // Recargar la página para reflejar los cambios
                    echo '<meta http-equiv="refresh" content="0">';
                }
            } elseif (isset($_POST['update'])) {
                $nombre = $_POST['nombre'];
                $genero = $_POST['genero'];
                $nacion = $_POST['nacion'];
                $url = 'http://localhost/proyectofinal/backend/autores/modificar.php';

                $data = array(
                    'nombre' => $nombre,
                    'genero' => $genero,
                    'nacion' => $nacion,
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
                    echo '<p>Error al actualizar el autor.</p>';
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
                        <h5 class="modal-title" id="editModalLabel">Actualizar Autor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="edit-nombre">Nombre</label>
                                <input type="text" class="form-control" id="edit-nombre" name="nombre" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-genero">Género</label>
                                <input type="text" class="form-control" id="edit-genero" name="genero">
                            </div>
                            <div class="form-group">
                                <label for="edit-nacion">Nacionalidad</label>
                                <input type="text" class="form-control" id="edit-nacion" name="nacion">
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
                var nombre = button.data('nombre');
                var genero = button.data('genero');
                var nacion = button.data('nacion');

                var modal = $(this);
                modal.find('#edit-nombre').val(nombre);
                modal.find('#edit-genero').val(genero);
                modal.find('#edit-nacion').val(nacion);
            });
        </script>
    </div>
</body>

</html>