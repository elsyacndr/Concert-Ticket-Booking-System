<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Set flash SEBELUM destroy session
setFlash('success', 'Anda telah logout. Sampai jumpa lagi!');

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

// Mulai session baru untuk menyimpan flash message
session_start();
setFlash('success', 'Anda telah logout. Sampai jumpa lagi!');

header('Location: ' . getBaseUrl() . '/auth/login.php');
exit();
