<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !commar_admin_verify_csrf_token()) {
    http_response_code(405);
    exit('Solicitud inválida.');
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
