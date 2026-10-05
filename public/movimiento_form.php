<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

$errores = [];

// NUEVO: si llega un id por la URL, estamos editando
$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

// Valores por defecto del formulario
$fecha        = date('Y-m-d');
$categoria_id = 0;
$importe      = '';
$descripcion  = '';

// NUEVO: si estamos editando, cargamos el movimiento (solo si es del usuario)
if ($editando) {
    $consulta = $pdo->prepare(
        'SELECT fecha, categoria_id, importe, descripcion
         FROM movimientos
         WHERE id = ? AND usuario_id = ?'
    );
    $consulta->execute([$id, $_SESSION['usuario_id']]);
    $movimiento = $consulta->fetch();

    // No existe o es de otro usuario: lo mandamos al listado
    if (!$movimiento) {
        header('Location: movimientos.php');
        exit;
    }

    // Rellenamos el formulario con sus datos
    $fecha        = $movimiento['fecha'];
    $categoria_id = $movimiento['categoria_id'];
    $importe      = $movimiento['importe'];
    $descripcion  = $movimiento['descripcion'];
}

// Cargamos las categorías para el desplegable
$categorias = $pdo->query('SELECT id, nombre, tipo FROM categorias ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    comprobar_csrf();

    // 1. Recoger los datos
    $fecha        = trim($_POST['fecha'] ?? '');
    $categoria_id = (int) ($_POST['categoria_id'] ?? 0);
    $importe      = str_replace(',', '.', trim($_POST['importe'] ?? ''));
    $descripcion  = trim($_POST['descripcion'] ?? '');

    // 2. Validar la fecha
    $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fecha_valida || $fecha_valida->format('Y-m-d') !== $fecha) {
        $errores[] = 'La fecha no es válida.';
    }

    // 3. Validar que la categoría existe
    $consulta = $pdo->prepare('SELECT id FROM categorias WHERE id = ?');
    $consulta->execute([$categoria_id]);
    if (!$consulta->fetch()) {
        $errores[] = 'Elige una categoría válida.';
    }

    // 4. Validar el importe
    if (!is_numeric($importe) || $importe <= 0 || $importe > 99999999.99) {
        $errores[] = 'El importe debe ser un número mayor que 0.';
    } else {
        $importe = round($importe, 2);
    }

    // 5. Validar la descripción (opcional)
    if (mb_strlen($descripcion) > 255) {
        $errores[] = 'La descripción no puede tener más de 255 caracteres.';
    }

    // 6. Si todo está bien, guardar
    if (empty($errores)) {
        if ($editando) {
            // NUEVO: actualizar (otra vez comprobando que es del usuario)
            $consulta = $pdo->prepare(
                'UPDATE movimientos
                 SET categoria_id = ?, importe = ?, fecha = ?, descripcion = ?
                 WHERE id = ? AND usuario_id = ?'
            );
            $consulta->execute([$categoria_id, $importe, $fecha, $descripcion, $id, $_SESSION['usuario_id']]);

            header('Location: movimientos.php?editado=1');
        } else {
            $consulta = $pdo->prepare(
                'INSERT INTO movimientos (usuario_id, categoria_id, importe, fecha, descripcion)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $consulta->execute([$_SESSION['usuario_id'], $categoria_id, $importe, $fecha, $descripcion]);

            header('Location: movimientos.php?creado=1');
        }
        exit;
    }
}

// NUEVO: el título cambia según si creamos o editamos
if ($editando) {
    $titulo = 'Editar movimiento';
} else {
    $titulo = 'Nuevo movimiento';
}

require '../includes/header.php';
?>

<div class="tarjeta">
    <h1><?php echo escapar($titulo); ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alerta alerta-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo escapar($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- NUEVO: si editamos, el formulario se envía con el id en la URL -->
    <form method="post" action="movimiento_form.php<?php if ($editando) echo '?id=' . $id; ?>" class="formulario">
        <?php echo campo_csrf(); ?>

        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" required
               value="<?php echo escapar($fecha); ?>">

        <label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">-- Elige una categoría --</option>

            <optgroup label="Ingresos">
                <?php foreach ($categorias as $categoria): ?>
                    <?php if ($categoria['tipo'] === 'ingreso'): ?>
                        <option value="<?php echo $categoria['id']; ?>"
                            <?php if ($categoria['id'] == $categoria_id) echo 'selected'; ?>>
                            <?php echo escapar($categoria['nombre']); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </optgroup>

            <optgroup label="Gastos">
                <?php foreach ($categorias as $categoria): ?>
                    <?php if ($categoria['tipo'] === 'gasto'): ?>
                        <option value="<?php echo $categoria['id']; ?>"
                            <?php if ($categoria['id'] == $categoria_id) echo 'selected'; ?>>
                            <?php echo escapar($categoria['nombre']); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </optgroup>
        </select>

        <label for="importe">Importe (€)</label>
        <input type="number" id="importe" name="importe" step="0.01" min="0.01" required
               value="<?php echo escapar($importe); ?>">

        <label for="descripcion">Descripción (opcional)</label>
        <input type="text" id="descripcion" name="descripcion" maxlength="255"
               value="<?php echo escapar($descripcion); ?>">

        <button type="submit" class="boton">Guardar</button>
    </form>

    <p><a href="movimientos.php">Volver a movimientos</a></p>
</div>

<?php require '../includes/footer.php'; ?>
