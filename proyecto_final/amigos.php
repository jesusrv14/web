<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: inicio.php');
    exit;
}



require_once './vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$myDatabase = $databaseConnection->codebook;
$userCollection = $myDatabase->users;

if (isset($_SESSION['email'])) {
    $usuarioActual = $userCollection->findOne(['correo' => $_SESSION['email']]);
}

$idUsuarioActual = $usuarioActual['_id'] ?? null;

$amigosCollection = $myDatabase->amigos;
$listaAmigos = $amigosCollection->find(
    [
        '$or' => [
            ['idUsuario' => $idUsuarioActual],
            ['idAmigo' => $idUsuarioActual]
        ]
    ]
);

$amigos = [];
foreach ($listaAmigos as $amigo) {
    if ($amigo['idUsuario'] == $idUsuarioActual) {
        $amigoUsuario = $userCollection->findOne(['_id' => $amigo['idAmigo']]);
    } else {
        $amigoUsuario = $userCollection->findOne(['_id' => $amigo['idUsuario']]);
    }
    if ($amigoUsuario) {
        $amigos[] = $amigoUsuario;
    }
}

function buscarUsuario($email, $userCollection)
{
    return $userCollection->findOne(['correo' => $email]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregarAmigo'])) {
    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    $idUsuario = $usuarioActual['_id'];
    $idAmigo = $_POST['agregarAmigo'];
    if ($idUsuario != $idAmigo) { 
        $resultado = agregarAmigo($idAmigo, $idUsuario, $amigosCollection, $userCollection, $amigos);
        if ($resultado !== true) {
            $error = $resultado; 
        }
    } else {
        $error = "No puedes agregarte a ti mismo como amigo.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminarAmigo'])) {
    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    $idAmigo = $_POST['eliminarAmigo'];
    $resultado = eliminarAmigo($idAmigo, $idUsuarioActual, $amigosCollection);
    if ($resultado !== true) {
        $error = $resultado; 
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['buscar'])) {
    $emailBusqueda = $_POST['email'];
    $usuarioBuscado = buscarUsuario($emailBusqueda, $userCollection);
    if (!$usuarioBuscado) {
        $errorBusqueda = "No se encontró ningún usuario con ese correo electrónico.";
    }
}

function agregarAmigo($idAmigo, $idUsuario, $amigosCollection, $userCollection, $amigos)
{
    foreach ($amigos as $amigo) {
        if ($amigo['_id'] == $idAmigo) {
            return "Ya es tu amigo";
        }
    }

    $amigo = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($idAmigo)]);
    if ($amigo) {
        $result = $amigosCollection->insertOne([
            'idUsuario' => new MongoDB\BSON\ObjectId($idUsuario),
            'emailUsuario' => $_SESSION['email'],
            'idAmigo' => new MongoDB\BSON\ObjectId($idAmigo),
            'emailAmigo' => $amigo['correo']
        ]);
        if ($result->getInsertedCount() > 0) {
            return true; 
        } else {
            return "Error al agregar amigo"; 
        }
    } else {
        return "Amigo no encontrado"; 
    }
}

function eliminarAmigo($idAmigo, $idUsuario, $amigosCollection)
{
    $result = $amigosCollection->deleteOne([
        '$or' => [
            ['idUsuario' => new MongoDB\BSON\ObjectId($idUsuario), 'idAmigo' => new MongoDB\BSON\ObjectId($idAmigo)],
            ['idAmigo' => new MongoDB\BSON\ObjectId($idUsuario), 'idUsuario' => new MongoDB\BSON\ObjectId($idAmigo)]
        ]
    ]);
    if ($result->getDeletedCount() > 0) {
        return true; 
    } else {
        return "Error al eliminar amigo"; 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amigos - Codebook</title>
  <link rel="stylesheet" href="./estilos_css/stile.css">
  <link rel="stylesheet" href="./estilos_css/amigos.css">
  <style>
    .btn-eliminar {
      background-color: #ff0000;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-eliminar:hover {
      background-color: #cc0000;
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

  <?php if (isset($error)): ?>
    <div class="container">
        <p class="error-message"><?php echo $error; ?></p>
    </div>
  <?php endif; ?>

  <?php if (isset($usuarioActual)): ?>
    <div class="container">
      <section class="post-form">
        <h2>Buscar Usuario</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <input type="text" name="email" placeholder="Correo electrónico" class="input-search">
          <button type="submit" name="buscar" class="btn-search">Buscar</button>
        </form>
        <?php if (isset($errorBusqueda)): ?>
          <p class="error-message"><?php echo $errorBusqueda; ?></p>
        <?php endif; ?>
        <?php if (isset($usuarioBuscado)): ?>
          <?php if ($usuarioBuscado): ?>
            <p>Usuario encontrado: <?php echo $usuarioBuscado['nombre']; ?></p>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="add-friend-form">
              <input type="hidden" name="agregarAmigo" value="<?php echo $usuarioBuscado['_id']; ?>">
              <button type="submit" class="btn-add">Añadir amigo</button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    </div>

    <div class="container">
      <h2>Lista de Amigos de <?php echo $usuarioActual['nombre']; ?></h2>
      <ul class="user-list">
        <?php foreach ($amigos as $amigo): ?>
          <li class="amigo">
            <?php echo $amigo['nombre']; ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="remove-friend-form">
              <input type="hidden" name="eliminarAmigo" value="<?php echo $amigo['_id']; ?>">
              <button type="submit" class="btn-eliminar">Eliminar amigo</button>
            </form>
            <a href="post-amigos.php?email=<?php echo $amigo['correo']; ?>" class="btn-ver-post">Ver Post</a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

  <?php else: ?>
    <div class="container">
        <p class="error-message">No se ha iniciado sesión.</p>
    </div>
  <?php endif; ?>

  <footer>
    <div class="container">
      <p>&copy; 2024 Codebook. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
