<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';

echo '<div class="admin-layout">';
include __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-frame container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">Dashboard Admin</h2>
            <p class="text-soft mb-0">Ringkasan cepat sistem pemesanan tiket konser.</p>
        </div>
        <div>
            <a href="<?= getBaseUrl() ?>/admin/events.php" class="btn btn-outline-primary">Kelola Event</a>
        </div>
    </div>

    <?php
    $statsQuery = 'SELECT
        (SELECT COUNT(*) FROM events) AS total_events,
        (SELECT COUNT(*) FROM users WHERE role = "customer") AS total_customers,
        (SELECT SUM(total_ticket) FROM bookings) AS total_tickets,
        (SELECT COUNT(*) FROM bookings) AS total_bookings,
        (SELECT COUNT(*) FROM events WHERE status IN ("upcoming","ongoing")) AS active_events
    ';
    $stats = $conn->query($statsQuery)->fetch_assoc();
    ?>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-value"><?= $stats['total_events'] ?: 0 ?></div>
                <div class="stat-label">Total Event</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card pink">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-value"><?= $stats['total_customers'] ?: 0 ?></div>
                <div class="stat-label">Total Customer</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card cyan">
                <div class="stat-icon"><i class="bi bi-ticket-perforated"></i></div>
                <div class="stat-value"><?= $stats['total_tickets'] ?: 0 ?></div>
                <div class="stat-label">Tiket Terjual</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-value"><?= $stats['total_bookings'] ?: 0 ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-glass p-4 h-100">
                <h5 class="mb-3">Event Aktif</h5>
                <p class="text-soft mb-0">Jumlah event upcoming / ongoing yang sedang aktif saat ini.</p>
                <div class="display-6 mt-3"><?= $stats['active_events'] ?: 0 ?></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-glass p-4 h-100">
                <h5 class="mb-3">Aktivitas Terbaru</h5>
                <?php
                $latestSql = 'SELECT b.booking_code, b.total_ticket, b.status, b.booking_date, u.name AS customer_name, e.name AS event_name
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN events e ON b.event_id = e.id
                    ORDER BY b.booking_date DESC LIMIT 6';
                $latest = $conn->query($latestSql);
                ?>
                <?php if ($latest->num_rows > 0): ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($row = $latest->fetch_assoc()): ?>
                            <li class="list-group-item bg-transparent border-secondary px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?= htmlspecialchars($row['customer_name']) ?></strong>
                                        <div class="text-soft small">Booking <?= htmlspecialchars($row['booking_code']) ?> untuk <?= htmlspecialchars($row['event_name']) ?></div>
                                    </div>
                                    <span class="badge badge-status badge-<?= htmlspecialchars($row['status']) ?> text-uppercase small"><?= htmlspecialchars($row['status']) ?></span>
                                </div>
                                <small class="text-soft"><?= date('d M Y H:i', strtotime($row['booking_date'])) ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-soft">Belum ada aktivitas booking terbaru.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>