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
// Devuelve el token CSRF de la sesión (lo crea si no existe)
function token_csrf() {
    if (empty($_SESSION['token_csrf'])) {
        $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token_csrf'];
}

// Devuelve el campo oculto con el token, para ponerlo dentro de cada formulario
function campo_csrf() {
    return '<input type="hidden" name="token_csrf" value="' . token_csrf() . '">';
}

// Comprueba que el token enviado coincide con el de la sesión. Si no, para todo.
function comprobar_csrf() {
    $token = $_POST['token_csrf'] ?? '';

    if (!hash_equals(token_csrf(), $token)) {
        http_response_code(403);
        die('La solicitud no es válida. Recarga la página e inténtalo de nuevo.');
    }
}
