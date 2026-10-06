<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

// Rango de fechas del mes actual: desde el día 1 hasta el día 1 del mes siguiente
$inicio_mes = date('Y-m-01');
$inicio_mes_siguiente = date('Y-m-01', strtotime($inicio_mes . ' +1 month'));

// 1. Total de ingresos y de gastos del mes
$consulta = $pdo->prepare(
    'SELECT c.tipo, SUM(m.importe) AS total
     FROM movimientos m
     JOIN categorias c ON m.categoria_id = c.id
     WHERE m.usuario_id = ? AND m.fecha >= ? AND m.fecha < ?
     GROUP BY c.tipo'
);
$consulta->execute([$_SESSION['usuario_id'], $inicio_mes, $inicio_mes_siguiente]);

$ingresos = 0;
$gastos = 0;

foreach ($consulta->fetchAll() as $fila) {
    if ($fila['tipo'] === 'ingreso') {
        $ingresos = (float) $fila['total'];
    } else {
        $gastos = (float) $fila['total'];
    }
}

$ahorro = $ingresos - $gastos;

// 2. Gastos del mes agrupados por categoría, de mayor a menor
$consulta = $pdo->prepare(
    'SELECT c.nombre, SUM(m.importe) AS total
     FROM movimientos m
     JOIN categorias c ON m.categoria_id = c.id
     WHERE m.usuario_id = ? AND c.tipo = \'gasto\' AND m.fecha >= ? AND m.fecha < ?
     GROUP BY c.id, c.nombre
     ORDER BY total DESC'
);
$consulta->execute([$_SESSION['usuario_id'], $inicio_mes, $inicio_mes_siguiente]);
$gastos_por_categoria = $consulta->fetchAll();

$titulo = 'Panel';
require '../includes/header.php';
?>

<div class="cabecera-seccion">
    <h1>Resumen de <?php echo formatear_mes($inicio_mes); ?></h1>
</div>

<div class="resumen">
    <div class="resumen-tarjeta">
        <p class="resumen-etiqueta">Ingresos</p>
        <p class="resumen-cifra importe-ingreso"><?php echo formatear_euros($ingresos); ?></p>
    </div>

    <div class="resumen-tarjeta">
        <p class="resumen-etiqueta">Gastos</p>
        <p class="resumen-cifra importe-gasto"><?php echo formatear_euros($gastos); ?></p>
    </div>

    <div class="resumen-tarjeta">
        <p class="resumen-etiqueta">Ahorro</p>
        <?php if ($ahorro >= 0): ?>
            <p class="resumen-cifra importe-ingreso"><?php echo formatear_euros($ahorro); ?></p>
        <?php else: ?>
            <p class="resumen-cifra importe-gasto"><?php echo formatear_euros($ahorro); ?></p>
        <?php endif; ?>
    </div>
</div>

<h2>Gastos por categoría</h2>

<?php if (empty($gastos_por_categoria)): ?>
    <p>No hay gastos este mes.</p>
<?php else: ?>
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="importe">Total</th>
                    <th class="importe">% del gasto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos_por_categoria as $fila): ?>
                    <tr>
                        <td><?php echo escapar($fila['nombre']); ?></td>
                        <td class="importe"><?php echo formatear_euros($fila['total']); ?></td>
                        <td class="importe"><?php echo round($fila['total'] / $gastos * 100); ?> %</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<p><a href="movimientos.php">Ver todos los movimientos</a></p>

<?php require '../includes/footer.php'; ?>
