<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./registro.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Registro Empleados</title>
   
</head>
<body>
    <div class="flex items-center justify-center min-h-screen">
        <div class="form-container">
            <div class="flex flex-col justify-center p-6 md:p-10"> 
                <span class="mb-2 text-3xl font-bold">Administrador autorizado para registrar un nuevo empleado</span> 
                <span class="text-gray-400 mb-6 font-bold">Datos del nuevo empleado:</span> 
                <div class="py-2"> 
                    <span class="mb-1 text-sm">Nombre Completo:</span> 
                    <input type="text"
                           name="nombreEmpleado"
                           id="nombre-empleado"
                           class="placeholder-gray-500"
                    />
                </div>
                <div class="py-2"> 
                    <span class="mb-1 text-sm">Email:</span> 
                    <input type="email"
                           class="placeholder-gray-500"
                           name="email"
                           id="email"
                    />
                </div>
                <div class="py-2"> 
                    <span class="mb-1 text-sm">Contraseña:</span> 
                    <input type="password"
                           name="contraseña"
                           id="contraseña"
                           class="placeholder-gray-500"
                    />
                </div>
                <div class="py-2"> <!-- Ajuste de espaciado vertical -->
                    <span class="mb-1 text-sm">Repetir contraseña:</span> <!-- Ajuste de tamaño de texto -->
                    <input type="password"
                           name="contraseña"
                           id="contraseña"
                           class="placeholder-gray-500"
                    />
                </div>
                <button class="w-full bg-black text-white p-2 rounded-lg mt-4 hover:bg-white hover:text-black hover:border hover:border-gray-300"> <!-- Ajuste de margen superior -->
                    Registrar Empleado
                </button>
            </div>
        </div>
    </div>
</body>
</html>