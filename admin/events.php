<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;
$where = 'WHERE 1=1';
$params = [];
$types = '';

if ($search !== '') {
    $where .= ' AND (name LIKE ? OR artist LIKE ? OR location LIKE ? OR code LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ssss';
}
if (in_array($status, ['upcoming','ongoing','finished','cancelled'])) {
    $where .= ' AND status = ?';
    $params[] = $status;
    $types .= 's';
}

$countSql = "SELECT COUNT(*) AS total FROM events $where";
$countStmt = $conn->prepare($countSql);
if ($types) {
    bindParams($countStmt, $types, $params);
}
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'] ?: 0;

$listSql = "SELECT * FROM events $where ORDER BY event_date ASC, event_time ASC LIMIT ? OFFSET ?";
$listStmt = $conn->prepare($listSql);
if ($types) {
    $bindTypes = $types . 'ii';
    $queryParams = array_merge($params, [$limit, $offset]);
    bindParams($listStmt, $bindTypes, $queryParams);
} else {
    $listStmt->bind_param('ii', $limit, $offset);
}
$listStmt->execute();
$events = $listStmt->get_result();
$totalPages = max(1, ceil($total / $limit));

?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="mb-1">Kelola Event</h2>
                <p class="text-soft mb-0">Tambahkan, edit, dan kelola event konser.</p>
            </div>
            <a href="<?= getBaseUrl() ?>/admin/add_event.php" class="btn btn-primary">Tambah Event</a>
        </div>

        <div class="card card-glass border-0 p-3 mb-4">
            <form class="row g-3" method="get" action="<?= getBaseUrl() ?>/admin/events.php">
                <div class="col-md-6">
                    <input type="search" name="search" class="form-control" placeholder="Cari event, artis, lokasi" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                        <option value="ongoing" <?= $status === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                        <option value="finished" <?= $status === 'finished' ? 'selected' : '' ?>>Finished</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Event</th>
                        <th>Artist</th>
                        <th>Tanggal</th>
                        <th>Harga</th>
                        <th>Kuota</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($events->num_rows === 0): ?>
                        <tr><td colspan="8" class="text-center text-soft">Tidak ada event ditemukan.</td></tr>
                    <?php endif; ?>
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['code']) ?></td>
                            <td><?= htmlspecialchars($event['name']) ?></td>
                            <td><?= htmlspecialchars($event['artist']) ?></td>
                            <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>
                            <td><?= formatRupiah($event['price']) ?></td>
                            <td><?= htmlspecialchars($event['sold']) ?>/<?= htmlspecialchars($event['quota']) ?></td>
                            <td><span class="badge badge-status badge-<?= htmlspecialchars($event['status']) ?> text-uppercase"><?= htmlspecialchars($event['status']) ?></span></td>
                            <td>
                                <a href="<?= getBaseUrl() ?>/admin/edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-light me-1">Edit</a>
                                <form method="post" action="<?= getBaseUrl() ?>/admin/delete_event.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger confirm-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4" aria-label="Halaman event">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= getBaseUrl() ?>/admin/events.php?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>