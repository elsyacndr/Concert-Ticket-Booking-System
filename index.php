<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 6;
$offset = ($page - 1) * $limit;

$where = 'WHERE 1=1';
$params = [];
$types = '';

if (!empty($search)) {
    $where .= ' AND (name LIKE ? OR artist LIKE ? OR location LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}
if (!empty($status) && in_array($status, ['upcoming','ongoing','finished','cancelled'])) {
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
$countResult = $countStmt->get_result()->fetch_assoc();
$totalEvents = $countResult['total'] ?? 0;

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

$totalPages = max(1, ceil($totalEvents / $limit));
?>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <div class="hero-banner p-5 card-glass shadow-sm">
                <h1 class="display-5 fw-bold">VibeTicket</h1>
                <p class="lead">Sistem pemesanan tiket konser modern untuk event musik, festival, dan konser kampus.</p>
                <p class="mb-4">Mulai eksplor acara, pesan tiket, dan kelola transaksi dengan desain profesional dan ramah untuk portofolio.</p>
                <a href="<?= getBaseUrl() ?>/customer/events.php" class="btn btn-primary btn-lg me-2">Jelajah Event</a>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= getBaseUrl() ?>/auth/register.php" class="btn btn-outline-primary btn-lg">Daftar Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="card card-glass border-0 p-4">
                <h5 class="mb-3">Filter Event</h5>
                <form class="row g-2" method="get" action="<?= getBaseUrl() ?>/index.php">
                    <div class="col-12">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama, artis, atau lokasi" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-12">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                            <option value="ongoing" <?= $status === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="finished" <?= $status === 'finished' ? 'selected' : '' ?>>Finished</option>
                            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 d-grid">
                        <button class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="h4">Daftar Konser</h2>
            <p class="text-soft mb-0">Total event ditemukan: <?= $totalEvents ?></p>
        </div>
        <a href="<?= getBaseUrl() ?>/customer/events.php" class="btn btn-outline-primary btn-sm">Lihat Semua Event</a>
    </div>

    <div class="row g-4">
        <?php if ($events->num_rows === 0): ?>
            <div class="col-12">
                <div class="card card-glass p-4 text-center">
                    <h5>Tidak ada event.</h5>
                    <p class="text-soft">Silakan ubah kata kunci pencarian atau status.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php while ($event = $events->fetch_assoc()): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card card-glass h-100 border-0 overflow-hidden">
                    <?php $posterId = 'poster-' . $event['id']; ?>
                    <img src="<?= getBaseUrl() ?>/assets/images/posters/<?= htmlspecialchars($event['poster'] ?: 'default_poster.jpg') ?>" class="event-poster" alt="Poster <?= htmlspecialchars($event['name']) ?>" data-fallback="<?= $posterId ?>">
                    <div id="<?= $posterId ?>" class="placeholder-box" style="display:none;"><i class="bi bi-image"></i> Poster tidak tersedia</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><?= htmlspecialchars($event['name']) ?></h5>
                                <small class="text-soft"><?= htmlspecialchars($event['artist']) ?></small>
                            </div>
                            <span class="badge badge-status badge-<?= htmlspecialchars($event['status']) ?> text-uppercase"><?= htmlspecialchars($event['status']) ?></span>
                        </div>
                        <p class="mb-2 text-soft"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($event['location']) ?></p>
                        <p class="mb-2 text-soft"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($event['event_date'])) ?> • <?= date('H:i', strtotime($event['event_time'])) ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <strong class="text-white"><?= formatRupiah($event['price']) ?></strong>
                            <a href="<?= getBaseUrl() ?>/customer/event_detail.php?id=<?= $event['id'] ?>" class="btn btn-outline-primary btn-sm">Detail</a>
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
                        <a class="page-link" href="<?= getBaseUrl() ?>/index.php?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>