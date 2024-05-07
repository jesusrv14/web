<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit();
} else {
    require_once '../vendor/autoload.php';

    $databaseConnection = new MongoDB\Client('mongodb+srv://jesusrusillo:Co_su15sje@codebook.t33csw0.mongodb.net/');
    $myDatabase = $databaseConnection->codebook;
    $userCollection = $myDatabase->users;

    $data = array("correo" => $_SESSION['email']);
    $fetch = $userCollection->findOne($data);

    if ($fetch) {
        $deleteResult = $userCollection->deleteOne($data);

        if ($deleteResult->getDeletedCount() > 0) {
           
            header('Location: ../cierre.php');
            exit();
        } else {
         
            $_SESSION['mensaje'] = "Error al intentar eliminar el perfil.";
            header('Location: ../perfil.php');
            exit();
        }
    } else {
        $_SESSION['mensaje'] = "Error: Perfil no encontrado.";
        header('Location: ../cierre.php');
        exit();
    }
}
?>
