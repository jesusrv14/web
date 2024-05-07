<?php
session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar Sesión - Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Codebook</h1>
      <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="registros.php">Regístrate</a></li>
            <li><a href="inicio.php">Iniciar sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h2>Cerrar Sesión</h2>
    <p>La sesión se ha cerrado exitosamente.</p>
    <p><a href="inicio.php">Ir a la página de inicio</a></p>
  </div>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
