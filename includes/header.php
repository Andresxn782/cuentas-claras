<?php
// Nombre del archivo que se está viendo (por ejemplo, "movimientos.php"), para marcarlo en el menú
$pagina_actual = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cuentas Claras: aplicación web para controlar tus ingresos y gastos personales.">
    <title><?php echo escapar($titulo); ?> | Cuentas Claras</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💰</text></svg>">
    <link rel="stylesheet" href="css/estilos.css?v=4">
</head>
<body>
    <header class="cabecera">
        <a href="index.php" class="logo">Cuentas Claras</a>
        <nav class="menu">
            <?php if (usuario_logueado()): ?>
                <span class="saludo">Hola, <?php echo escapar($_SESSION['usuario_nombre']); ?></span>

                <a href="index.php"
                   class="<?php if ($pagina_actual === 'index.php') echo 'activo'; ?>">Panel</a>

                <a href="movimientos.php"
                   class="<?php if (in_array($pagina_actual, ['movimientos.php', 'movimiento_form.php'])) echo 'activo'; ?>">Movimientos</a>

                <form method="post" action="logout.php" class="form-logout">
                    <?php echo campo_csrf(); ?>
                    <button type="submit" class="boton-enlace">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <a href="login.php"
                   class="<?php if ($pagina_actual === 'login.php') echo 'activo'; ?>">Iniciar sesión</a>

                <a href="registro.php"
                   class="<?php if ($pagina_actual === 'registro.php') echo 'activo'; ?>">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="contenedor">
