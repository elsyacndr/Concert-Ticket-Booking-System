<aside class="admin-sidebar d-none d-lg-flex flex-column">
    <!-- Brand -->
    <div class="px-3 py-4 mb-2">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c4dff,#ff4db8);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-music-note-beamed text-white" style="font-size:1.1rem;"></i>
            </div>
            <span class="brand-logo">VibeTicket</span>
        </div>
        <small class="text-muted" style="font-size:0.72rem;padding-left:44px;">Admin Panel</small>
    </div>

    <!-- User Info -->
    <div class="mx-3 mb-3 p-3 rounded-3" style="background:rgba(124,77,255,0.10);border:1px solid rgba(124,77,255,0.20);">
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#7c4dff,#ff4db8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-fill text-white" style="font-size:0.9rem;"></i>
            </div>
            <div style="overflow:hidden;">
                <div class="fw-600 text-white" style="font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
                <div style="font-size:0.7rem;color:#7c4dff;">Administrator</div>
            </div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-grow-1 px-2">
        <div class="sidebar-section">Menu Utama</div>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'events.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/admin/events.php">
                    <i class="bi bi-music-note-list"></i> Kelola Event
                </a>
            </li>
        </ul>
        <div class="sidebar-section mt-3">Manajemen</div>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/admin/users.php">
                    <i class="bi bi-people"></i> Data User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'bookings.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/admin/bookings.php">
                    <i class="bi bi-receipt"></i> Booking & Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : '' ?>" href="<?= getBaseUrl() ?>/admin/reports.php">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout -->
    <div class="px-2 pb-4 mt-auto">
        <a class="nav-link text-danger" href="<?= getBaseUrl() ?>/auth/logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</aside>
