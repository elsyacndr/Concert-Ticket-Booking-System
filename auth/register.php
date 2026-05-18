<?php
require_once __DIR__ . '/../includes/header.php';
redirectIfLoggedIn();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($name) || !$email || empty($phone) || empty($password) || empty($confirmPassword)) {
        $error = 'Semua field wajib diisi dengan benar.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $photo = 'default.png';
            $stmt = $conn->prepare('INSERT INTO users (role, name, email, password, phone, photo) VALUES (?, ?, ?, ?, ?, ?)');
            $role = 'customer';
            $stmt->bind_param('ssssss', $role, $name, $email, $passwordHash, $phone, $photo);
            if ($stmt->execute()) {
                setFlash('success', 'Pendaftaran berhasil! Silakan login untuk melanjutkan.');
                header('Location: ' . getBaseUrl() . '/auth/login.php');
                exit();
            }
            $error = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    }
}

$pageTitle = 'Register';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height:80vh;">
        <div class="col-md-9 col-lg-7 col-xl-6">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3"
                    style="width:60px;height:60px;border-radius:18px;background:linear-gradient(135deg,#7c4dff,#ff4db8);">
                    <i class="bi bi-person-plus-fill text-white" style="font-size:1.5rem;"></i>
                </div>
                <h2 class="fw-800 mb-1">Buat Akun Baru</h2>
                <p class="text-soft">Daftar dan mulai pesan tiket konser favoritmu</p>
            </div>

            <div class="card-glass p-4 p-md-5" style="border-radius:20px;">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama lengkap kamu" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="contoh@mail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" required placeholder="081234567890" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="pw1" class="form-control" style="border-right:none;" required placeholder="Min. 6 karakter">
                                <button type="button" class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.10);border-left:none;color:#9ba3c8;cursor:pointer;" onclick="togglePassword('pw1',this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="pw2" class="form-control" style="border-right:none;" required placeholder="Ulangi password">
                                <button type="button" class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.10);border-left:none;color:#9ba3c8;cursor:pointer;" onclick="togglePassword('pw2',this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
                                <i class="bi bi-person-check me-2"></i>Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <span class="text-soft">Sudah punya akun?</span>
                    <a href="<?= getBaseUrl() ?>/auth/login.php" class="ms-1 fw-600" style="color:#a07aff;">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>