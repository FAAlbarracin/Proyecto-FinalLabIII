<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado (por ejemplo, rol 1 para administrador)
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 1) {
    header("Location: http://localhost/proyectofinal/frontend/pages/loginEmpleado.php");
    exit;
}

function sanitizarEntrada($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros</title>
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
            <option value="empleados.php">Empleados</option>
            <option value="prestamos.php">Préstamos</option>
            <option value="socios.php">Socios</option>
        </select>
        <button><a href="logout.php">logout</a></button>
    </div>
    <div class="content">
        <?php
        // Aquí podrías incluir contenido dinámico dependiendo de la opción seleccionada del menú desplegable
        echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['username']) . ".</p>";
        ?>

        <div class="container">
            <h2>Agregar Libro</h2>
            <form method="post" action="libros.php">
                <div class="form-group">
                    <label for="titulo">Titulo:</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="lanzamiento">Lanzamiento:</label>
                    <input type="number" id="lanzamiento" name="lanzamiento" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="autor1">Autor1:</label>
                    <input type="text" id="autor1" name="autor1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="autor2">Autor2:</label>
                    <input type="text" id="autor2" name="autor2" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="autor3">Autor3:</label>
                    <input type="text" id="autor3" name="autor3" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="editorial">Editorial:</label>
                    <input type="text" id="editorial" name="editorial" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="idioma">Idioma:</label>
                    <input type="text" id="idioma" name="idioma" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="genero">Genero:</label>
                    <input type="text" id="genero" name="genero" class="form-control" required>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary">Agregar Libro</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["agregar"])) {
                $titulo = $_POST['titulo'];
                $autor1= $_POST['autor1'];
                $autor2= $_POST['autor2'];
                $autor3= $_POST['autor3'];
                $lanzamiento = $_POST['lanzamiento'];
                $editorial = $_POST['editorial'];
                $idioma = $_POST['idioma'];
                $genero = $_POST['genero'];

                $data = array(
                    'titulo' => $titulo,
                    'autor1' => $autor1,
                    'autor2' => $autor2,
                    'autor3' => $autor3,
                    'lanzamiento' => $lanzamiento,
                    'editoriales_editorial' => $editorial,
                    'idioma' => $idioma,
                    'genero' => $genero

                );

                $url = 'http://localhost/proyectofinal/backend/libros/agregar.php'; // Ajusta la URL al archivo empleados.php correcto
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

            <form method="get" action="libros.php">
            <div class="form-group">
                    <label for="titulo">Titulo:</label>
                    <input type="text" id="titulo" name="titulo" class="form-control">
                </div>
                <div class="form-group">
                    <label for="genero">Genero:</label>
                    <input type="text" id="genero" name="genero" class="form-control">
                </div>
                <div class="form-group">
                    <label for="idioma">Idioma:</label>
                    <input type="text" id="idioma" name="idioma" class="form-control">
                </div>
                <!-- <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control" required>
                        <option value="1">No Prestado</option>
                        <option value="0">Prestado</option>
                    </select>
                </div> -->

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            $titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
            $genero = isset($_GET['genero']) ? $_GET['genero'] : '';
            $idioma = isset($_GET['idioma']) ? $_GET['idioma'] :'';

            // Verificar si se usan filtros
            if (!empty($titulo) || !empty($genero) || !empty($idioma)) {
                $url = 'http://localhost/proyectofinal/backend/libros/filtrar.php?titulo=' . urlencode($titulo) . '&genero=' . urlencode($genero) .'&idioma=' . urlencode($idioma);
            } else {
                $url = 'http://localhost/proyectofinal/backend/libros/mostrar.php';
            }

            // Obtener la respuesta de la API
            $response = @file_get_contents($url);

            // Verificar si la solicitud fue exitosa
            if ($response === FALSE) {
                echo '<p>Error al obtener la lista de empleados.</p>';
            } else {
                $libros = json_decode($response, true);

                if (!empty($libros)) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Titulo</th>';
                    echo '<th>Lanzamiento</th>';
                    echo '<th>Editorial</th>';
                    echo '<th>Idioma</th>';
                    echo '<th>Genero</th>';
                    echo '<th>Estado</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($libros as $libro) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($libro['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['titulo']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['lanzamiento']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['editoriales_editorial']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['idioma']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['genero']) . '</td>';
                        echo '<td>' . htmlspecialchars($libro['estado']) . '</td>';
                        echo '<td>
                                <form method="post" action="libros.php" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar este libro?\');" style="display:inline;">
                                    <input type="hidden" name="titulo" value="' . htmlspecialchars($libro['titulo']) . '">
                                    <button name="delete" type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                        data-id="' . htmlspecialchars($libro['id']) . '" 
                                        data-titulo="' . htmlspecialchars($libro['titulo']) . '" 
                                        data-lanzamiento="' . htmlspecialchars($libro['lanzamiento']) . '" 
                                        data-editorial="' . htmlspecialchars($libro['editoriales_editorial']) . '" 
                                        data-idioma="' . htmlspecialchars($libro['idioma']) . '"
                                        data-genero="' . htmlspecialchars($libro['genero']) . '"
                                        data-rol="' . htmlspecialchars($libro['estado']) . '">Actualizar</button>
                              </td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay libros registrados.</p>';
                }
            }

            // Manejo de la solicitud de eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['titulo'])) {
                $titulo = sanitizarEntrada($_POST['titulo']);
                $url = 'http://localhost/proyectofinal/backend/libros/eliminar.php';
    
                $data = array('titulo' => $titulo);
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
                $titulo = $_POST['titulo'];
                $lanzamiento = $_POST['lanzamiento'];
                $editorial = $_POST['editorial'];
                $idioma = $_POST['idioma'];
                $genero = $_POST['genero'];
                $estado = $_POST['estado'];

                $url = 'http://localhost/proyectofinal/backend/libros/modificar.php';

                $data = array(
                    'id' => $id,
                    'titulo' => $titulo,
                    'lanzamiento' => $lanzamiento,
                    'editorial' => $editorial,
                    'idioma' => $idioma,
                    'genero' => $genero,
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
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Actualizar Libros</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="libros.php">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="form-group">
                                <label for="edit-titulo">Titulo</label>
                                <input type="text" class="form-control" id="edit-titulo" name="titulo" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-lanzamiento">Lanzamiento</label>
                                <input type="number" class="form-control" id="edit-lanzamiento" name="lanzamiento" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-editorial">Editorial</label>
                                <input type="text" class="form-control" id="edit-editorial" name="editorial" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-idioma">Idioma</label>
                                <input type="text" class="form-control" id="edit-idioma" name="idioma" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-genero">Genero</label>
                                <input type="text" class="form-control" id="edit-genero" name="genero" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-estado">Estado</label>
                                <select name="estado" class="form-control" id="edit-estado" required>
                                    <option value="#">Seleccione</option>
                                    <option value="1">No Prestado</option>
                                    <option value="0">Prestado</option>
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
                var titulo = button.data('titulo');
                var lanzamiento = button.data('lanzamiento');
                var editorial = button.data('editorial');
                var idioma = button.data('idioma');
                var genero = button.data('genero');
                var estado = button.data('estado');

                var modal = $(this);
                modal.find('#edit-id').val(id);
                modal.find('#edit-titulo').val(titulo);
                modal.find('#edit-lanzamiento').val(lanzamiento);
                modal.find('#edit-editorial').val(editorial);
                modal.find('#edit-idioma').val(idioma);
                modal.find('#edit-genero').val(genero);
                modal.find('#edit-estado').val(estado);
            });
        </script>
    </div>
</body>
</html>
