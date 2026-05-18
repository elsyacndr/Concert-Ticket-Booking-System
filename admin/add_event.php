<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $artist = sanitize($_POST['artist'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $quota = intval($_POST['quota'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['upcoming','ongoing','finished','cancelled']) ? $_POST['status'] : 'upcoming';

    if (empty($code) || empty($name) || empty($artist) || empty($location) || empty($event_date) || empty($event_time) || $price <= 0 || $quota <= 0) {
        $error = 'Semua field wajib diisi dengan format yang benar.';
    } else {
        $poster = 'default_poster.jpg';
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

        $stmt = $conn->prepare('INSERT INTO events (code, name, artist, location, event_date, event_time, price, quota, poster, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssdiss', $code, $name, $artist, $location, $event_date, $event_time, $price, $quota, $poster, $description, $status);
        if ($stmt->execute()) {
            setFlash('success', 'Event berhasil ditambahkan.');
            header('Location: ' . getBaseUrl() . '/admin/events.php');
            exit();
        }
        $error = 'Gagal menyimpan data event. Silakan coba lagi.';
    }
}

$pageTitle = 'Tambah Event';
?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div>
                <h2 class="mb-1">Tambah Event</h2>
                <p class="text-soft">Isi data event konser baru untuk ditampilkan ke customer.</p>
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
                        <input type="text" name="code" class="form-control" required placeholder="EVT001" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Event</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Artis / Band</label>
                        <input type="text" name="artist" class="form-control" required value="<?= htmlspecialchars($_POST['artist'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="event_date" class="form-control" required value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam</label>
                        <input type="time" name="event_time" class="form-control" required value="<?= htmlspecialchars($_POST['event_time'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Tiket</label>
                        <input type="number" name="price" class="form-control" required min="0" step="1000" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kuota Tiket</label>
                        <input type="number" name="quota" class="form-control" required min="1" value="<?= htmlspecialchars($_POST['quota'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Event</label>
                        <select name="status" class="form-select">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poster Event</label>
                        <input type="file" name="poster" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi Event</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">Simpan Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>