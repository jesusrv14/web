<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit();
} else {
    require_once  '../vendor/autoload.php';

    $databaseConnection = new MongoDB\Client;

    $myDatabase = $databaseConnection->codebook;

    $userCollection = $myDatabase->users;

    $userEmail = $_SESSION['email'];

    $data = array("correo" => $userEmail);
    $fetch = $userCollection->findOne($data);

    if ($fetch) {
        if (isset($_POST['correo']) && isset($_POST['nombre']) && isset($_POST['telefono'])) {
            $nuevoCorreo = $_POST['correo'];
            $nuevoNombre = $_POST['nombre'];
            $nuevoTelefono = $_POST['telefono'];

            $actualizacion = [
                '$set' => [
                    'correo' => $nuevoCorreo,
                    'nombre' => $nuevoNombre,
                    'telefono' => $nuevoTelefono
                ]
            ];

            $filtro = ['_id' => $fetch['_id']];
            $resultado = $userCollection->updateOne($filtro, $actualizacion);

            if ($resultado->getModifiedCount() > 0) {
                $_SESSION['nombre'] = $nuevoNombre;
                $_SESSION['correo'] = $nuevoCorreo;
                $_SESSION['telefono'] = $nuevoTelefono;

                $_SESSION['mensaje'] = "¡Datos actualizados correctamente! La sesión se cerrará en unos momentos...";
            } else {
                $_SESSION['mensaje'] = "¡Hubo un error al actualizar los datos!";
            }

            header('Refresh: 5; URL=../cierre.php');
            exit();
        }
    } else {
        $_SESSION['mensaje'] = "Error: Datos del usuario no encontrados.";
    }

    header('Location: ../perfil.php');
    exit();
}
?>