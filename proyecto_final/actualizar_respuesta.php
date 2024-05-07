<?php
require_once './actions/responder.php';

$respuestaActual = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestaId']) && isset($_POST['respuesta'])) {
    $respuestaId = $_POST['respuestaId'];
    $nuevaRespuesta = $_POST['respuesta'];

    try {
        $respuestasCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($respuestaId)],
            ['$set' => ['respuesta' => $nuevaRespuesta]]
        );

        header('Location: index.php');
        exit();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Error al actualizar la respuesta: " . $e->getMessage());
    }
} else {
    if (isset($_GET['respuestaId'])) {
        $respuestaId = $_GET['respuestaId'];
        $respuesta = $respuestasCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($respuestaId)]);
        if ($respuesta) {
            $respuestaActual = $respuesta['respuesta'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualizar Respuesta | Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/respo.css">
  <link rel="stylesheet" href="./estilos_css/actualizar_respuestas.css">
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
    <section class="actualizar-respuesta">
        <h2>Actualizar Respuesta</h2>
        <div class="respuesta-form">
            <form id="form-actualizar-respuesta" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <textarea name="respuesta" id="respuesta" placeholder="Edita tu respuesta"><?php echo $respuestaActual; ?></textarea>
                <input type="hidden" name="respuestaId" value="<?php echo $respuestaId; ?>">
                <button id="btn-actualizar-respuesta" type="submit">Actualizar respuesta</button>
            </form>
            <?php if (!empty($respuestaActual)): ?>
    <div class="respuesta-actual post">
        <h3>Respuesta Actual:</h3>
        <p><?php echo $respuestaActual; ?></p>
    </div>
<?php endif; ?>

        </div>
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
        var formActualizarRespuesta = document.getElementById('form-actualizar-respuesta');
        formActualizarRespuesta.addEventListener('submit', function(event) {
            event.preventDefault(); 
            var formData = new FormData(formActualizarRespuesta);
            fetch(formActualizarRespuesta.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al actualizar la respuesta.');
                }
                return response.text();
            })
            .then(data => {
                location.href = 'index.php';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
  </script>
</body>
</html>
