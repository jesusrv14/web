<?php
session_start();

if(isset($_POST['registro'])) {
    require_once '../vendor/autoload.php';

    $cliente = new MongoDB\Client('mongodb+srv://jesusrusillo:Co_su15sje@codebook.t33csw0.mongodb.net/');

    $myDatabase = $cliente->codebook;
    $userCollection = $myDatabase->users;

    $correo = $_POST['email'];

    $usuarioExistenteCorreo = $userCollection->findOne(['correo' => $correo]);

    if($usuarioExistenteCorreo) {
        $_SESSION['error_registro'] = "El correo electrónico ya está registrado";
    }
    else {
        $nombre = $_POST['nombre'];
        $contraseña = sha1($_POST['password']);
        $telefono = $_POST['telefono'];

        $userCollection->insertOne([
            'correo' => $correo,
            'nombre' => $nombre,
            'contraseña' => $contraseña,
            'telefono' => $telefono
        ]);

        $_SESSION['exito_registro'] = "Registro exitoso";
    }
}
?>
