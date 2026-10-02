<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo escapar($titulo); ?> | Cuentas Claras</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="cabecera">
        <a href="index.php" class="logo">Cuentas Claras</a>
                <nav class="menu">
            <?php if (usuario_logueado()): ?>
                <span class="saludo">Hola, <?php echo escapar($_SESSION['usuario_nombre']); ?></span>
                <a href="index.php">Panel</a>
                <a href="movimientos.php">Movimientos</a>
                <form method="post" action="logout.php" class="form-logout">
                    <?php echo campo_csrf(); ?>
                    <button type="submit" class="boton-enlace">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="contenedor">
