<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 8;
$offset = ($page - 1) * $limit;
$where = 'WHERE 1=1';
$params = [];
$types = '';

if ($search !== '') {
    $where .= ' AND (name LIKE ? OR artist LIKE ? OR location LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
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
$pageTitle = 'Daftar Event';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h2 class="mb-1">Daftar Konser</h2>
            <p class="text-soft mb-0">Temukan event musik terbaru dan pesan tiket favoritmu.</p>
        </div>
        <a href="<?= getBaseUrl() ?>/customer/bookings.php" class="btn btn-outline-primary">Riwayat Booking</a>
    </div>

    <div class="card card-glass p-3 mb-4">
        <form class="row g-3" method="get" action="<?= getBaseUrl() ?>/customer/events.php">
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

    <div class="row g-4">
        <?php if ($events->num_rows === 0): ?>
            <div class="col-12">
                <div class="card card-glass p-4 text-center">
                    <h5>Tidak ada event ditemukan.</h5>
                </div>
            </div>
        <?php endif; ?>
        <?php while ($event = $events->fetch_assoc()): ?>
            <div class="col-md-6 col-xl-3">
                <div class="card card-glass h-100 border-0">
                    <img src="<?= getBaseUrl() ?>/assets/images/posters/<?= htmlspecialchars($event['poster'] ?: 'default_poster.jpg') ?>" class="event-poster" alt="Poster <?= htmlspecialchars($event['name']) ?>" data-fallback="poster-<?= $event['id'] ?>">
                    <div id="poster-<?= $event['id'] ?>" class="placeholder-box" style="display:none;"><i class="bi bi-image"></i> Poster tidak tersedia</div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1"><?= htmlspecialchars($event['name']) ?></h5>
                        <p class="text-soft mb-2 small"><?= htmlspecialchars($event['artist']) ?></p>
                        <div class="mb-3 text-soft small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($event['location']) ?></div>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge badge-status badge-<?= htmlspecialchars($event['status']) ?> text-uppercase small"><?= htmlspecialchars($event['status']) ?></span>
                            <a href="<?= getBaseUrl() ?>/customer/event_detail.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-5" aria-label="Pagination Event">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= getBaseUrl() ?>/customer/events.php?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>