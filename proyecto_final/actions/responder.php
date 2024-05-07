<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client('mongodb://localhost:27017');

$myDatabase = $databaseConnection->codebook;
$postsCollection = $myDatabase->post;
$respuestasCollection = $myDatabase->respuestas;

function insertarPublicacion($contenido, $correoUsuario) {
    global $postsCollection;
    
    $documento = [
        "contenido" => $contenido,
        "email" => $correoUsuario, 
        "fecha" => new MongoDB\BSON\UTCDateTime(time() * 1000),
    ];

    $insertResult = $postsCollection->insertOne($documento);
    return $insertResult->getInsertedId();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId']) && isset($_POST['respuesta'])) {
    $postId = $_POST['postId'];
    $respuesta = $_POST['respuesta'];
    
    $correoUsuario = isset($_SESSION['email']) ? $_SESSION['email'] : null;
    
    $documento = [
        "postId" => $postId,
        "respuesta" => $respuesta,
        "email" => $correoUsuario, 
        "fecha" => new MongoDB\BSON\UTCDateTime(time() * 1000)
    ];
    
    try {
        $respuestasCollection->insertOne($documento);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Error al insertar la respuesta: " . $e->getMessage());
    }
}

function obtenerPublicaciones() {
    global $postsCollection;

    $publicaciones = $postsCollection->find([], ['sort' => ['fecha' => -1]]);
    return $publicaciones;
}

$publicaciones = obtenerPublicaciones();
?>
