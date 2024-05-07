<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if(isset($_SESSION['correo'])) {
    // Cargar el cliente MongoDB
    require '../vendor/autoload.php';

    // Conectar a MongoDB
    $cliente = new MongoDB\Client('mongodb://localhost:27017');

    // Seleccionar la base de datos y la colección de usuarios
    $myDatabase = $cliente->codebook;
    $userCollection = $myDatabase->users;

    // Obtener el correo electrónico del usuario de la sesión
    $correo = $_SESSION['correo'];

    // Buscar al usuario por su correo electrónico en la base de datos
    $usuario = $userCollection->findOne(['correo' => $correo]);

    // Verificar si se encontró al usuario
    if($usuario) {
        // Mostrar los datos del perfil del usuario
        echo "<h2>Perfil de Usuario</h2>";
        echo "<p>Correo Electrónico: " . $usuario->correo . "</p>";
        echo "<p>Nombre: " . $usuario->nombre . "</p>";
        echo "<p>Teléfono: " . $usuario->telefono . "</p>";
    } else {
        // Si no se encuentra al usuario, mostrar un mensaje de error
        echo "<p>Error: Usuario no encontrado</p>";
    }
} else {
    // Si el usuario no ha iniciado sesión, redirigir al usuario a la página de inicio de sesión
    header('Location: ./inicio.php');
    exit;
}
?>
