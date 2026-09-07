<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Payment.php';

$db = (new Database())->connect();
$adminUser = getCurrentAdmin();
$msg = '';

// Handle mark as paid
if (isset($_GET['mark_paid'])) {
    $payId = (int)$_GET['mark_paid'];
    $db->prepare("UPDATE payments SET payment_status = 'ชำระแล้ว', payment_date = NOW() WHERE id = ?")->execute([$payId]);
    header('Location: payments.php');
    exit;
}

$sql = "
    SELECT p.*,
           so.booking_code, so.status AS order_status,
           c.name AS customer_name, c.phone AS customer_phone
    FROM payments p
    JOIN service_orders so ON so.id = p.order_id
    JOIN customers c ON c.id = so.customer_id
    ORDER BY p.id DESC
";
$payments = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$totalReceived = $db->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'ชำระแล้ว'")->fetchColumn();
$totalPending = $db->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'รอตรวจสอบ'")->fetchColumn();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>รายการชำระเงิน • AirCare Pro</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="admin-wrapper">
  <!-- Sidebar -->
  <aside class="sidebar">
    <a href="index.php" class="sidebar-brand">
      <div class="brand-logo-icon">AC</div>
      <div class="brand-info">
        <b>AirCare Pro</b>
        <span>System Admin</span>
      </div>
    </a>
    <nav class="sidebar-nav">
      <span class="sidebar-label">การจัดการหลัก</span>
      <a href="index.php"><span class="nav-icon">📊</span><span>แดชบอร์ด & กราฟสถิติ</span></a>
      <a href="orders.php"><span class="nav-icon">❄️</span><span>งานบริการล้างแอร์</span></a>
      <a href="technicians.php"><span class="nav-icon">🔧</span><span>จัดการช่างแอร์</span></a>
      <a href="customers.php"><span class="nav-icon">👥</span><span>ข้อมูลลูกค้า</span></a>
      <a href="payments.php" class="active"><span class="nav-icon">💳</span><span>การชำระเงิน</span></a>
      <span class="sidebar-label" style="margin-top:16px;">พอร์ทัลลูกค้า</span>
      <a href="../index.php" target="_blank"><span class="nav-icon">🌐</span><span>หน้าระบบลูกค้า ↗</span></a>
      <a href="../track.php" target="_blank"><span class="nav-icon">🔍</span><span>หน้าเช็คสถานะลูกค้า ↗</span></a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-snippet">
        <div class="user-snippet-info">
          <b><?=htmlspecialchars($adminUser['name'])?></b>
          <span>สิทธิ์: ผู้ดูแลระบบ</span>
        </div>
        <a href="logout.php" class="btn-logout">ออก</a>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="top-bar">
      <div>
        <div class="eyebrow-tag"><span>●</span> PAYMENT & SETTLEMENT</div>
        <h1>รายการชำระเงินและการเงิน</h1>
        <p class="top-bar-sub">ตรวจสอบยอดโอนชำระเงิน และสถานะการรับเงินของร้าน</p>
      </div>
    </header>

    <!-- Summary Cards -->
    <section class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-emerald">✓</div>
        <div class="stat-info">
          <small>ยอดเงินที่รับชำระแล้วทั้งหมด</small>
          <div class="stat-number" style="color:#15803d;">฿<?=number_format((float)$totalReceived, 2)?></div>
          <div class="stat-badge stat-badge-up">เงินเข้าบัญชีเรียบร้อย</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-amber">🕒</div>
        <div class="stat-info">
          <small>ยอดเงินที่รอตรวจสอบ / รอเก็บเงิน</small>
          <div class="stat-number" style="color:#b45309;">฿<?=number_format((float)$totalPending, 2)?></div>
          <div class="stat-badge" style="background:#fef3c7; color:#b45309;">รอช่างเก็บเงินหน้างานหรือตรวจสลิป</div>
        </div>
      </div>
    </section>

    <div class="panel">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>รหัสงาน</th>
              <th>ชื่อลูกค้า</th>
              <th>ช่องทางชำระเงิน</th>
              <th>จำนวนเงิน</th>
              <th>วันที่ชำระ</th>
              <th>สถานะการชำระ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $p): ?>
              <?php
                $payObj = new Payment(
                  $p['id'],
                  $p['order_id'],
                  (float)$p['amount'],
                  $p['payment_method'] ?? 'พร้อมเพย์ QR Code',
                  $p['payment_status'] ?? 'รอตรวจสอบ',
                  $p['payment_date']
                );
              ?>
              <tr>
                <td><b><?=htmlspecialchars($p['booking_code'] ?: ('#AC-' . $p['order_id']))?></b></td>
                <td>
                  <b><?=htmlspecialchars($p['customer_name'])?></b>
                  <div style="font-size:12px; color:#64748b;"><?=htmlspecialchars($p['customer_phone'])?></div>
                </td>
                <td>
                  <span style="font-weight:500; color:#334155;">
                    💳 <?=htmlspecialchars($payObj->getPaymentMethod())?>
                  </span>
                </td>
                <td><b style="font-size:15px; color:#0f172a;"><?=$payObj->getFormattedAmount()?></b></td>
                <td>
                  <small style="color:#64748b;">
                    <?=$p['payment_date'] ? date('d/m/Y H:i', strtotime($p['payment_date'])) : '-'?>
                  </small>
                </td>
                <td><?=$payObj->getStatusBadge()?></td>
                <td>
                  <?php if (!$payObj->isPaid()): ?>
                    <a href="payments.php?mark_paid=<?=$p['id']?>" class="btn btn-primary btn-sm" onclick="return confirm('ยืนยันว่าได้รับยอดชำระเงินแล้ว?')">
                      ยืนยันรับเงิน
                    </a>
                  <?php else: ?>
                    <span style="color:#10b981; font-size:12.5px; font-weight:600;">✓ ชำระแล้ว</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
              <tr><td colspan="7" style="text-align:center; padding:35px; color:#94a3b8;">ยังไม่มีรายการชำระเงิน</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

</body>
</html>
