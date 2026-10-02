<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

// Si ya ha iniciado sesión, no tiene sentido mostrarle el login
if (usuario_logueado()) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  comprobar_csrf();
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Buscamos al usuario por su email
    $consulta = $pdo->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE email = ?');
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();

    // Comprobamos que existe y que la contraseña es correcta
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Email o contraseña incorrectos.';
    }
}

$titulo = 'Iniciar sesión';
require '../includes/header.php';
?>

<div class="tarjeta">
    <h1>Iniciar sesión</h1>

    <?php if (isset($_GET['registrado'])): ?>
        <div class="alerta alerta-exito">Cuenta creada correctamente. Ya puedes iniciar sesión.</div>
    <?php endif; ?>

    <?php if (isset($_GET['salir'])): ?>
        <div class="alerta alerta-exito">Has cerrado sesión correctamente.</div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alerta alerta-error"><?php echo escapar($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" class="formulario">
      <?php echo campo_csrf(); ?>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?php echo escapar($email); ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="boton">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
</div>

<?php require '../includes/footer.php'; ?>
