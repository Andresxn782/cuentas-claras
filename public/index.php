<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

$titulo = 'Panel';
require '../includes/header.php';

// PRUEBA DE XSS (borra estas líneas cuando lo hayas visto)
$textoPeligroso = '<script>alert("Te he hackeado")</script>';
?>

<h1>Bienvenido a Cuentas Claras</h1>
<p>Aquí irá el resumen del mes.</p>

<?php require '../includes/footer.php'; ?>
