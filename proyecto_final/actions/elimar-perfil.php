<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
} else {
    require_once './vendor/autoload.php';

    $databaseConnection = new MongoDB\Client;
    $myDatabase = $databaseConnection->codebook;
    $userCollection = $myDatabase->users;

    $data = array("correo" => $_SESSION['email']);
    $fetch = $userCollection->findOne($data);

    if ($fetch) {
        // Eliminar el perfil del usuario
        $deleteResult = $userCollection->deleteOne($data);

        if ($deleteResult->getDeletedCount() > 0) {
            // Perfil eliminado con éxito
            // Redireccionar a una página de confirmación o a la página de inicio
            header('Location: index.php');
            exit();
        } else {
            // No se pudo eliminar el perfil
            $_SESSION['mensaje'] = "Error al intentar eliminar el perfil.";
            header('Location: perfil.php');
            exit();
        }
    } else {
        // Perfil no encontrado
        $_SESSION['mensaje'] = "Error: Perfil no encontrado.";
        header('Location: perfil.php');
        exit();
    }
}
?>
