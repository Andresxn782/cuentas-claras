<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

requerir_login();

// Los mismos filtros que en el listado
$mes = $_GET['mes'] ?? '';
if ($mes !== '' && !mes_valido($mes)) {
    $mes = '';
}
$categoria_id = (int) ($_GET['categoria'] ?? 0);

$movimientos = obtener_movimientos($pdo, $_SESSION['usuario_id'], $mes, $categoria_id);

// Nombre del archivo: "movimientos.csv" o "movimientos-2026-10.csv"
$nombre_archivo = 'movimientos';
if ($mes !== '') {
    $nombre_archivo .= '-' . $mes;
}
$nombre_archivo .= '.csv';

// Le decimos al navegador que esto es un archivo CSV para descargar
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');

// Escribimos directamente en la respuesta que se envía al navegador
$salida = fopen('php://output', 'w');

// Marca para que Excel reconozca las tildes y las eñes (UTF-8)
fwrite($salida, "\xEF\xBB\xBF");

// Fila de títulos
fputcsv($salida, ['Fecha', 'Tipo', 'Categoría', 'Descripción', 'Importe'], ';', '"', '');

// Una fila por movimiento
foreach ($movimientos as $movimiento) {
    if ($movimiento['tipo'] === 'ingreso') {
        $tipo = 'Ingreso';
        $importe = number_format($movimiento['importe'], 2, ',', '');
    } else {
        $tipo = 'Gasto';
        $importe = '-' . number_format($movimiento['importe'], 2, ',', '');
    }

    fputcsv($salida, [
        formatear_fecha($movimiento['fecha']),
        $tipo,
        limpiar_csv($movimiento['categoria']),
        limpiar_csv($movimiento['descripcion']),
        $importe,
    ], ';', '"', '');
}

fclose($salida);
exit;
