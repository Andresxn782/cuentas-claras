<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

// NUEVO: leer y validar los filtros de la URL
$mes = $_GET['mes'] ?? '';
if ($mes !== '' && !mes_valido($mes)) {
    $mes = '';
}
$categoria_id = (int) ($_GET['categoria'] ?? 0);

// NUEVO: construimos la consulta según los filtros elegidos
$sql = 'SELECT m.id, m.fecha, m.importe, m.descripcion, c.nombre AS categoria, c.tipo
        FROM movimientos m
        JOIN categorias c ON m.categoria_id = c.id
        WHERE m.usuario_id = ?';
$parametros = [$_SESSION['usuario_id']];

if ($mes !== '') {
    [$inicio_mes, $inicio_mes_siguiente] = rango_mes($mes);
    $sql .= ' AND m.fecha >= ? AND m.fecha < ?';
    $parametros[] = $inicio_mes;
    $parametros[] = $inicio_mes_siguiente;
}

if ($categoria_id > 0) {
    $sql .= ' AND m.categoria_id = ?';
    $parametros[] = $categoria_id;
}

$sql .= ' ORDER BY m.fecha DESC, m.id DESC';

$consulta = $pdo->prepare($sql);
$consulta->execute($parametros);
$movimientos = $consulta->fetchAll();

// NUEVO: datos para los desplegables de los filtros
$categorias = $pdo->query('SELECT id, nombre, tipo FROM categorias ORDER BY nombre')->fetchAll();

$meses_disponibles = [];
for ($i = 0; $i < 12; $i++) {
    $meses_disponibles[] = date('Y-m', strtotime(date('Y-m-01') . " -$i month"));
}

$hay_filtros = ($mes !== '' || $categoria_id > 0);

$titulo = 'Movimientos';
require '../includes/header.php';
?>

<div class="cabecera-seccion">
    <h1>Movimientos</h1>
    <a href="movimiento_form.php" class="boton">+ Nuevo movimiento</a>
</div>

<?php if (isset($_GET['creado'])): ?>
    <div class="alerta alerta-exito">Movimiento creado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET['editado'])): ?>
    <div class="alerta alerta-exito">Movimiento actualizado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET['borrado'])): ?>
    <div class="alerta alerta-exito">Movimiento borrado correctamente.</div>
<?php endif; ?>

<!-- NUEVO: formulario de filtros -->
<form method="get" action="movimientos.php" class="filtros">
    <div>
        <label for="mes">Mes</label>
        <select id="mes" name="mes">
            <option value="">Todos los meses</option>
            <?php foreach ($meses_disponibles as $opcion_mes): ?>
                <option value="<?php echo $opcion_mes; ?>"
                    <?php if ($opcion_mes === $mes) echo 'selected'; ?>>
                    <?php echo formatear_mes($opcion_mes . '-01'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria">
            <option value="0">Todas las categorías</option>

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
    </div>

    <div class="filtros-botones">
        <button type="submit" class="boton">Filtrar</button>
        <?php if ($hay_filtros): ?>
            <a href="movimientos.php" class="boton boton-secundario">Quitar filtros</a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($movimientos)): ?>
    <!-- NUEVO: mensaje distinto si no hay resultados por culpa de los filtros -->
    <?php if ($hay_filtros): ?>
        <p>No hay movimientos con estos filtros.</p>
    <?php else: ?>
        <p>Todavía no tienes movimientos. ¡Añade el primero!</p>
    <?php endif; ?>
<?php else: ?>
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th class="importe">Importe</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?php echo formatear_fecha($movimiento['fecha']); ?></td>
                        <td><?php echo escapar($movimiento['categoria']); ?></td>
                        <td><?php echo escapar($movimiento['descripcion']); ?></td>

                        <?php if ($movimiento['tipo'] === 'ingreso'): ?>
                            <td class="importe importe-ingreso">+<?php echo formatear_euros($movimiento['importe']); ?></td>
                        <?php else: ?>
                            <td class="importe importe-gasto">-<?php echo formatear_euros($movimiento['importe']); ?></td>
                        <?php endif; ?>

                        <td class="acciones">
                            <a href="movimiento_form.php?id=<?php echo $movimiento['id']; ?>">Editar</a>

                            <form method="post" action="movimiento_borrar.php" class="form-borrar">
                                <?php echo campo_csrf(); ?>
                                <input type="hidden" name="id" value="<?php echo $movimiento['id']; ?>">
                                <button type="submit" class="boton-borrar">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<dialog id="dialogo-borrar" class="dialogo">
    <h2>¿Borrar movimiento?</h2>
    <p>Esta acción no se puede deshacer.</p>
    <div class="dialogo-botones">
        <button type="button" id="cancelar-borrar" class="boton boton-secundario">Cancelar</button>
        <button type="button" id="confirmar-borrar" class="boton boton-peligro">Sí, borrar</button>
    </div>
</dialog>

<script src="js/confirmar-borrado.js"></script>

<?php require '../includes/footer.php'; ?>
