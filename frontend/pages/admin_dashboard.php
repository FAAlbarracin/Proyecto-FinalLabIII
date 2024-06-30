<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado (por ejemplo, rol 1 para administrador)
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 1) {
    header("Location: http://localhost/proyectofinal/frontend/pages/loginEmpleado.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./styles/admin_dashboard.css">
</head>
<body>
    <h1>Panel de Administración</h1>

    <div class="menu">
        <select onchange="location = this.value;">
            <option value="#">Seleccionar...</option>
            <option value="autores.php">Autores</option>
            <option value="editoriales.php">Editoriales</option>
            <option value="empleados.php">Empleados</option>
            <option value="libros.php">Libros</option>
            <option value="prestamos.php">Préstamos</option>
            <option value="socios.php">Socios</option>
        </select>
        <button><a href="logout.php">logout</a></button>
    </div>

    <div class="content">
        <?php
        // Aquí podrías incluir contenido dinámico dependiendo de la opción seleccionada del menú desplegable
        echo "<p>Bienvenido, " . $_SESSION['username'] . ".</p>";
        ?>
    </div>
</body>
</html>
