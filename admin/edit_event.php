<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$eventId = intval($_GET['id'] ?? 0);
if ($eventId <= 0) {
    header('Location: ' . getBaseUrl() . '/admin/events.php');
    exit();
}

$stmt = $conn->prepare('SELECT * FROM events WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
if (!$event) {
    header('Location: ' . getBaseUrl() . '/admin/events.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $artist = sanitize($_POST['artist'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $quota = intval($_POST['quota'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['upcoming','ongoing','finished','cancelled']) ? $_POST['status'] : 'upcoming';

    if (empty($name) || empty($artist) || empty($location) || empty($event_date) || empty($event_time) || $price <= 0 || $quota <= 0) {
        $error = 'Semua field wajib diisi dengan benar.';
    } else {
        $poster = $event['poster'];
        if (!empty($_FILES['poster']['name'])) {
            $uploadDir = __DIR__ . '/../assets/images/posters/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $posterName = time() . '_' . basename($_FILES['poster']['name']);
            $posterPath = $uploadDir . $posterName;
            if (move_uploaded_file($_FILES['poster']['tmp_name'], $posterPath)) {
                $poster = $posterName;
            }
        }

        $update = $conn->prepare('UPDATE events SET name = ?, artist = ?, location = ?, event_date = ?, event_time = ?, price = ?, quota = ?, poster = ?, description = ?, status = ? WHERE id = ?');
        $update->bind_param('ssssssdissi', $name, $artist, $location, $event_date, $event_time, $price, $quota, $poster, $description, $status, $eventId);
        if ($update->execute()) {
            setFlash('success', 'Event berhasil diperbarui.');
            header('Location: ' . getBaseUrl() . '/admin/events.php');
            exit();
        }
        $error = 'Gagal memperbarui data event. Silakan coba lagi.';
    }
}

$pageTitle = 'Edit Event';
?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div>
                <h2 class="mb-1">Edit Event</h2>
                <p class="text-soft mb-0">Perbarui informasi event konser.</p>
            </div>
            <a href="<?= getBaseUrl() ?>/admin/events.php" class="btn btn-outline-primary">Kembali ke Event</a>
        </div>

        <div class="card card-glass p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Event</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['code']) ?>" disabled>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Event</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? $event['name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Artis / Band</label>
                        <input type="text" name="artist" class="form-control" required value="<?= htmlspecialchars($_POST['artist'] ?? $event['artist']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($_POST['location'] ?? $event['location']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="event_date" class="form-control" required value="<?= htmlspecialchars($_POST['event_date'] ?? $event['event_date']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam</label>
                        <input type="time" name="event_time" class="form-control" required value="<?= htmlspecialchars($_POST['event_time'] ?? $event['event_time']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Tiket</label>
                        <input type="number" name="price" class="form-control" required min="0" step="1000" value="<?= htmlspecialchars($_POST['price'] ?? $event['price']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kuota Tiket</label>
                        <input type="number" name="quota" class="form-control" required min="1" value="<?= htmlspecialchars($_POST['quota'] ?? $event['quota']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Event</label>
                        <select name="status" class="form-select">
                            <?php foreach (['upcoming','ongoing','finished','cancelled'] as $statusOption): ?>
                                <option value="<?= $statusOption ?>" <?= ($statusOption === ($_POST['status'] ?? $event['status'])) ? 'selected' : '' ?>><?= ucfirst($statusOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poster Event</label>
                        <input type="file" name="poster" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi Event</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? $event['description']) ?></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>