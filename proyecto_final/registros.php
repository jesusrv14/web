<?php
session_start();

if(isset($_POST['registro'])) {
    require_once './vendor/autoload.php';

    $cliente = new MongoDB\Client('mongodb://localhost:27017');

    $myDatabase = $cliente->codebook;
    $userCollection = $myDatabase->users;

    $correo = $_POST['email'];
    $nombre = $_POST['nombre'];
    $contraseña = sha1($_POST['password']);
    $telefono = $_POST['telefono'];

    $usuarioExistente = $userCollection->findOne(['correo' => $correo]);

    if($usuarioExistente) {
        $_SESSION['error_registro'] = "El correo electrónico ya está registrado";
    } else {
        
        $userCollection->insertOne([
            'correo' => $correo,
            'nombre' => $nombre,
            'contraseña' => $contraseña,
            'telefono' => $telefono
        ]);

        
        $_SESSION['exito_registro'] = "Registro exitoso";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse - Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Codebook</h1>
      <nav>
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="amigos.php">Amigos</a></li>
            <li><a href="cierre.php">Cerrar Sesión</a></li>
            <li><a href="inicio.php">Iniciar sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h2>Registrarse</h2>
    <?php if (isset($_SESSION['error_registro'])): ?>
        <p class="error"><?php echo $_SESSION['error_registro']; ?></p>
        <?php unset($_SESSION['error_registro']); ?>
    <?php elseif (isset($_SESSION['exito_registro'])): ?>
        <p class="exito"><?php echo $_SESSION['exito_registro']; ?></p>
        <?php unset($_SESSION['exito_registro']); ?>
    <?php endif; ?>
    <form action="" method="post">
      <label for="nombre">Nombre:</label><br>
      <input type="text" id="nombre" name="nombre" required><br>

      <label for="email">Correo Electrónico:</label><br>
      <input type="email" id="email" name="email" required><br>

      <label for="password">Contraseña:</label><br>
      <input type="password" id="password" name="password" required><br>

      <label for="telefono">Teléfono:</label><br>
      <input type="text" id="telefono" name="telefono"><br>

      <button type="submit" name="registro">Registrarse</button><br>
    </form>
    
    <p>¿Ya tienes una cuenta? <a href="inicio.php">Inicia sesión aquí</a></p>
  </div>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
