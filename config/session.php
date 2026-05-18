<?php
/**
 * Session Configuration & Helper Functions
 * Concert Ticket Booking System
 * 
 * File ini mengelola session dan fungsi-fungsi helper
 * untuk autentikasi dan otorisasi user
 */

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Fungsi Cek Login
// ============================================================

/**
 * Cek apakah user sudah login
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Cek apakah user adalah customer
 * @return bool
 */
function isCustomer() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

// ============================================================
// Middleware / Guard Functions
// ============================================================

/**
 * Paksa user untuk login - redirect ke halaman login jika belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . getBaseUrl() . '/auth/login.php');
        exit();
    }
}

/**
 * Paksa user harus admin - redirect jika bukan admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . getBaseUrl() . '/customer/dashboard.php');
        exit();
    }
}

/**
 * Paksa user harus customer - redirect jika bukan customer
 */
function requireCustomer() {
    requireLogin();
    if (!isCustomer()) {
        header('Location: ' . getBaseUrl() . '/admin/dashboard.php');
        exit();
    }
}

/**
 * Redirect jika sudah login (untuk halaman login/register)
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: ' . getBaseUrl() . '/admin/dashboard.php');
        } else {
            header('Location: ' . getBaseUrl() . '/customer/dashboard.php');
        }
        exit();
    }
}

// ============================================================
// Flash Message Functions
// ============================================================

/**
 * Set flash message untuk ditampilkan sekali
 * @param string $type  - success, danger, warning, info
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash_type']    = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Tampilkan flash message dan hapus dari session
 * @return string HTML alert
 */
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $type    = $_SESSION['flash_type']    ?? 'info';
        $message = $_SESSION['flash_message'] ?? '';
        
        // Hapus flash message dari session
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        
        // Icon berdasarkan tipe
        $icons = [
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-x-circle-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'info'    => 'bi-info-circle-fill',
        ];
        $icon = $icons[$type] ?? 'bi-info-circle-fill';
        
        return '<div class="alert alert-' . $type . ' alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi ' . $icon . ' me-2"></i>
            <div>' . htmlspecialchars($message) . '</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    return '';
}

// ============================================================
// Helper Functions
// ============================================================

/**
 * Dapatkan base URL aplikasi
 * @return string
 */
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Deteksi subfolder otomatis
    $script   = $_SERVER['SCRIPT_NAME'] ?? '';
    $parts    = explode('/', $script);
    // Ambil folder utama project
    $base     = '/' . ($parts[1] ?? '');
    return $protocol . '://' . $host . $base;
}

/**
 * Format harga ke format Rupiah
 * @param float $price
 * @return string
 */
function formatRupiah($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 * @param string $date
 * @return string
 */
function formatDate($date) {
    $bulan = [
        '01' => 'Januari',   '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',     '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',      '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',   '11' => 'November',  '12' => 'Desember',
    ];
    if (empty($date)) return '-';
    $d = date('d', strtotime($date));
    $m = date('m', strtotime($date));
    $y = date('Y', strtotime($date));
    return $d . ' ' . ($bulan[$m] ?? $m) . ' ' . $y;
}

/**
 * Generate kode booking unik
 * @return string
 */
function generateBookingCode() {
    $year   = date('Y');
    $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    return 'BK-' . $year . '-' . $random;
}

/**
 * Sanitasi input untuk mencegah XSS
 * @param string $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Dapatkan badge HTML berdasarkan status event
 * @param string $status
 * @return string HTML badge
 */
function getEventStatusBadge($status) {
    $badges = [
        'upcoming'  => '<span class="badge bg-primary"><i class="bi bi-clock me-1"></i>Upcoming</span>',
        'ongoing'   => '<span class="badge bg-success"><i class="bi bi-play-circle me-1"></i>Ongoing</span>',
        'finished'  => '<span class="badge bg-secondary"><i class="bi bi-check-circle me-1"></i>Finished</span>',
        'cancelled' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Cancelled</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Dapatkan badge HTML berdasarkan status booking
 * @param string $status
 * @return string HTML badge
 */
function getBookingStatusBadge($status) {
    $badges = [
        'pending'   => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>',
        'paid'      => '<span class="badge bg-info text-dark"><i class="bi bi-credit-card me-1"></i>Paid</span>',
        'rejected'  => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>',
        'completed' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Upload file dengan validasi
 * @param array  $file      - $_FILES['field']
 * @param string $uploadDir - direktori tujuan
 * @param array  $allowedTypes - tipe file yang diizinkan
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadFile($file, $uploadDir, $allowedTypes = ['jpg','jpeg','png','gif']) {
    // Cek apakah ada file yang diupload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Tidak ada file yang diupload atau terjadi error.'];
    }
    
    // Cek ukuran file (max 5MB)
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file terlalu besar. Maksimal 5MB.'];
    }
    
    // Cek ekstensi file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', $allowedTypes)];
    }
    
    // Generate nama file unik
    $newFilename = uniqid('img_', true) . '.' . $ext;
    $destination = $uploadDir . $newFilename;
    
    // Pindahkan file ke direktori tujuan
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Gagal menyimpan file. Cek permission folder.'];
    }
    
    return ['success' => true, 'filename' => $newFilename];
}

/**
 * Dapatkan data user yang sedang login
 * @return array
 */
function getCurrentUser() {
    return [
        'id'    => $_SESSION['user_id']    ?? null,
        'name'  => $_SESSION['user_name']  ?? 'Guest',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role']  ?? 'customer',
        'photo' => $_SESSION['user_photo'] ?? 'default.png',
    ];
}
