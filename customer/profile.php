<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT name, email, phone FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($name) || empty($phone)) {
        $error = 'Nama dan nomor telepon wajib diisi.';
    } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $error = 'Password baru dan konfirmasi tidak cocok.';
    } else {
        if (!empty($newPassword)) {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare('UPDATE users SET name = ?, phone = ?, password = ? WHERE id = ?');
            $update->bind_param('sssi', $name, $phone, $passwordHash, $userId);
        } else {
            $update = $conn->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
            $update->bind_param('ssi', $name, $phone, $userId);
        }
        if ($update->execute()) {
            $_SESSION['user_name'] = $name;
            setFlash('success', 'Profil berhasil diperbarui.');
            header('Location: ' . getBaseUrl() . '/customer/profile.php');
            exit();
        }
        $error = 'Gagal memperbarui profil. Silakan coba lagi.';
    }
}

$pageTitle = 'Edit Profil';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-glass p-4">
                <h3 class="mb-3">Edit Profil</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? $user['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" disabled value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <button class="btn btn-primary">Simpan Profil</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>