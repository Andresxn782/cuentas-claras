<?php
// Escapa un texto para mostrarlo en HTML de forma segura (evita XSS)
function escapar($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
