<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$users = $conn->query('SELECT id, name, email, phone, created_at FROM users WHERE role = "customer" ORDER BY created_at DESC');
$pageTitle = 'Manajemen User';
?>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="admin-frame container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="mb-1">Manajemen User</h2>
                <p class="text-soft mb-0">Lihat daftar customer yang terdaftar di sistem.</p>
            </div>
        </div>

        <div class="card card-glass p-3">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows === 0): ?>
                            <tr><td colspan="5" class="text-center text-soft">Belum ada data user customer.</td></tr>
                        <?php endif; ?>
                        <?php $no = 1; while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?: '-') ?></td>
                                <td><?= formatDate($user['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>