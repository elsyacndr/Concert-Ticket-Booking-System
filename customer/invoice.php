<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$bookingId = intval($_GET['id'] ?? 0);
$userId    = $_SESSION['user_id'];

// Ambil data booking lengkap
$stmt = $conn->prepare(
    'SELECT b.*, e.name AS event_name, e.artist, e.location, e.event_date,
            e.event_time, e.price AS ticket_price, e.poster,
            u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
     FROM bookings b
     JOIN events e ON b.event_id = e.id
     JOIN users  u ON b.user_id  = u.id
     WHERE b.id = ? AND b.user_id = ? LIMIT 1'
);
$stmt->bind_param('ii', $bookingId, $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    setFlash('danger', 'Invoice tidak ditemukan.');
    header('Location: ' . getBaseUrl() . '/customer/bookings.php');
    exit();
}

// Handle upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_proof') {
    if (empty($_FILES['payment_proof']['name'])) {
        setFlash('danger', 'Pilih file bukti pembayaran terlebih dahulu.');
    } else {
        $uploadDir = __DIR__ . '/../assets/images/payments/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext     = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','pdf'];
        if (!in_array($ext, $allowed)) {
            setFlash('danger', 'Format tidak didukung. Gunakan JPG, PNG, atau PDF.');
        } elseif ($_FILES['payment_proof']['size'] > 5 * 1024 * 1024) {
            setFlash('danger', 'Ukuran file maksimal 5MB.');
        } else {
            $fileName = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadDir . $fileName)) {
                $upd = $conn->prepare('UPDATE bookings SET payment_proof = ? WHERE id = ? AND user_id = ?');
                $upd->bind_param('sii', $fileName, $bookingId, $userId);
                $upd->execute();
                setFlash('success', 'Bukti pembayaran berhasil diunggah! Menunggu konfirmasi admin.');
                header('Location: ' . getBaseUrl() . '/customer/invoice.php?id=' . $bookingId);
                exit();
            }
            setFlash('danger', 'Gagal mengunggah file. Cek permission folder.');
        }
    }
}

// Info rekening simulasi
$bankAccounts = [
    ['bank'=>'BCA',     'no'=>'1234567890', 'name'=>'VibeTicket Indonesia'],
    ['bank'=>'Mandiri', 'no'=>'0987654321', 'name'=>'VibeTicket Indonesia'],
    ['bank'=>'BNI',     'no'=>'1122334455', 'name'=>'VibeTicket Indonesia'],
];
$qrisCode = 'QRIS-VIBETICKET-2026';

