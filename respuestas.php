<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './actions/responder.php';

if (isset($_POST['eliminarRespuesta'])) {
    $respuestaId = $_POST['respuestaId'];
    
    try {
        $respuestasCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($respuestaId)]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Error al eliminar la respuesta: " . $e->getMessage());
    }
}

if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];
} else {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId']) && isset($_POST['respuesta']) && !empty($_POST['respuesta'])) {
    if (!isset($_SESSION['email'])) {
        header('Location: inicio.php');
        exit();
    }
    
    $postId = $_POST['postId'];
    $respuesta = $_POST['respuesta'];
    
    $documento = [
        "postId" => $postId,
        "respuesta" => $respuesta,
        "email" => obtenerCorreoUsuario(), 
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

$respuestas = iterator_to_array($respuestasCollection->find(['postId' => $postId]));

$respuestas = array_reverse($respuestas);

if (empty($respuestas)) {
    $mensaje = "No hay respuestas aún.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Respuestas | Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/respo.css">
  <style>
    .btn-eliminar-respuesta {
      background-color: #ff0000;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      margin-top: 10px;
      display: inline-block; 
    }

    .btn-eliminar-respuesta:hover {
      background-color: #cc0000;
    }

    .btn-actualizar-respuesta {
      background-color: green;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      margin-top: 10px;
      display: inline-block; 
      margin-left: 10px;
    }

    .btn-actualizar-respuesta:hover {
      background-color: green;
    }

    body {
      margin-bottom: 100px; 
    }
  </style>
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
    <section class="respuestas">
        <h2>Respuestas</h2>
        <?php if (isset($_SESSION['email'])): ?>
        <div class="post-form">
            <form id="form-respuesta" class="responder-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <textarea name="respuesta" id="respuesta" placeholder="Escribe tu respuesta" required></textarea>
                <button id="btn-enviar-respuesta" type="submit">Enviar respuesta</button>
            </form>
        </div>
        <?php else: ?>
        <p>Inicia sesión para responder.</p>
        <?php endif; ?>
        <?php if (!empty($respuestas)): ?>
            <div class="container">
                <?php foreach ($respuestas as $respuesta): ?>
                    <div class="respuesta">
                        <?php echo isset($respuesta['email']) ? $respuesta['email'] : "Anónimo"; ?>: <?php echo $respuesta['respuesta']; ?> - <?php echo date('d/m/Y H:i', $respuesta['fecha']->toDateTime()->getTimestamp()); ?>
                        <?php if (isset($_SESSION['email']) && $_SESSION['email'] === $respuesta['email']): ?>
                            <div class="btn-container">
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="respuestaId" value="<?php echo $respuesta['_id']; ?>">
                                    <button type="submit" name="eliminarRespuesta" class="btn-eliminar-respuesta">Eliminar Respuesta</button>
                                </form>
                                <a href="actualizar_respuesta.php?respuestaId=<?php echo $respuesta['_id']; ?>" class="btn-actualizar-respuesta">Actualizar Respuesta</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><?php echo $mensaje; ?></p>
        <?php endif; ?>
    </section>
  </div>

  <footer>
    <div class="container">
      <a href="index.php" class="btn-volver">Volver</a>
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var formRespuesta = document.getElementById('form-respuesta');
        var btnEnviarRespuesta = document.getElementById('btn-enviar-respuesta');
        
        formRespuesta.addEventListener('submit', function(event) {
            btnEnviarRespuesta.disabled = true;
            
            if (formRespuesta.dataset.submitted) {
                event.preventDefault();
                return;
            } else {
                formRespuesta.dataset.submitted = true;
            }
        });
    });
  </script>
</body>
</html>
