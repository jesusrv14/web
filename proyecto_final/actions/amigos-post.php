<?php
require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client('mongodb://localhost:27017');
$myDatabase = $databaseConnection->codebook;
$postsCollection = $myDatabase->post;

function obtenerPostsPorEmail($email)
{
    global $postsCollection;

    $publicaciones = $postsCollection->find(['email' => $email], ['sort' => ['fecha' => -1]]);
    return $publicaciones;
}
?>