$pageTitle = 'Invoice ' . $booking['booking_code'];
?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background:transparent;">
            <li class="breadcrumb-item"><a href="<?= getBaseUrl() ?>/customer/bookings.php" class="text-soft">Riwayat Booking</a></li>
            <li class="breadcrumb-item active text-white"><?= htmlspecialchars($booking['booking_code']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- ── INVOICE CARD ── -->
        <div class="col-lg-8">
            <div class="card-glass p-4 p-md-5" id="invoiceArea" style="border-radius:20px;">
                <!-- Header Invoice -->
                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#a855f7,#f472b6);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-music-note-beamed text-white"></i>
                            </div>
                            <span style="font-size:1.3rem;font-weight:900;background:linear-gradient(90deg,#c084fc,#f9a8d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">VibeTicket</span>
                        </div>
                        <p class="text-soft mb-0" style="font-size:0.82rem;">Your Gateway to Unforgettable Concerts</p>
                    </div>
                    <div class="text-end">
                        <div style="font-size:1.1rem;font-weight:800;color:#c084fc;">INVOICE</div>
                        <div style="font-size:0.9rem;font-weight:700;"><?= htmlspecialchars($booking['booking_code']) ?></div>
                        <div class="text-soft" style="font-size:0.8rem;"><?= date('d M Y, H:i', strtotime($booking['booking_date'])) ?></div>
                    </div>
                </div>

                <hr style="border-color:rgba(255,255,255,0.12);">

                <!-- Info Customer & Event -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <p class="text-soft mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;font-weight:700;">Ditagihkan Kepada</p>
                        <div class="fw-700"><?= htmlspecialchars($booking['customer_name']) ?></div>
                        <div class="text-soft small"><?= htmlspecialchars($booking['customer_email']) ?></div>
                        <div class="text-soft small"><?= htmlspecialchars($booking['customer_phone'] ?: '-') ?></div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-soft mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;font-weight:700;">Detail Event</p>
                        <div class="fw-700"><?= htmlspecialchars($booking['event_name']) ?></div>
                        <div class="text-soft small"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($booking['artist']) ?></div>
                        <div class="text-soft small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($booking['location']) ?></div>
                        <div class="text-soft small"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($booking['event_date'])) ?> pukul <?= date('H:i', strtotime($booking['event_time'])) ?></div>
                    </div>
                </div>

                <!-- Tabel Item -->
                <div class="table-responsive mb-4">
                    <table class="table mb-0" style="color:#fff;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(168,85,247,0.35);">
                                <th class="text-soft ps-0" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.06em;">Item</th>
                                <th class="text-soft text-center" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.06em;">Qty</th>
                                <th class="text-soft text-end" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.06em;">Harga Satuan</th>
                                <th class="text-soft text-end pe-0" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.06em;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.07);">
                                <td class="ps-0">
                                    <div class="fw-600"><?= htmlspecialchars($booking['event_name']) ?></div>
                                    <div class="text-soft small">Tiket Konser - <?= date('d M Y', strtotime($booking['event_date'])) ?></div>
                                </td>
                                <td class="text-center"><?= $booking['total_ticket'] ?></td>
                                <td class="text-end"><?= formatRupiah($booking['ticket_price']) ?></td>
                                <td class="text-end pe-0 fw-700"><?= formatRupiah($booking['total_price']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="d-flex justify-content-end mb-4">
                    <div style="min-width:260px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-soft">Subtotal</span>
                            <span><?= formatRupiah($booking['total_price']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-soft">Biaya Layanan</span>
                            <span class="text-green">Gratis</span>
                        </div>
                        <hr style="border-color:rgba(255,255,255,0.12);">
                        <div class="d-flex justify-content-between">
                            <span class="fw-800" style="font-size:1.05rem;">TOTAL</span>
                            <span class="fw-800" style="font-size:1.2rem;color:#c084fc;"><?= formatRupiah($booking['total_price']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 rounded-3" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.10);">
                    <div>
                        <span class="text-soft small">Status Pembayaran:</span>
                        <span class="ms-2 badge badge-status badge-<?= $booking['status'] ?> text-uppercase"><?= $booking['status'] ?></span>
                    </div>
                    <?php if ($booking['status'] === 'completed'): ?>
                        <a href="<?= getBaseUrl() ?>/customer/ticket.php?id=<?= $booking['id'] ?>" class="btn btn-success btn-sm" target="_blank">
                            <i class="bi bi-ticket-perforated me-1"></i>Cetak Tiket
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Catatan -->
                <?php if (!empty($booking['notes'])): ?>
                <div class="mt-3 p-3 rounded-3" style="background:rgba(248,113,113,0.10);border:1px solid rgba(248,113,113,0.25);">
                    <i class="bi bi-info-circle me-2 text-danger"></i>
                    <span class="text-soft small"><?= htmlspecialchars($booking['notes']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <button onclick="window.print()" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-printer me-1"></i>Print Invoice
                </button>
                <a href="<?= getBaseUrl() ?>/customer/bookings.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <!-- ── SIDEBAR: PEMBAYARAN ── -->
        <div class="col-lg-4">
            <?php if (in_array($booking['status'], ['pending']) && empty($booking['payment_proof'])): ?>
            <!-- Pilih Metode Pembayaran -->
            <div class="card-glass p-4 mb-4" style="border-radius:16px;">
                <h5 class="mb-1"><i class="bi bi-credit-card me-2 text-purple"></i>Cara Pembayaran</h5>
                <p class="text-soft small mb-3">Pilih metode dan transfer sesuai nominal invoice.</p>

                <!-- Tab Metode -->
                <ul class="nav nav-pills mb-3 gap-1" id="payTab">
                    <li class="nav-item">
                        <button class="nav-link active px-3 py-1" style="font-size:0.82rem;" data-bs-toggle="pill" data-bs-target="#tabBank">Transfer Bank</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-1" style="font-size:0.82rem;" data-bs-toggle="pill" data-bs-target="#tabQris">QRIS</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-1" style="font-size:0.82rem;" data-bs-toggle="pill" data-bs-target="#tabEwallet">E-Wallet</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Transfer Bank -->
                    <div class="tab-pane fade show active" id="tabBank">
                        <?php foreach ($bankAccounts as $bank): ?>
                        <div class="p-3 mb-2 rounded-3" style="background:rgba(168,85,247,0.10);border:1px solid rgba(168,85,247,0.20);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-700" style="color:#c084fc;"><?= $bank['bank'] ?></div>
                                    <div class="fw-800" style="font-size:1.05rem;letter-spacing:0.05em;"><?= $bank['no'] ?></div>
                                    <div class="text-soft small"><?= $bank['name'] ?></div>
                                </div>
                                <button class="btn btn-sm btn-outline-light" onclick="copyText('<?= $bank['no'] ?>', this)">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="mt-2 p-2 rounded-2 text-center" style="background:rgba(250,204,21,0.10);border:1px solid rgba(250,204,21,0.25);">
                            <span class="text-soft small">Transfer tepat: </span>
                            <span class="fw-700" style="color:#facc15;"><?= formatRupiah($booking['total_price']) ?></span>
                        </div>
                    </div>

                    <!-- QRIS -->
                    <div class="tab-pane fade" id="tabQris">
                        <div class="text-center p-3">
                            <div style="width:160px;height:160px;margin:0 auto 12px;background:white;border-radius:12px;display:flex;align-items:center;justify-content:center;padding:12px;">
                                <div style="width:100%;height:100%;background:repeating-linear-gradient(45deg,#1a0045 0px,#1a0045 4px,white 4px,white 8px);border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                    <span style="font-size:0.6rem;color:#1a0045;font-weight:800;text-align:center;">QRIS<br>VibeTicket</span>
                                </div>
                            </div>
                            <div class="text-soft small">Scan QR Code dengan aplikasi e-wallet atau m-banking</div>
                            <div class="fw-700 mt-2" style="color:#facc15;"><?= formatRupiah($booking['total_price']) ?></div>
                        </div>
                    </div>

                    <!-- E-Wallet -->
                    <div class="tab-pane fade" id="tabEwallet">
                        <?php
                        $ewallets = [
                            ['name'=>'GoPay',  'no'=>'081200001111', 'icon'=>'bi-phone'],
                            ['name'=>'OVO',    'no'=>'081200002222', 'icon'=>'bi-phone'],
                            ['name'=>'Dana',   'no'=>'081200003333', 'icon'=>'bi-phone'],
                            ['name'=>'ShopeePay','no'=>'081200004444','icon'=>'bi-phone'],
                        ];
                        foreach ($ewallets as $ew):
                        ?>
                        <div class="p-3 mb-2 rounded-3 d-flex justify-content-between align-items-center" style="background:rgba(34,211,238,0.08);border:1px solid rgba(34,211,238,0.18);">
                            <div>
                                <div class="fw-700" style="color:#67e8f9;"><?= $ew['name'] ?></div>
                                <div class="text-soft small"><?= $ew['no'] ?></div>
                            </div>
                            <button class="btn btn-sm btn-outline-light" onclick="copyText('<?= $ew['no'] ?>', this)">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                        <div class="mt-2 p-2 rounded-2 text-center" style="background:rgba(250,204,21,0.10);border:1px solid rgba(250,204,21,0.25);">
                            <span class="text-soft small">Nominal: </span>
                            <span class="fw-700" style="color:#facc15;"><?= formatRupiah($booking['total_price']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Bukti -->
            <div class="card-glass p-4" style="border-radius:16px;">
                <h5 class="mb-1"><i class="bi bi-upload me-2 text-green"></i>Upload Bukti Bayar</h5>
                <p class="text-soft small mb-3">Setelah transfer, upload bukti pembayaran di sini.</p>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_proof">
                    <div class="mb-3">
                        <label class="form-label">File Bukti (JPG/PNG/PDF, max 5MB)</label>
                        <input type="file" name="payment_proof" class="form-control" required accept="image/*,.pdf" data-preview="previewImg">
                        <img id="previewImg" src="" alt="Preview" style="display:none;max-width:100%;margin-top:8px;border-radius:8px;max-height:150px;object-fit:cover;">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-1"></i>Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>

            <?php elseif ($booking['status'] === 'pending' && !empty($booking['payment_proof'])): ?>
            <!-- Sudah upload, menunggu konfirmasi -->
            <div class="card-glass p-4" style="border-radius:16px;">
                <div class="text-center py-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(250,204,21,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi bi-hourglass-split" style="font-size:1.8rem;color:#facc15;"></i>
                    </div>
                    <h5>Menunggu Konfirmasi</h5>
                    <p class="text-soft small">Bukti pembayaran sudah diterima. Admin sedang memverifikasi pembayaranmu.</p>
                    <a href="<?= getBaseUrl() ?>/assets/images/payments/<?= htmlspecialchars($booking['payment_proof']) ?>" target="_blank" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-eye me-1"></i>Lihat Bukti
                    </a>
                </div>
            </div>

            <?php elseif ($booking['status'] === 'paid'): ?>
            <!-- Paid, menunggu complete -->
            <div class="card-glass p-4" style="border-radius:16px;">
                <div class="text-center py-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(34,211,238,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi bi-check-circle" style="font-size:1.8rem;color:#22d3ee;"></i>
                    </div>
                    <h5>Pembayaran Dikonfirmasi</h5>
                    <p class="text-soft small">Pembayaran telah diverifikasi. Menunggu konfirmasi tiket dari admin.</p>
                </div>
            </div>

            <?php elseif ($booking['status'] === 'completed'): ?>
            <!-- Completed - bisa cetak tiket -->
            <div class="card-glass p-4" style="border-radius:16px;">
                <div class="text-center py-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(74,222,128,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi bi-patch-check-fill" style="font-size:1.8rem;color:#4ade80;"></i>
                    </div>
                    <h5 style="color:#4ade80;">Tiket Dikonfirmasi!</h5>
                    <p class="text-soft small">Selamat! Tiketmu sudah dikonfirmasi. Silakan cetak tiket untuk dibawa ke venue.</p>
                    <a href="<?= getBaseUrl() ?>/customer/ticket.php?id=<?= $booking['id'] ?>" class="btn btn-success w-100 mb-2" target="_blank">
                        <i class="bi bi-ticket-perforated me-1"></i>Cetak Tiket
                    </a>
                </div>
            </div>

            <?php elseif ($booking['status'] === 'rejected'): ?>
            <!-- Rejected -->
            <div class="card-glass p-4" style="border-radius:16px;">
                <div class="text-center py-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(248,113,113,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi bi-x-circle-fill" style="font-size:1.8rem;color:#f87171;"></i>
                    </div>
                    <h5 style="color:#f87171;">Pembayaran Ditolak</h5>
                    <p class="text-soft small">Maaf, pembayaranmu ditolak. Silakan hubungi admin atau pesan ulang.</p>
                    <?php if (!empty($booking['notes'])): ?>
                    <div class="p-2 rounded-2 text-start" style="background:rgba(248,113,113,0.10);border:1px solid rgba(248,113,113,0.25);">
                        <small class="text-soft">Alasan: <?= htmlspecialchars($booking['notes']) ?></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i>';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-light');
        setTimeout(function(){ btn.innerHTML = orig; btn.classList.remove('btn-success'); btn.classList.add('btn-outline-light'); }, 1500);
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
