<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
} else {
    require_once  './vendor/autoload.php';

    $databaseConnection = new MongoDB\Client;

    $myDatabase = $databaseConnection->codebook;

    $userCollection = $myDatabase->users;

    $data = array("correo" => $_SESSION['email']);
    $fetch = $userCollection->findOne($data);

    if ($fetch) {
        $nombre = isset($fetch['nombre']) ? $fetch['nombre'] : '';
        $correo = isset($fetch['correo']) ? $fetch['correo'] : '';
        $telefono = isset($fetch['telefono']) ? $fetch['telefono'] : '';
    } else {
        $mensaje = "Error: Datos del usuario no encontrados.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil de Usuario - Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/perfil.css">
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
          <li><a href="cierre.php">Cerrar Sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="centered-container">
    <div class="container profile-container">
      <h2>Perfil de Usuario</h2>
      <form action="./actions/actualizar-perfil.php" method="POST">
        <div class="profile-image-container">
          <img src="perfil-foto.jpg" alt="Foto de perfil" class="profile-image"  style="width: 100px; height: 100px;  border-radius: 50%; margin-left: 10px; object-fit: cover;">
        </div>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required><br>

        <label for="correo">Correo Electrónico:</label><br>
        <input type="email" id="correo" name="correo" value="<?php echo isset($correo) ? $correo : ''; ?>" required><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="text" id="telefono" name="telefono" value="<?php echo isset($telefono) ? $telefono : ''; ?>"><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>
        <?php if(isset($_SESSION['mensaje'])): ?>
        <div><?php echo $_SESSION['mensaje']; ?></div>
        <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        <div class="btn-container">
            <button type="submit">Actualizar</button>
        </div>
      </form>
      <div class="btn-container">
        <form action="./actions/eliminar-perfil.php" method="POST">
          <label for="contrasena-eliminar">Contraseña:</label>
          <input type="password" id="contrasena-eliminar" name="contrasena-eliminar" required>
          <button type="submit" class="btn-eliminar">Eliminar perfil</button>
        </form>
      </div>
      <?php if(isset($mensaje)): ?>
      <div><?php echo $mensaje; ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div style="height: 50px;"></div>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>

  <?php if (isset($_SESSION['mensaje'])): ?>
  <div class="hidden-section">
    <p>La sesión se cerrará en unos momentos...</p>
  </div>
  <script>
    setTimeout(function() {
      window.location.href = 'cierre.php';
    }, 5000);
  </script>
  <?php endif; ?>
</body>
</html>
