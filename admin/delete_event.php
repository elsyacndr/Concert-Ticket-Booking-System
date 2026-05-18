<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = intval($_POST['id'] ?? 0);
    if ($eventId > 0) {
        $stmt = $conn->prepare('DELETE FROM events WHERE id = ?');
        $stmt->bind_param('i', $eventId);
        if ($stmt->execute()) {
            setFlash('success', 'Event berhasil dihapus.');
        } else {
            setFlash('danger', 'Gagal menghapus event.');
        }
    } else {
        setFlash('danger', 'Data event tidak valid.');
    }
}

header('Location: ' . getBaseUrl() . '/admin/events.php');
exit();
