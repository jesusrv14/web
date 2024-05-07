<?php
require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client('mongodb+srv://jesusrusillo:Co_su15sje@codebook.t33csw0.mongodb.net/');
$myDatabase = $databaseConnection->codebook;
$postsCollection = $myDatabase->post;

function obtenerPostsPorEmail($email)
{
    global $postsCollection;

    $publicaciones = $postsCollection->find(['email' => $email], ['sort' => ['fecha' => -1]]);
    return $publicaciones;
}
?>
