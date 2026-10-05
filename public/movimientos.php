<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

// Solo los movimientos del usuario de la sesión, con el nombre y el tipo de su categoría
$consulta = $pdo->prepare(
    'SELECT m.id, m.fecha, m.importe, m.descripcion, c.nombre AS categoria, c.tipo
     FROM movimientos m
     JOIN categorias c ON m.categoria_id = c.id
     WHERE m.usuario_id = ?
     ORDER BY m.fecha DESC, m.id DESC'
);
$consulta->execute([$_SESSION['usuario_id']]);
$movimientos = $consulta->fetchAll();

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

<?php if (empty($movimientos)): ?>
    <p>Todavía no tienes movimientos. ¡Añade el primero!</p>
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

                        <td>
                            <a href="movimiento_form.php?id=<?php echo $movimiento['id']; ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>
