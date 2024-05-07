<?php

require '../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;

$myDatabase = $databaseConnection->codebook;
$userCollection = $myDatabase->users;

if(isset($_POST['inicio_sesion'])) {
    $correo = $_POST['email'];
    $contraseña = sha1($_POST['password']);

    $usuario = $userCollection->findOne(['correo' => $correo, 'contraseña' => $contraseña]);

    if($usuario) {
        session_start();
        $_SESSION['correo'] = $usuario['email']; 
        header('Location: ../perfil.php');
        exit;
    } else {
        $mensajeError = "Correo electrónico o contraseña incorrectos";
    }
}

header('Location: ../inicio.php');
exit;
?>
