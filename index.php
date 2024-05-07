<?php
session_start();
require_once './actions/post.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $postId = $_GET['delete'];
    $correoUsuario = $_SESSION['email'];
    borrarPublicacionYRespuestas($postId, $correoUsuario);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    if (!isset($_SESSION['email'])) {
        header('Location: inicio.php');
        exit();
    }
    
    $correoUsuario = $_SESSION['email']; 
    insertarPublicacion($_POST['content'], $correoUsuario);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$publicaciones = obtenerPublicaciones();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Publicaciones | Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/publicaciones.css">
  <style>
    button.btn-eliminar-publicacion {
      background-color: #ff0000 !important;
      color: #fff !important;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin-top: 10px;
    }

    button.btn-eliminar-publicacion:hover {
      background-color: #cc0000 !important;
    }

    a.btn-actualizar-publicacion {
      background-color: green !important;
      color: #fff !important;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin-top: 10px;
    }

    a.btn-actualizar-publicacion:hover {
      background-color: darkgreen !important;
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
            <?php if (!isset($_SESSION['email'])): ?>
            <li><a href="inicio.php">Iniciar sesión</a></li>
            <?php else: ?>
            <li><a href="cierre.php">Cerrar Sesión</a></li>
            <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <section class="post-form">
      <h2>Crear Publicación</h2>
      <?php if (isset($_SESSION['email'])): ?>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <textarea name="content" placeholder="¿Qué estás pensando?"></textarea>
        <button type="submit">Publicar</button>
      </form>
      <?php else: ?>
      <p>Inicia sesión para publicar una nueva publicación.</p>
      <?php endif; ?>
    </section>

    <section class="posts">
      <?php foreach ($publicaciones as $publicacion): ?>
        <div class="post">
          <p><?php echo $publicacion['contenido']; ?></p>
          <span><?php echo isset($publicacion['email']) ? $publicacion['email'] : "Correo Anonimo"; ?> - <?php echo date('d/m/Y H:i', $publicacion['fecha']->toDateTime()->getTimestamp()); ?></span>
          <?php if (isset($_SESSION['email']) && $_SESSION['email'] === $publicacion['email']): ?>
            <div class="btn-container">
                <a href="?delete=<?php echo $publicacion['_id']; ?>" class="btn-eliminar"><button class="btn-eliminar-publicacion" type="submit">Borrar Publicación</button></a>
                <a href="actualizar_post.php?postId=<?php echo $publicacion['_id']; ?>" class="btn-actualizar-publicacion">Actualizar Publicación</a>
            </div>
          <?php endif; ?>
          <a href="respuestas.php?postId=<?php echo $publicacion['_id']; ?>" class="btn-ver-respuestas">Ver Respuestas</a>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
