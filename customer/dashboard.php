<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$userId = $_SESSION['user_id'];

$summaryStmt = $conn->prepare('SELECT COUNT(*) AS total_bookings, SUM(total_price) AS total_spent FROM bookings WHERE user_id = ?');
$summaryStmt->bind_param('i', $userId);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();

$recentEvents = $conn->query('SELECT * FROM events WHERE status IN ("upcoming","ongoing") ORDER BY event_date ASC LIMIT 4');
$recentBookingsStmt = $conn->prepare('SELECT b.*, e.name AS event_name, e.event_date FROM bookings b JOIN events e ON b.event_id = e.id WHERE b.user_id = ? ORDER BY b.booking_date DESC LIMIT 5');
$recentBookingsStmt->bind_param('i', $userId);
$recentBookingsStmt->execute();
$recentBookings = $recentBookingsStmt->get_result();

$pageTitle = 'Dashboard Customer';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="mb-4">
        <h2 class="mb-1">Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        <p class="text-soft mb-0">Selamat datang di ruang customer. Temukan konser dan lihat riwayat bookingmu.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-glass p-3 h-100">
                <small class="text-soft">Total Booking</small>
                <h3><?= $summary['total_bookings'] ?: 0 ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-glass p-3 h-100">
                <small class="text-soft">Total Pengeluaran</small>
                <h3><?= formatRupiah($summary['total_spent'] ?: 0) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-glass p-3 h-100">
                <small class="text-soft">Event Aktif</small>
                <h3><?= $conn->query('SELECT COUNT(*) FROM events WHERE status IN ("upcoming","ongoing")')->fetch_row()[0] ?: 0 ?></h3>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-7">
            <div class="card card-glass p-4 mb-4">
                <h5 class="mb-3">Event Mendatang</h5>
                <div class="row g-3">
                    <?php if ($recentEvents->num_rows === 0): ?>
                        <div class="col-12 text-soft">Tidak ada event mendatang saat ini.</div>
                    <?php endif; ?>
                    <?php while ($event = $recentEvents->fetch_assoc()): ?>
                        <div class="col-md-6">
                            <div class="card card-glass border-0 h-100">
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($event['name']) ?></h6>
                                    <p class="text-soft small mb-2"><?= htmlspecialchars($event['artist']) ?></p>
                                    <p class="text-soft small mb-2"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($event['event_date'])) ?></p>
                                    <a href="<?= getBaseUrl() ?>/customer/event_detail.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-glass p-4">
                <h5 class="mb-3">Riwayat Booking Terbaru</h5>
                <?php if ($recentBookings->num_rows === 0): ?>
                    <p class="text-soft">Belum ada riwayat booking.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($booking = $recentBookings->fetch_assoc()): ?>
                            <li class="list-group-item bg-transparent border-secondary">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?= htmlspecialchars($booking['event_name']) ?></strong>
                                        <div class="text-soft small"><?= date('d M Y', strtotime($booking['event_date'])) ?></div>
                                    </div>
                                    <span class="badge badge-status badge-<?= htmlspecialchars($booking['status']) ?> text-uppercase small"><?= htmlspecialchars($booking['status']) ?></span>
                                </div>
                                <small class="text-soft">Total tiket: <?= htmlspecialchars($booking['total_ticket']) ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>