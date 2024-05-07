<?php
require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client('mongodb://localhost:27017');

$myDatabase = $databaseConnection->codebook;
$postsCollection = $myDatabase->post;

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

function obtenerPublicaciones() {
    global $postsCollection;

    $publicaciones = $postsCollection->find([], ['sort' => ['fecha' => -1]]);
    return $publicaciones;
}

function borrarPublicacionYRespuestas($postId, $correoUsuario) {
    global $postsCollection, $respuestasCollection;

    $resultado = $postsCollection->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($postId),
        'email' => $correoUsuario
    ]);

    if ($resultado->getDeletedCount() > 0) {
        $respuestasCollection->deleteMany(['postId' => (string) $postId]);
        return true;
    } else {
        return false;
    }
}

function actualizarPublicacion($contenidoActual, $nuevoContenido) {
    global $postsCollection;

    try {
        $result = $postsCollection->updateOne(
            ['contenido' => $contenidoActual],
            ['$set' => ['contenido' => $nuevoContenido]]
        );

        if ($result->getModifiedCount() > 0) {
            return true;
        } else {
            throw new Exception("Error al actualizar la publicación: No se encontró la publicación para actualizar");
        }
    } catch (Exception $e) {
        $errorLog = fopen("error_log.txt", "a") or die("No se pudo abrir el archivo de registro.");
        $errorMessage = "Error al actualizar la publicación: " . $e->getMessage() . "\n";
        fwrite($errorLog, $errorMessage);
        fclose($errorLog);

        return false;
    }
}
?>
