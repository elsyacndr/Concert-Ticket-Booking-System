<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$eventId = intval($_GET['id'] ?? 0);
if ($eventId <= 0) {
    header('Location: ' . getBaseUrl() . '/customer/events.php');
    exit();
}

$stmt = $conn->prepare('SELECT * FROM events WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
if (!$event) {
    header('Location: ' . getBaseUrl() . '/customer/events.php');
    exit();
}

$error = '';
$success = '';
$available = max(0, $event['quota'] - $event['sold']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $totalTicket = intval($_POST['total_ticket'] ?? 0);

    if ($totalTicket <= 0) {
        $error = 'Jumlah tiket harus lebih dari nol.';
    } elseif ($totalTicket > $available) {
        $error = 'Jumlah tiket melebihi sisa kuota yang tersedia.';
    } elseif (in_array($event['status'], ['finished','cancelled'])) {
        $error = 'Event tidak dapat dipesan karena sudah selesai atau dibatalkan.';
    } else {
        $bookingCode = generateBookingCode();
        $totalPrice = $totalTicket * $event['price'];
        $userId = $_SESSION['user_id'];

        $insert = $conn->prepare('INSERT INTO bookings (user_id, event_id, booking_code, total_ticket, total_price) VALUES (?, ?, ?, ?, ?)');
        $insert->bind_param('iisid', $userId, $eventId, $bookingCode, $totalTicket, $totalPrice);
        if ($insert->execute()) {
            $newBookingId = $conn->insert_id;
            $updSold = $conn->prepare('UPDATE events SET sold = sold + ? WHERE id = ?');
            $updSold->bind_param('ii', $totalTicket, $eventId);
            $updSold->execute();
            setFlash('success', 'Booking berhasil! Silakan selesaikan pembayaran.');
            header('Location: ' . getBaseUrl() . '/customer/invoice.php?id=' . $newBookingId);
            exit();
        }
        $error = 'Gagal membuat booking. Silakan coba lagi.';
    }
}

$pageTitle = 'Detail Event';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-glass p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5">
                        <img src="<?= getBaseUrl() ?>/assets/images/posters/<?= htmlspecialchars($event['poster'] ?: 'default_poster.jpg') ?>" alt="Poster <?= htmlspecialchars($event['name']) ?>" class="event-poster" data-fallback="poster-<?= $event['id'] ?>">
                        <div id="poster-<?= $event['id'] ?>" class="placeholder-box" style="display:none;"><i class="bi bi-image"></i> Poster tidak tersedia</div>
                    </div>
                    <div class="col-md-7">
                        <h2><?= htmlspecialchars($event['name']) ?></h2>
                        <p class="text-soft mb-2">Artis: <?= htmlspecialchars($event['artist']) ?></p>
                        <p class="text-soft mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($event['location']) ?></p>
                        <p class="text-soft mb-2"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($event['event_date'])) ?> • <?= date('H:i', strtotime($event['event_time'])) ?></p>
                        <p class="text-soft mb-2"><i class="bi bi-ticket-perforated me-1"></i>Harga: <?= formatRupiah($event['price']) ?></p>
                        <p class="text-soft mb-2"><i class="bi bi-people me-1"></i>Sisa tiket: <?= $available ?></p>
                        <span class="badge badge-status badge-<?= htmlspecialchars($event['status']) ?> text-uppercase"><?= htmlspecialchars($event['status']) ?></span>
                    </div>
                </div>
                <hr class="my-4 border-secondary">
                <h5>Deskripsi Event</h5>
                <p class="text-soft"><?= nl2br(htmlspecialchars($event['description'] ?: '-')) ?></p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-glass p-4">
                <h5 class="mb-3">Pesan Tiket</h5>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Tiket</label>
                        <input type="number" name="total_ticket" class="form-control" min="1" max="<?= $available ?>" value="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" <?= $available === 0 ? 'disabled' : '' ?>>Pesan Sekarang</button>
                </form>
                <?php if ($available === 0): ?>
                    <div class="text-warning mt-3">Tiket sudah habis atau event tidak tersedia.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>