<?php
require_once './actions/post.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $postId = $_POST['delete_post_id'];

    $deleted = borrarPublicacion($postId);

    if ($deleted) {
        $deletedResponses = borrarRespuestasPorPublicacion($postId);

        if ($deletedResponses) {
            echo "La publicación y todas las respuestas asociadas se eliminaron correctamente.";
        } else {
            echo "Error al eliminar las respuestas asociadas a la publicación.";
        }
    } else {
        echo "Error al eliminar la publicación.";
    }
} else {
    echo "Solicitud no válida.";
}
?>
