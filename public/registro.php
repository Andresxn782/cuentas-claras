<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

// Si ya ha iniciado sesión, no tiene sentido que se registre
if (usuario_logueado()) {
    header('Location: index.php');
    exit;
}

$errores = [];
$nombre = '';
$email = '';

// Solo procesamos los datos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    comprobar_csrf();

    // 1. Recoger los datos
    $nombre    = trim($_POST['nombre'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // 2. Validar
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre no puede tener más de 100 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido.';
    }

    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // 3. Comprobar que el email no está registrado
    if (empty($errores)) {
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $errores[] = 'Ya existe una cuenta con ese email.';
        }
    }

    // 4. Si todo está bien, guardar el usuario
    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $consulta = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)');
        $consulta->execute([$nombre, $email, $hash]);

        header('Location: login.php?registrado=1');
        exit;
    }
}

$titulo = 'Crear cuenta';
require '../includes/header.php';
?>

<div class="tarjeta">
    <h1>Crear cuenta</h1>

    <?php if (!empty($errores)): ?>
        <div class="alerta alerta-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo escapar($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="registro.php" class="formulario">
         <?php echo campo_csrf(); ?>
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?php echo escapar($nombre); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required
               value="<?php echo escapar($email); ?>">

        <label for="password">Contraseña (mínimo 8 caracteres)</label>
        <input type="password" id="password" name="password" minlength="8" required>

        <label for="password2">Repite la contraseña</label>
        <input type="password" id="password2" name="password2" minlength="8" required>

        <button type="submit" class="boton">Crear cuenta</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<?php require '../includes/footer.php'; ?>
