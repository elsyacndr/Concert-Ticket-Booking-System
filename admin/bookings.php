<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = intval($_POST['booking_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $allowed = ['approve' => 'paid', 'reject' => 'rejected', 'complete' => 'completed'];
    if ($bookingId > 0 && isset($allowed[$action])) {
        $status = $allowed[$action];
        $note = sanitize($_POST['note'] ?? '');
        $stmt = $conn->prepare('UPDATE bookings SET status = ?, notes = ? WHERE id = ?');
        $stmt->bind_param('ssi', $status, $note, $bookingId);
        if ($stmt->execute()) {
            setFlash('success', 'Status booking berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui status booking.');
        }
    }
    header('Location: ' . getBaseUrl() . '/admin/bookings.php');
    exit();
}

$bookingsSql = 'SELECT b.*, u.name AS customer_name, e.name AS event_name, e.location FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN events e ON b.event_id = e.id
    ORDER BY b.booking_date DESC';
$bookings = $conn->query($bookingsSql);
$pageTitle = 'Booking & Transaksi';
?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="mb-1">Booking & Transaksi</h2>
                <p class="text-soft mb-0">Kelola status pembayaran dan konfirmasi tiket.</p>
            </div>
        </div>

        <div class="card card-glass p-3">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Event</th>
                            <th>Total Tiket</th>
                            <th>Total Harga</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bookings->num_rows === 0): ?>
                            <tr><td colspan="8" class="text-center text-soft">Belum ada booking.</td></tr>
                        <?php endif; ?>
                        <?php while ($row = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['booking_code']) ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['event_name']) ?></td>
                                <td><?= htmlspecialchars($row['total_ticket']) ?></td>
                                <td><?= formatRupiah($row['total_price']) ?></td>
                                <td>
                                    <?php if (!empty($row['payment_proof'])): ?>
                                        <a href="<?= getBaseUrl() ?>/assets/images/payments/<?= htmlspecialchars($row['payment_proof']) ?>" target="_blank" class="link-light">Lihat</a>
                                    <?php else: ?>
                                        <span class="text-soft">Belum upload</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-status badge-<?= htmlspecialchars($row['status']) ?> text-uppercase small"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td>
                                    <form method="post" class="d-flex gap-2 flex-wrap">
                                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-warning">Reject</button>
                                        <?php elseif ($row['status'] === 'paid'): ?>
                                            <button type="submit" name="action" value="complete" class="btn btn-sm btn-primary">Confirm</button>
                                        <?php else: ?>
                                            <span class="text-soft">Tidak ada aksi</span>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>