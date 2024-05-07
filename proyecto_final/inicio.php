<?php
session_start();

if(isset($_SESSION['email'])) {
    header('Location: perfil.php');
    exit;
}

if(isset($_POST['inicio_sesion'])) {
    require_once './vendor/autoload.php';

    $cliente = new MongoDB\Client('mongodb://localhost:27017');

    $myDatabase = $cliente->codebook;
    $userCollection = $myDatabase->users;

    $correo = $_POST['email'];
    $contraseña = sha1($_POST['password']);

    $usuario = $userCollection->findOne(['correo' => $correo, 'contraseña' => $contraseña]);

    if($usuario) {
        $_SESSION['email'] = $usuario['correo'];
        
        header('Location: perfil.php');
        exit;
    } else {
        $mensajeError = "Correo electrónico o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/form-stile.css">
</head>
<body>
  <header>
    <div class="container">
      <img src="perfil-foto.jpg" alt="Foto de perfil" class="profile-image"  style="width: 100px; height: 100px;  border-radius: 50%; margin-left: 10px; object-fit: cover;">
      <h1>Codebook</h1>
      <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="amigos.php">Amigos</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container form-container">
    <h2>Iniciar Sesión</h2>
    <form action="inicio.php" method="post">
      <label for="email">Correo Electrónico:</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" name="inicio_sesion">Iniciar Sesión</button>
    </form>
    
    <?php if (isset($mensajeError)): ?>
      <p class="error"><?php echo $mensajeError; ?></p>
    <?php endif; ?>
    <p>¿No tienes una cuenta? <a href="registros.php">Regístrate aquí</a></p>
  </div>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
