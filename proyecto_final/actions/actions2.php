<?php
session_start();

if(isset($_POST['inicio_sesion'])) {
    // Cargar el cliente MongoDB
    require_once '../vendor/autoload.php';

    // Conectar a MongoDB
    $cliente = new MongoDB\Client('mongodb://localhost:27017');

    // Seleccionar la base de datos y la colección de usuarios
    $myDatabase = $cliente->codebook;
    $userCollection = $myDatabase->users;

    // Obtener los datos del formulario
    $correo = $_POST['email'];
    $contraseña = sha1($_POST['password']);

    // Buscar al usuario por correo electrónico y contraseña
    $usuario = $userCollection->findOne(['correo' => $correo, 'contraseña' => $contraseña]);

    // Verificar si se encontró al usuario
    if($usuario) {
        // Guardar el correo electrónico en la sesión
        $_SESSION['email'] = $usuario['correo'];
        
        // Redirigir al usuario a la página de perfil
        header('Location: ./perfil.php');
        exit;
    } else {
        // Si no se encuentra al usuario, mostrar un mensaje de error
        $mensajeError = "Correo electrónico o contraseña incorrectos";
    }
} else {
    // Si no se envió el formulario de inicio de sesión, redirigir a la página de inicio
    header('Location: ./inicio.php');
    exit;
}
?>
