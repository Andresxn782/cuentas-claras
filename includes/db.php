<?php
require_once '../config/config.php';

try {
    // Conectamos con la base de datos
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);

    // 1. Si una consulta falla, que PHP avise con un error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Los resultados llegan como $fila['nombre']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 3. Las consultas preparadas las hace MySQL de verdad (más seguro)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    if (MODO_DEBUG) {
        die('Error de conexión: ' . $e->getMessage());
    } else {
        die('No se puede conectar con la base de datos. Inténtalo más tarde.');
    }
}
