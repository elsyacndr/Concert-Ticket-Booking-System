<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . getBaseUrl() . '/customer/bookings.php');
    exit();
}

$bookingId = intval($_POST['booking_id'] ?? 0);
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT * FROM bookings WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $bookingId, $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    setFlash('danger', 'Booking tidak ditemukan.');
    header('Location: ' . getBaseUrl() . '/customer/bookings.php');
    exit();
}

if (empty($_FILES['payment_proof']['name'])) {
    setFlash('danger', 'Silakan pilih berkas bukti pembayaran.');
    header('Location: ' . getBaseUrl() . '/customer/bookings.php');
    exit();
}

$uploadDir = __DIR__ . '/../assets/images/payments/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$fileName = time() . '_' . basename($_FILES['payment_proof']['name']);
$destination = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $destination)) {
    $update = $conn->prepare('UPDATE bookings SET payment_proof = ?, status = ? WHERE id = ?');
    $pending = 'pending';
    $update->bind_param('ssi', $fileName, $pending, $bookingId);
    if ($update->execute()) {
        setFlash('success', 'Bukti pembayaran berhasil diunggah. Tunggu konfirmasi dari admin.');
    } else {
        setFlash('danger', 'Gagal menyimpan bukti pembayaran.');
    }
} else {
    setFlash('danger', 'Terjadi kesalahan saat mengunggah berkas.');
}

header('Location: ' . getBaseUrl() . '/customer/bookings.php');
exit();
