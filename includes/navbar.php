<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top app-navbar">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= getBaseUrl() ?>/index.php">
            <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#7c4dff,#ff4db8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-music-note-beamed text-white" style="font-size:1rem;"></i>
            </div>
            <span>VibeTicket</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/index.php">
                        <i class="bi bi-house me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= getBaseUrl() ?>/customer/events.php">
                        <i class="bi bi-music-note-list me-1"></i>Konser
                    </a>
                </li>
                <?php if (isCustomer()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= getBaseUrl() ?>/customer/bookings.php">
                        <i class="bi bi-ticket-perforated me-1"></i>Booking Saya
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= getBaseUrl() ?>/admin/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= getBaseUrl() ?>/auth/login.php" class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a href="<?= getBaseUrl() ?>/auth/register.php" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-person-plus me-1"></i>Daftar
                    </a>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-2"
                            style="background:rgba(124,77,255,0.18);border:1px solid rgba(124,77,255,0.35);color:#fff;border-radius:10px;"
                            type="button" data-bs-toggle="dropdown">
                            <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#7c4dff,#ff4db8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-person-fill" style="font-size:0.75rem;"></i>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="background:#1a1d3a;border:1px solid rgba(124,77,255,0.25);border-radius:12px;min-width:180px;">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item text-white" href="<?= getBaseUrl() ?>/admin/dashboard.php"><i class="bi bi-speedometer2 me-2 text-purple"></i>Dashboard Admin</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item text-white" href="<?= getBaseUrl() ?>/customer/dashboard.php"><i class="bi bi-grid me-2 text-purple"></i>Dashboard</a></li>
                                <li><a class="dropdown-item text-white" href="<?= getBaseUrl() ?>/customer/profile.php"><i class="bi bi-person me-2 text-purple"></i>Profil Saya</a></li>
                                <li><a class="dropdown-item text-white" href="<?= getBaseUrl() ?>/customer/bookings.php"><i class="bi bi-ticket-perforated me-2 text-purple"></i>Booking Saya</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.10);"></li>
                            <li><a class="dropdown-item" style="color:#ff6b6b;" href="<?= getBaseUrl() ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
