<?php
require_once __DIR__ . '/../includes/header.php';
redirectIfLoggedIn();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email dan password harus diisi dengan format yang benar.';
    } else {
        $stmt = $conn->prepare('SELECT id, role, name, email, password, photo FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_photo'] = $user['photo'];

            setFlash('success', 'Login berhasil! Selamat datang, ' . $user['name'] . '.');
            if ($user['role'] === 'admin') {
                header('Location: ' . getBaseUrl() . '/admin/dashboard.php');
            } else {
                header('Location: ' . getBaseUrl() . '/customer/dashboard.php');
            }
            exit();
        }
        $error = 'Email atau password salah. Silakan coba kembali.';
    }
}

$pageTitle = 'Login';
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height:80vh;">
        <div class="col-md-9 col-lg-7 col-xl-5">
            <!-- Logo -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3"
                    style="width:60px;height:60px;border-radius:18px;background:linear-gradient(135deg,#7c4dff,#ff4db8);">
                    <i class="bi bi-music-note-beamed text-white" style="font-size:1.6rem;"></i>
                </div>
                <h2 class="fw-800 mb-1">Selamat Datang!</h2>
                <p class="text-soft">Masuk ke akun VibeTicket kamu</p>
            </div>

            <div class="card-glass p-4 p-md-5" style="border-radius:20px;">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.10);border-right:none;color:#9ba3c8;">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control" style="border-left:none;" required placeholder="contoh@mail.com">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.10);border-right:none;color:#9ba3c8;">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput" class="form-control" style="border-left:none;border-right:none;" required placeholder="Password kamu">
                            <button type="button" class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.10);border-left:none;color:#9ba3c8;cursor:pointer;" onclick="togglePassword('passwordInput',this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-soft">Belum punya akun?</span>
                    <a href="<?= getBaseUrl() ?>/auth/register.php" class="ms-1 fw-600" style="color:#a07aff;">Daftar sekarang</a>
                </div>

                <!-- Demo credentials -->
                <div class="mt-4 p-3 rounded-3" style="background:rgba(124,77,255,0.08);border:1px solid rgba(124,77,255,0.20);">
                    <p class="text-soft mb-1" style="font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Demo Login</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <div>
                            <span class="text-soft" style="font-size:0.78rem;">Admin:</span>
                            <code style="font-size:0.78rem;color:#a07aff;">admin@concert.com</code>
                        </div>
                        <div>
                            <span class="text-soft" style="font-size:0.78rem;">Pass:</span>
                            <code style="font-size:0.78rem;color:#a07aff;">password</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>