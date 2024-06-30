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
            <option value="autores.php">Autores</option>
            <option value="empleados.php">Empleados</option>
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
            <h2>Agregar Editorial</h2>
            <form method="post" action="editoriales.php">
                <div class="form-group">
                    <label for="editorial">Editorial:</label>
                    <input type="text" id="editorial" name="editorial" class="form-control" required>
                </div>

                <button type="submit" name="agregar" class="btn btn-primary">Agregar Editorial</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $editorial = $_POST['editorial'];

                $data = array(
                    'editorial' => $editorial
                );

                $url = 'http://localhost/proyectofinal/backend/editoriales/agregar.php'; // Ajusta la URL al archivo autores.php correcto
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
                    echo '<p>Error al agregar el editorial.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                }
            }
            ?>

            <form method="get" action="editoriales.php">
                <div class="form-group">
                    <label for="editorial">Editorial:</label>
                    <input type="text" id="editorial" name="editorial" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $editorial = isset($_GET['editorial']) ? $_GET['editorial'] : '';

            // Verificar si se usan filtros
            if (!empty($editorial)) {
                $url = 'http://localhost/proyectofinal/backend/editoriales/filtrar.php?editorial=' . urlencode($editorial);
            } else {
                $url = 'http://localhost/proyectofinal/backend/editoriales/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de editoriales.</p>';
            } else {
                $editoriales = json_decode($response, true);

                if (!empty($editoriales)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Editorial</th>';
                    echo '<th>Acciones</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($editoriales as $editorial) {
                        // Verificar que $editorial sea un array y contenga la clave 'editorial'
                        if (is_array($editorial) && isset($editorial['editorial'])) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($editorial['editorial']) . '</td>';
                            echo '<td>
                                <form method="post" action="editoriales.php" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar esta editorial?\');" style="display:inline;">
                                    <input type="hidden" name="editorial" value="' . htmlspecialchars($editorial['editorial']) . '">
                                    <button name="delete" type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                        data-editorial="' . htmlspecialchars($editorial['editorial']) . '">Actualizar</button>
                              </td>';
                            echo '</tr>';
                        }
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay editoriales registrados.</p>';
                }
            }

            // Manejo de la solicitud de eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['editorial'])) {
                $editorial = $_POST['editorial'];
                $url = 'http://localhost/proyectofinal/backend/editoriales/eliminar.php';

                $data = array('editorial' => $editorial);
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
                    echo '<p>Error al eliminar la editorial.</p>';
                } else {
                    $response = json_decode($result, true);
                    echo '<p>' . htmlspecialchars($response['message']) . '</p>';
                    // Recargar la página para reflejar los cambios
                    echo '<meta http-equiv="refresh" content="0">';
                }
            } elseif (isset($_POST['update'])) {
                $editorial = $_POST['editorial'];
                $url = 'http://localhost/proyectofinal/backend/editoriales/modificar.php';

                $data = array(
                    'editorial' => $editorial
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
                    echo '<p>Error al actualizar la editorial.</p>';
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
                        <h5 class="modal-title" id="editModalLabel">Actualizar Editorial</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="editoriales.php">
                            <div class="form-group">
                                <label for="edit-editorial">Editorial</label>
                                <input type="text" class="form-control" id="edit-editorial" name="editorial" readonly>
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
                var editorial = button.data('editorial');

                var modal = $(this);
                modal.find('#edit-editorial').val(editorial);
            });
        </script>
    </div>
</body>

</html>
