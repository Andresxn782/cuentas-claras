<?php
// Configuración segura de la cookie de sesión
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Escapa un texto para mostrarlo en HTML de forma segura (evita XSS)
function escapar($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Devuelve true si hay un usuario con la sesión iniciada
function usuario_logueado() {
    return isset($_SESSION['usuario_id']);
}

// Si no hay sesión iniciada, manda al usuario al login
function requerir_login() {
    if (!usuario_logueado()) {
        header('Location: login.php');
        exit;
    }
}
