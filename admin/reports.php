<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$where = 'WHERE 1=1';
$params = [];
$types = '';

if ($startDate) {
    $where .= ' AND b.booking_date >= ?';
    $params[] = $startDate . ' 00:00:00';
    $types .= 's';
}
if ($endDate) {
    $where .= ' AND b.booking_date <= ?';
    $params[] = $endDate . ' 23:59:59';
    $types .= 's';
}

$sql = 'SELECT b.*, u.name AS customer_name, e.name AS event_name FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN events e ON b.event_id = e.id ' . $where . ' ORDER BY b.booking_date DESC';
$stmt = $conn->prepare($sql);
if ($types) {
    bindParams($stmt, $types, $params);
}
$stmt->execute();
$reports = $stmt->get_result();

$summarySql = 'SELECT COUNT(*) AS total_booking, SUM(total_price) AS total_revenue FROM bookings b ' . $where;
$summaryStmt = $conn->prepare($summarySql);
if ($types) {
    bindParams($summaryStmt, $types, $params);
}
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();

$pageTitle = 'Laporan Transaksi';
?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="mb-1">Laporan Transaksi</h2>
                <p class="text-soft mb-0">Lihat laporan booking dan filter berdasarkan tanggal.</p>
            </div>
            <button class="btn btn-outline-light" onclick="window.print()">Print Laporan</button>
        </div>

        <div class="card card-glass p-3 mb-4">
            <form class="row g-3" method="get" action="<?= getBaseUrl() ?>/admin/reports.php">
                <div class="col-md-5">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary mt-4">Terapkan</button>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-glass p-3 h-100">
                    <h6 class="text-soft">Total Booking</h6>
                    <h3><?= $summary['total_booking'] ?: 0 ?></h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-glass p-3 h-100">
                    <h6 class="text-soft">Pendapatan</h6>
                    <h3><?= formatRupiah($summary['total_revenue'] ?: 0) ?></h3>
                </div>
            </div>
        </div>

        <div class="card card-glass p-3">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Customer</th>
                            <th>Event</th>
                            <th>Tiket</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reports->num_rows === 0): ?>
                            <tr><td colspan="7" class="text-center text-soft">Tidak ada data transaksi.</td></tr>
                        <?php endif; ?>
                        <?php while ($row = $reports->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['booking_code']) ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['event_name']) ?></td>
                                <td><?= htmlspecialchars($row['total_ticket']) ?></td>
                                <td><?= formatRupiah($row['total_price']) ?></td>
                                <td><span class="badge badge-status badge-<?= htmlspecialchars($row['status']) ?> text-uppercase small"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td><?= date('d M Y', strtotime($row['booking_date'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>