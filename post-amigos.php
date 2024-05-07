<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Posts de Usuario | Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/amigos-post.css">
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
    <?php
    require_once './actions/amigos-post.php';

    if (isset($_GET['email'])) {
        $emailUsuario = $_GET['email'];

        $publicacionesUsuario = obtenerPostsPorEmail($emailUsuario);

        echo "<h2>Posts de $emailUsuario</h2>";
        foreach ($publicacionesUsuario as $publicacion) {
            echo "<div class='post-amigo'>";
            echo "<p>{$publicacion['contenido']}</p>";
            echo "<p class='post-info'>Publicado por: {$publicacion['email']} - " . date('d/m/Y H:i', $publicacion['fecha']->toDateTime()->getTimestamp()) . "</p>";
            echo "<a href='respuestas.php?postId={$publicacion['_id']}' class='btn-ver-respuestas-amigo'>Ver Respuestas</a>"; 
            echo "</div>";
        }
    } else {
        echo "<p>No se proporcionó un correo electrónico de usuario.</p>";
    }
    ?>
  </div>

  <footer>
    <div class="container">
      <a href="amigos.php" class="btn-volver">Volver</a>
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
