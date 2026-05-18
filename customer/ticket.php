<?php
require_once __DIR__ . '/../includes/header.php';
requireCustomer();

$bookingId = intval($_GET['id'] ?? 0);
$userId    = $_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT b.*, e.name AS event_name, e.artist, e.location, e.event_date,
            e.event_time, e.price AS ticket_price, e.poster,
            u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
     FROM bookings b
     JOIN events e ON b.event_id = e.id
     JOIN users  u ON b.user_id  = u.id
     WHERE b.id = ? AND b.user_id = ? AND b.status = "completed" LIMIT 1'
);
$stmt->bind_param('ii', $bookingId, $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    setFlash('danger', 'Tiket tidak ditemukan atau belum dikonfirmasi.');
    header('Location: ' . getBaseUrl() . '/customer/bookings.php');
    exit();
}

$tickets = [];
for ($i = 1; $i <= $booking['total_ticket']; $i++) {
    $ticketId = strtoupper(substr(md5($booking['booking_code'] . $i . 'VIBE'), 0, 8));
    $barcode  = $booking['booking_code'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $tickets[] = ['id' => $ticketId, 'barcode' => $barcode, 'number' => $i];
}

$pageTitle = 'E-Ticket - ' . $booking['booking_code'];
?><?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
    <div><h4 class="mb-1"><i class="bi bi-ticket-perforated me-2" style="color:#c084fc;"></i>E-Ticket Konser</h4>
    <p class="text-soft mb-0 small"><?= htmlspecialchars($booking['booking_code']) ?> &bull; <?= $booking['total_ticket'] ?> tiket</p></div>
    <div class="d-flex gap-2"><button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i>Cetak Semua Tiket</button></div></div>
<?php foreach ($tickets as $ticket): ?>
<div class="vt-ticket mb-5"><div class="vt-wrap">
<div class="vt-stub"><div class="vt-stub-brand"><i class="bi bi-music-note-beamed"></i><br>Vibe<br>Ticket</div>
<div class="vt-stub-rotate"><?= htmlspecialchars($booking['event_name']) ?></div>
<div class="vt-stub-num"><?= $ticket['number'] ?>/<?= $booking['total_ticket'] ?></div></div>
<div class="vt-main"><div class="vt-top"><div>
<div class="vt-event-name"><?= htmlspecialchars($booking['event_name']) ?></div>
<div class="vt-artist"><?= htmlspecialchars($booking['artist']) ?></div></div>
<div class="vt-valid-badge"><i class="bi bi-patch-check-fill me-1"></i>VALID</div></div>
<div class="vt-info-grid">
<div class="vt-info-cell"><span class="vt-label">TANGGAL</span><span class="vt-value"><?= date('d M Y',strtotime($booking['event_date'])) ?></span></div>
<div class="vt-info-cell"><span class="vt-label">JAM</span><span class="vt-value"><?= date('H:i',strtotime($booking['event_time'])) ?> WIB</span></div>
<div class="vt-info-cell"><span class="vt-label">VENUE</span><span class="vt-value"><?= htmlspecialchars($booking['location']) ?></span></div>
<div class="vt-info-cell"><span class="vt-label">PEMEGANG</span><span class="vt-value"><?= htmlspecialchars($booking['customer_name']) ?></span></div>
<div class="vt-info-cell"><span class="vt-label">HARGA</span><span class="vt-value"><?= formatRupiah($booking['ticket_price']) ?></span></div>
<div class="vt-info-cell"><span class="vt-label">NO TIKET</span><span class="vt-value"><?= $ticket['number'] ?> dari <?= $booking['total_ticket'] ?></span></div></div>
<div class="vt-ticket-id"><i class="bi bi-hash me-1"></i>TICKET ID: <strong><?= $ticket['id'] ?></strong></div></div>
<div class="vt-tear"><div class="vt-circle top"></div><div class="vt-dashes"></div><div class="vt-circle bottom"></div></div>
<div class="vt-barcode-section"><div class="vt-barcode-wrap"><div class="vt-barcode">
<?php $h=md5($ticket['barcode']); for($b=0;$b<40;$b++): $w=(hexdec($h[$b%32])%3)+1; $hh=(hexdec($h[(40-$b)%32])%30)+50; ?>
<div class="vt-bar" style="width:<?= $w ?>px;height:<?= $hh ?>px;"></div>
<?php endfor; ?>
</div><div class="vt-barcode-num"><?= htmlspecialchars($ticket['barcode']) ?></div></div>
<div class="vt-qr-wrap"><div class="vt-qr"><div class="vt-qr-inner"><div class="vt-qr-c tl"></div><div class="vt-qr-c tr"></div><div class="vt-qr-c bl"></div><div class="vt-qr-dots"></div></div></div><div class="vt-qr-label">SCAN ME</div></div>
</div></div></div>
<?php endforeach; ?>
<div class="card-glass p-3 mt-2 no-print" style="border-radius:12px;"><p class="text-soft small mb-0"><i class="bi bi-info-circle me-1"></i>Tunjukkan tiket ini kepada petugas. Setiap lembar berlaku 1 orang.</p></div>
</div>
<style>
.vt-ticket{max-width:820px;margin:0 auto;}
.vt-wrap{display:flex;border-radius:18px;overflow:hidden;box-shadow:0 10px 50px rgba(168,85,247,.40);border:1px solid rgba(168,85,247,.35);min-height:200px;}
.vt-stub{width:64px;flex-shrink:0;background:linear-gradient(180deg,#a855f7 0%,#7c3aed 50%,#f472b6 100%);display:flex;flex-direction:column;align-items:center;justify-content:space-between;padding:16px 8px;color:#fff;}
.vt-stub-brand{font-size:.55rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;text-align:center;line-height:1.3;}
.vt-stub-rotate{writing-mode:vertical-rl;transform:rotate(180deg);font-size:.65rem;font-weight:700;letter-spacing:.06em;color:rgba(255,255,255,.85);max-height:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.vt-stub-num{font-size:.7rem;font-weight:800;background:rgba(255,255,255,.25);border-radius:999px;padding:2px 6px;}
.vt-main{flex:1;background:linear-gradient(135deg,#1e0050 0%,#2d0070 60%,#1a0045 100%);padding:22px 24px;display:flex;flex-direction:column;gap:14px;}
.vt-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;}
.vt-event-name{font-size:1.25rem;font-weight:900;color:#fff;line-height:1.2;}
.vt-artist{font-size:.82rem;color:#d8b4fe;margin-top:3px;}
.vt-valid-badge{flex-shrink:0;background:rgba(74,222,128,.20);border:1px solid rgba(74,222,128,.45);color:#86efac;padding:4px 12px;border-radius:999px;font-size:.7rem;font-weight:800;letter-spacing:.06em;white-space:nowrap;}
.vt-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px 16px;}
.vt-info-cell{display:flex;flex-direction:column;}
.vt-label{font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;color:#a78bfa;font-weight:700;}
.vt-value{font-size:.82rem;font-weight:600;color:#fff;margin-top:1px;}
.vt-ticket-id{display:inline-block;background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.30);color:#c084fc;padding:4px 14px;border-radius:8px;font-size:.72rem;letter-spacing:.04em;align-self:flex-start;}
.vt-tear{width:22px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;background:linear-gradient(135deg,#1e0050,#2d0070);position:relative;}
.vt-circle{width:18px;height:18px;background:#13003a;border-radius:50%;position:absolute;left:50%;transform:translateX(-50%);z-index:2;}
.vt-circle.top{top:-9px;}.vt-circle.bottom{bottom:-9px;}
.vt-dashes{width:0;height:100%;border-left:2px dashed rgba(168,85,247,.40);margin:0 auto;}
.vt-barcode-section{width:160px;flex-shrink:0;background:linear-gradient(135deg,#160040,#1e0050);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:18px 14px;gap:14px;}
.vt-barcode-wrap{display:flex;flex-direction:column;align-items:center;gap:6px;}
.vt-barcode{display:flex;align-items:flex-end;gap:1.5px;height:70px;padding:4px;background:#fff;border-radius:4px;}
.vt-bar{background:#1a0045;border-radius:1px;flex-shrink:0;}
.vt-barcode-num{font-size:.5rem;color:#a78bfa;letter-spacing:.03em;text-align:center;word-break:break-all;max-width:130px;}
.vt-qr-wrap{display:flex;flex-direction:column;align-items:center;gap:4px;}
.vt-qr{width:72px;height:72px;background:#fff;border-radius:8px;padding:6px;display:flex;align-items:center;justify-content:center;}
.vt-qr-inner{width:100%;height:100%;background:repeating-linear-gradient(0deg,#1a0045 0,#1a0045 2px,white 2px,white 5px),repeating-linear-gradient(90deg,#1a0045 0,#1a0045 2px,white 2px,white 5px);border-radius:3px;position:relative;}
.vt-qr-c{position:absolute;width:16px;height:16px;background:#1a0045;border:2.5px solid white;}
.vt-qr-c.tl{top:0;left:0;border-radius:2px 0 0 0;}.vt-qr-c.tr{top:0;right:0;border-radius:0 2px 0 0;}.vt-qr-c.bl{bottom:0;left:0;border-radius:0 0 0 2px;}
.vt-qr-dots{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:14px;height:14px;background:#1a0045;border-radius:2px;}
.vt-qr-label{font-size:.6rem;color:#a78bfa;font-weight:700;letter-spacing:.1em;}
@media print{
  .no-print,.app-navbar,nav{display:none!important;}
  body{background:#fff!important;}
  .vt-wrap{background:white!important;border:2px solid #a855f7!important;box-shadow:none!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
  .vt-main{background:linear-gradient(135deg,#1e0050,#2d0070)!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
  .vt-stub{background:linear-gradient(180deg,#a855f7,#7c3aed,#f472b6)!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
  .vt-ticket{page-break-inside:avoid;margin-bottom:24px;}
  .vt-ticket+.vt-ticket{page-break-before:always;}
}
@media(max-width:600px){.vt-info-grid{grid-template-columns:1fr 1fr;}.vt-barcode-section{width:120px;}.vt-stub{width:48px;}}
</style>
<?php include __DIR__ . '/../includes/footer.php'; ?>
