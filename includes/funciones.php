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
// Convierte 1234.5 en "1.234,50 €"
function formatear_euros($cantidad) {
    return number_format((float) $cantidad, 2, ',', '.') . ' €';
}

// Convierte "2026-10-05" en "05/10/2026"
function formatear_fecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}
// Convierte "2026-10-01" en "octubre de 2026"
function formatear_mes($fecha) {
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];

    $marca = strtotime($fecha);
    $numero_mes = (int) date('n', $marca);

    return $meses[$numero_mes] . ' de ' . date('Y', $marca);
}
