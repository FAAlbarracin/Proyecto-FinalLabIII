<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Empleado</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="relative flex flex-col md:flex-row m-6 space-y-8 bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="form-container md:w-1/2">
                <div class="flex flex-col justify-center p-6 md:p-10">
                    <form action="../../backend/empleados/login.php" method="POST">
                        <span class="mb-3 text-3xl md:text-4xl font-bold text-center">Perfil Empleados</span>
                        <span class="text-gray-400 mb-6 font-bold text-center">
                            Bibliotech
                        </span>
                        <div class="py-2">
                            <span class="mb-1 text-md">Usuario:</span>
                            <input 
                                type="text"
                                class="w-full p-2 border border-gray-300 rounded-md placeholder-gray-500"
                                name="username"
                                id="username"
                                required
                            />
                        </div>
                        <div class="py-2">
                            <span class="mb-1 text-md">Contraseña:</span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="w-full p-2 border border-gray-300 rounded-md placeholder-gray-500"
                                required
                            />
                        </div>
                        
                        <button class="w-full bg-black text-white p-2 rounded-lg mt-4 hover:bg-white hover:text-black hover:border hover:border-gray-300" type="submit"
                        >
                            Iniciar Sesión
                        </button>
                    </form>
                </div>
            </div>
            <div class="image-container md:w-1/2">
                <img
                    src="../src/imagenes/biblioteca.jpg"
                    alt="Imagen Biblioteca"
                    class="w-full h-full object-cover rounded-r-2xl"
                />
            </div>
        </div>
    </div>
</body>
</html>
