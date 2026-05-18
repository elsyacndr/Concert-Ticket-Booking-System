<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$userId = $_SESSION['user_id'];
$bookingsStmt = $conn->prepare('SELECT b.*, e.name AS event_name, e.event_date FROM bookings b JOIN events e ON b.event_id = e.id WHERE b.user_id = ? ORDER BY b.booking_date DESC');
$bookingsStmt->bind_param('i', $userId);
$bookingsStmt->execute();
$bookings = $bookingsStmt->get_result();
$pageTitle = 'Riwayat Booking';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h2 class="mb-1">Riwayat Booking</h2>
            <p class="text-soft mb-0">Lihat status pembayaran dan unggah bukti pembayaran jika diperlukan.</p>
        </div>
        <a href="<?= getBaseUrl() ?>/customer/events.php" class="btn btn-primary">Lanjutkan Pesan</a>
    </div>
    <div class="card card-glass p-3">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Event</th>
                        <th>Tiket</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr><td colspan="6" class="text-center text-soft">Belum ada riwayat booking.</td></tr>
                    <?php endif; ?>
                    <?php while ($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><a href="<?= getBaseUrl() ?>/customer/invoice.php?id=<?= $booking['id'] ?>" class="fw-600" style="color:#c084fc;"><?= htmlspecialchars($booking['booking_code']) ?></a></td>
                            <td><?= htmlspecialchars($booking['event_name']) ?></td>
                            <td><?= htmlspecialchars($booking['total_ticket']) ?></td>
                            <td><?= formatRupiah($booking['total_price']) ?></td>
                            <td><span class="badge badge-status badge-<?= htmlspecialchars($booking['status']) ?> text-uppercase small"><?= htmlspecialchars($booking['status']) ?></span></td>
                            <td class="d-flex gap-1 flex-wrap">
                                <a href="<?= getBaseUrl() ?>/customer/invoice.php?id=<?= $booking['id'] ?>" class="btn btn-sm btn-outline-light">
                                    <i class="bi bi-receipt me-1"></i>Invoice
                                </a>
                                <?php if ($booking['status'] === 'completed'): ?>
                                <a href="<?= getBaseUrl() ?>/customer/ticket.php?id=<?= $booking['id'] ?>" class="btn btn-sm btn-success" target="_blank">
                                    <i class="bi bi-ticket-perforated me-1"></i>Tiket
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>