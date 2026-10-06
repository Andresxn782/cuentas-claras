<?php
// Copia este archivo como config.php y rellena tus datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'cuentas_claras');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// true mientras desarrollas; false cuando la subas al hosting
define('MODO_DEBUG', false);

// Mostrar los errores de PHP solo en modo desarrollo
if (MODO_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
