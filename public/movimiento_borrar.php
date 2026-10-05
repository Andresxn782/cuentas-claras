<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

// Solo se puede borrar con el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: movimientos.php');
    exit;
}

comprobar_csrf();

$id = (int) ($_POST['id'] ?? 0);

// Borramos solo si el movimiento es del usuario de la sesión
$consulta = $pdo->prepare('DELETE FROM movimientos WHERE id = ? AND usuario_id = ?');
$consulta->execute([$id, $_SESSION['usuario_id']]);

if ($consulta->rowCount() > 0) {
    header('Location: movimientos.php?borrado=1');
} else {
    header('Location: movimientos.php');
}
exit;
