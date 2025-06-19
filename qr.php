<?php
require_once __DIR__ . '/vendor/phpqrcode/qrlib.php';
header('Content-Type: image/png');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    QRcode::png('ID inválido', false, QR_ECLEVEL_L, 8, 2);
    exit;
}

$url = "http://localhost/perfilUsuario/show/$id";
QRcode::png($url, false, QR_ECLEVEL_L, 8, 2);



