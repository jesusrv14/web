<?php
session_start();
require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$myDatabase = $databaseConnection->codebook;
$postsCollection = $myDatabase->post;

function obtenerPublicacionPorId($postId) {
    global $postsCollection;
    return $postsCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($postId)]);
}

function actualizarPublicacion($postId, $nuevoContenido) {
    global $postsCollection;

    try {
        $result = $postsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($postId)],
            ['$set' => ['contenido' => $nuevoContenido]]
        );

        return $result->getModifiedCount() > 0;
    } catch (Exception $e) {
        error_log("Error al actualizar la publicación: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postId = $_POST["postId"];
    $nuevoContenido = $_POST["nuevoContenido"];

    if (actualizarPublicacion($postId, $nuevoContenido)) {
        $mensaje = "La publicación se actualizó correctamente.";
    } else {
        $mensaje = "Error al actualizar la publicación. Por favor, inténtalo de nuevo más tarde.";
    }
}

$postId = isset($_GET['postId']) ? $_GET['postId'] : null;
if (!$postId) {
    header("Location: index.php");
    exit;
}

$publicacion = obtenerPublicacionPorId($postId);
if (!$publicacion) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Publicación | Codebook</title>
    <link rel="stylesheet" href="./estilos_css/stile.css">
    <link rel="stylesheet" href="./estilos_css/publicaciones.css">
    <link rel="stylesheet" href="./estilos_css/actualizar_post.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Codebook</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="perfil.php">Perfil</a></li>
                    <li><a href="amigos.php">Amigos</a></li>
                    <li><a href="cierre.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="actualizar-publicacion">
            <h2>Actualizar Publicación</h2>
            <?php if (isset($mensaje)) : ?>
                <p><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <div class="respuesta-form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="postId" value="<?php echo htmlspecialchars($postId); ?>">
                    <label for="nuevoContenido">Nuevo Contenido:</label><br>
                    <textarea id="nuevoContenido" name="nuevoContenido" rows="4" cols="50" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea><br>
                    <button id="btn-actualizar-respuesta" type="submit">Actualizar</button>
                </form>
                <div class="post">
                    <h3>Publicación Antes de Modificar:</h3>
                    <p><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <a href="index.php" class="btn-volver">Volver</a>
            <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
