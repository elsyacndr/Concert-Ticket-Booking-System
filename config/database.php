<?php
/**
 * Database Configuration
 * Concert Ticket Booking System
 * 
 * File ini berisi konfigurasi koneksi database MySQL
 * menggunakan MySQLi dengan prepared statements
 */

// ============================================================
// Konfigurasi Database
// ============================================================
define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_NAME',     'concert_ticket_booking');
define('DB_CHARSET',  'utf8mb4');

// ============================================================
// Konfigurasi Aplikasi
// ============================================================
define('APP_NAME',    'VibeTicket');
define('APP_TAGLINE', 'Your Gateway to Unforgettable Concerts');
define('APP_URL',     'http://localhost/Concert%20Ticket%20Booking%20System');
define('APP_VERSION', '1.0.0');

// ============================================================
// Konfigurasi Upload
// ============================================================
define('UPLOAD_POSTER',  '../assets/images/posters/');
define('UPLOAD_PAYMENT', '../assets/images/payments/');
define('UPLOAD_AVATAR',  '../assets/images/avatars/');
define('MAX_FILE_SIZE',  5 * 1024 * 1024); // 5MB

// ============================================================
// Fungsi Koneksi Database
// ============================================================
function getConnection() {
    // Buat koneksi baru ke MySQL
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Cek apakah koneksi berhasil
    if ($conn->connect_error) {
        die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;margin:20px;border-radius:8px;">
            <h3 style="color:#c00;">❌ Database Connection Failed</h3>
            <p>Error: ' . htmlspecialchars($conn->connect_error) . '</p>
            <p>Pastikan XAMPP MySQL sudah berjalan dan database <strong>concert_ticket_booking</strong> sudah dibuat.</p>
        </div>');
    }
    
    // Set charset ke utf8mb4 untuk mendukung karakter unicode
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

// Buat koneksi global yang bisa digunakan di seluruh aplikasi
$conn = getConnection();
