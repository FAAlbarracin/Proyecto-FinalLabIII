<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado (por ejemplo, rol 1 para administrador)
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 0) {
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
            <option value="employee_dashboard.php">Home</option>
            <option value="employee_prestamos.php">Préstamos</option>
            <option value="employee_socios.php">Socios</option>
        </select>
        <button><a href="logout.php">logout</a></button>
    </div>
    <div class="content">
        <?php
        // Aquí podrías incluir contenido dinámico dependiendo de la opción seleccionada del menú desplegable
        echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['username']) . ".</p>";
        ?>

        <div class="container">
            <form method="get" action="employee_libros.php">
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
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>

            <?php
            // Asegurarse de que las variables de sesión existen antes de usarlas
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
                echo '<p>Error: No se ha iniciado sesión correctamente.</p>';
                exit;
            }

            $titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
            $genero = isset($_GET['genero']) ? $_GET['genero'] : '';
            $idioma = isset($_GET['idioma']) ? $_GET['idioma'] : '';

            // Verificar si se usan filtros
            if (!empty($titulo) || !empty($genero) || !empty($idioma)) {
                $url = 'http://localhost/proyectofinal/backend/libros/filtrar.php?titulo=' . urlencode($titulo) . '&genero=' . urlencode($genero) . '&idioma=' . urlencode($idioma);
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
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No hay libros registrados.</p>';
                }
            }
            ?>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
</body>
</html>
