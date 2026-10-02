<?php
require_once '../includes/funciones.php';

// Solo se puede cerrar sesión con el formulario (POST) y con token válido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
comprobar_csrf();

// 1. Vaciar los datos de la sesión
$_SESSION = [];

// 2. Borrar la cookie de sesión del navegador
setcookie(session_name(), '', time() - 3600, '/');

// 3. Destruir la sesión en el servidor
session_destroy();

header('Location: login.php?salir=1');
exit;
