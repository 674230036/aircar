<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/BookingManager.php';
require_once __DIR__ . '/classes/ServiceOrder.php';

$db = (new Database())->connect();
$bookingManager = new BookingManager($db);

$keyword = trim($_GET['q'] ?? '');
$orders = [];
if (!empty($keyword)) {
    $orders = $bookingManager->findBookingForCustomer($keyword);
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
  <title>ตรวจสอบสถานะการจอง • AirCare Pro (บ้านโป่ง)</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/customer.css">
</head>
<body class="customer-body">

  <!-- Header -->
  <header class="customer-header">
    <div class="nav-container">
      <a href="index.php" class="brand-wrapper">
        <div class="brand-icon">❄️</div>
        <div class="brand-text">
          <b>AirCare Pro</b>
          <span>สาขาบ้านโป่ง ราชบุรี</span>
        </div>
      </a>
      <nav class="nav-links">
        <a href="index.php">หน้าแรก</a>
        <a href="index.php#technicians">ทีมช่างมืออาชีพ</a>
        <a href="index.php#booking-form">จองคิวล้างแอร์</a>
        <a href="track.php" class="active">ตรวจสอบสถานะ</a>
      </nav>
      <div class="nav-action-group">
        <a href="index.php#booking-form" class="btn-book-nav">＋ จองคิวทันที</a>
        <a href="admin/login.php" class="btn-admin-gate" title="สำหรับผู้ดูแลระบบ">
          <span>🔐</span> สำหรับเจ้าหน้าที่
        </a>
      </div>
    </div>
  </header>

  <!-- Track Section -->
  <main class="track-section">
    <div style="text-align:center; margin-bottom:36px;">
      <span class="section-tag">REAL-TIME ORDER TRACKING • BAN PONG</span>
      <h1 style="font-size:32px; font-weight:700; color:#0f172a; margin-bottom:10px;">ตรวจสอบสถานะคิวล้างแอร์</h1>
      <p style="color:#64748b; font-size:15.5px;">กรอกเบอร์โทรศัพท์ที่ใช้จอง หรือรหัสการจอง เพื่อติดตามสถานะและดูรายงานผลการล้างของช่าง</p>
    </div>

    <!-- Search Box -->
    <div class="track-search-box">
      <form method="get" class="track-form">
        <input type="text" name="q" value="<?=htmlspecialchars($keyword)?>" class="track-input" placeholder="กรอกเบอร์โทรศัพท์ เช่น 0812345678 หรือรหัสจอง เช่น AC-BP-..." required autofocus>
        <button type="submit" class="btn-hero-primary" style="padding:14px 28px; border:none; cursor:pointer;">
          <span>🔍</span> ค้นหาสถานะ
        </button>
      </form>
    </div>

    <?php if (!empty($keyword) && empty($orders)): ?>
      <div class="timeline-box" style="text-align:center; padding:48px 24px;">
        <div style="font-size:48px; margin-bottom:12px;">🔍</div>
        <h3 style="font-size:18px; color:#0f172a; margin-bottom:8px;">ไม่พบข้อมูลการจอง</h3>
        <p style="color:#64748b; font-size:14px; margin-bottom:20px;">กรุณาตรวจสอบเบอร์โทรศัพท์ หรือรหัสใบจองใหม่อีกครั้ง</p>
        <a href="index.php#booking-form" class="btn btn-primary">จองคิวล้างแอร์ใหม่ →</a>
      </div>
    <?php endif; ?>

    <?php foreach ($orders as $order): ?>
      <?php
        $status = $order['status'];
        $step1Class = 'completed';
        $step2Class = in_array($status, ['นัดหมายแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น']) ? 'completed' : ($status === 'รอดำเนินการ' ? 'active' : '');
        $step3Class = in_array($status, ['กำลังดำเนินการ', 'เสร็จสิ้น']) ? 'completed' : ($status === 'นัดหมายแล้ว' ? 'active' : '');
        $step4Class = ($status === 'เสร็จสิ้น') ? 'completed' : ($status === 'กำลังดำเนินการ' ? 'active' : '');
        if ($status === 'ยกเลิก') {
          $step1Class = 'completed';
          $step2Class = ''; $step3Class = ''; $step4Class = '';
        }
        $sub = $order['subdistrict'] ?? 'ต.บ้านโป่ง';
        $lat = $order['latitude'] ?? 13.8164;
        $lng = $order['longitude'] ?? 99.8774;
        $mapUrl = $order['map_url'] ?: "https://www.google.com/maps?q={$lat},{$lng}";
        $soObj = new ServiceOrder($order['id'], $order['customer_id'], $order['technician_id'], $order['service_date'], $order['status']);
      ?>
      <div class="timeline-box">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; border-bottom:1px solid #f1f5f9; padding-bottom:18px; margin-bottom:20px;">
          <div>
            <div style="font-size:12px; color:#0284c7; font-weight:700; letter-spacing:1px;">BOOKING REFERENCE</div>
            <h2 style="font-size:22px; font-weight:700; color:#0f172a;"><?=htmlspecialchars($order['booking_code'] ?: ('#AC-' . $order['id']))?></h2>
            <div style="font-size:13.5px; color:#64748b; margin-top:2px;">
              ผู้จอง: <b><?=htmlspecialchars($order['customer_name'])?></b> (<?=htmlspecialchars($order['customer_phone'])?>)
            </div>
            <div style="font-size:12px; color:#0284c7; font-weight:600; margin-top:2px;">
              📍 พิกัด: <?=htmlspecialchars($sub)?> อ.บ้านโป่ง จ.ราชบุรี
            </div>
          </div>
          <div style="text-align:right;">
            <div style="margin-bottom:6px;">
              <?=$soObj->getStatusBadge()?>
            </div>
            <div style="font-size:13px; color:#64748b; margin-bottom:8px;">
              ยอดรวม: <b style="color:#0284c7; font-size:16px;">฿<?=number_format((float)$order['total_price'], 2)?></b>
            </div>
            <a href="receipt.php?code=<?=htmlspecialchars($order['booking_code'])?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size:12px; padding:5px 12px;">
              🧾 ดูบิล / พิมพ์ใบเสร็จ
            </a>
          </div>
        </div>

        <?php if ($status === 'ยกเลิก'): ?>
          <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:14px; border-radius:12px; text-align:center; font-weight:600;">
            ✕ รายการจองนี้ถูกยกเลิกแล้ว หากต้องการรับบริการกรุณาจองคิวใหม่อีกครั้ง
          </div>
        <?php else: ?>
          <!-- Timeline Steps -->
          <div class="order-timeline">
            <div class="timeline-step <?=$step1Class?>">
              <div class="step-bubble">1</div>
              <div class="step-label">จองสำเร็จ</div>
            </div>
            <div class="timeline-step <?=$step2Class?>">
              <div class="step-bubble">2</div>
              <div class="step-label">ยืนยันนัดหมาย</div>
            </div>
            <div class="timeline-step <?=$step3Class?>">
              <div class="step-bubble">3</div>
              <div class="step-label">ช่างเข้าบริการ</div>
            </div>
            <div class="timeline-step <?=$step4Class?>">
              <div class="step-bubble">4</div>
              <div class="step-label">ล้างเสร็จสิ้น</div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Details Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; background:#f8fafc; padding:20px; border-radius:14px; border:1px solid #e2e8f0; margin-top:24px;">
          <div>
            <small style="color:#64748b; font-size:12px; display:block;">วันและเวลานัดหมาย</small>
            <b style="font-size:14.5px; color:#0f172a;">
              📅 <?=$soObj->getFormattedDate()?>
            </b>
            <div style="font-size:12.5px; color:#64748b; margin-top:4px;">
              ประเภท: <?=htmlspecialchars($order['ac_type'] ?: 'แอร์ติดผนัง')?> (<?=$order['units'] ?: 1?> เครื่อง)
            </div>
          </div>

          <div>
            <small style="color:#64748b; font-size:12px; display:block;">ช่างผู้รับผิดชอบงาน</small>
            <?php if (!empty($order['technician_name'])): ?>
              <b style="font-size:14.5px; color:#0284c7;">
                🔧 <?=htmlspecialchars($order['technician_name'])?>
              </b>
              <div style="font-size:12.5px; color:#64748b; margin-top:4px;">
                📞 โทร: <?=htmlspecialchars($order['technician_phone'] ?: '-')?>
              </div>
            <?php else: ?>
              <b style="font-size:14.5px; color:#d97706;">
                🕒 กำลังจัดสรรช่างบ้านโป่ง
              </b>
              <div style="font-size:12px; color:#64748b; margin-top:4px;">เจ้าหน้าที่จะโทรยืนยันก่อนเดินทาง</div>
            <?php endif; ?>
          </div>

          <div>
            <small style="color:#64748b; font-size:12px; display:block;">สถานที่ให้บริการ & Google Maps</small>
            <div style="font-size:13px; color:#334155; line-height:1.4;">
              📍 <?=htmlspecialchars($order['customer_address'])?>
            </div>
            <a href="<?=$mapUrl?>" target="_blank" style="display:inline-block; margin-top:6px; color:#0284c7; font-size:12px; text-decoration:none; font-weight:600;">
              ดูพิกัดบน Google Maps ↗
            </a>
          </div>
        </div>

        <!-- Job Completion Technical Report if completed -->
        <?php if (!empty($order['cleaning_result'])): ?>
          <div style="margin-top:20px; background:#f0fdf4; border:1.5px solid #86efac; border-radius:14px; padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap;">
              <h3 style="font-size:15px; font-weight:700; color:#15803d; margin:0;">
                ✓ รายงานผลการให้บริการและตรวจเช็คแอร์ (Service Report)
              </h3>
              <span style="font-size:12px; color:#166534;">
                ปิดงานเมื่อ: <?=!empty($order['completed_at']) ? date('d/m/Y H:i น.', strtotime($order['completed_at'])) : 'เสร็จสมบูรณ์'?>
              </span>
            </div>

            <div style="font-size:13.5px; color:#1e293b; line-height:1.7;">
              <div><b>ผลการล้าง:</b> <?=htmlspecialchars($order['cleaning_result'])?></div>
              <?php if (!empty($order['gas_psi_after'])): ?>
                <div style="color:#0369a1; margin-top:4px;">
                  <b>แรงดันน้ำยาแอร์:</b> ก่อนล้าง <?=htmlspecialchars($order['gas_psi_before'] ?: '-')?> ➔ หลังล้าง <b><?=htmlspecialchars(rtrim($order['gas_psi_after'], ' PSIpsi'))?> PSI</b> (เย็นฉ่ำปกติ)
                </div>
              <?php endif; ?>
              <?php if (!empty($order['electric_current'])): ?>
                <div style="color:#0369a1; margin-top:4px;">
                  <b>กระแสไฟฟ้า:</b> <?=htmlspecialchars(rtrim($order['electric_current'], ' AaAmp'))?> A (ประหยัดไฟปกติ)
                </div>
              <?php endif; ?>
              <?php if (!empty($order['recommendation'])): ?>
                <div style="background:#ffffff; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; margin-top:10px; color:#15803d; font-size:13px;">
                  💡 <b>คำแนะนำจากช่าง:</b> <?=htmlspecialchars($order['recommendation'])?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <div style="text-align:center; margin-top:30px;">
      <a href="index.php" style="color:#0284c7; text-decoration:none; font-size:14.5px; font-weight:600;">
        ← กลับสู่หน้าหลักจองคิวล้างแอร์บ้านโป่ง
      </a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="customer-footer">
    <div class="footer-container">
      <div class="footer-brand">
        <b>AirCare Pro • ศูนย์บริการล้างแอร์บ้านโป่ง ราชบุรี</b>
        <p style="font-size:13.5px; line-height:1.7;">
          ให้บริการล้าง ซ่อม บำรุงรักษาเครื่องปรับอากาศทุกประเภท ด้วยทีมช่างมืออาชีพผ่านการรับรอง น้ำยาฆ่าเชื้อปลอดภัย ไร้สารตกค้าง พร้อมรับประกันงาน 30 วัน
        </p>
      </div>
      <div>
        <h4 style="color:#fff; margin-bottom:14px; font-size:15px;">เมนูลัด</h4>
        <div style="display:flex; flex-direction:column; gap:8px; font-size:13.5px;">
          <a href="index.php#technicians" style="color:#94a3b8; text-decoration:none;">ทีมช่างแอร์บ้านโป่ง</a>
          <a href="index.php#booking-form" style="color:#94a3b8; text-decoration:none;">จองคิวบริการ</a>
          <a href="track.php" style="color:#94a3b8; text-decoration:none;">ตรวจสอบสถานะ</a>
        </div>
      </div>
      <div>
        <h4 style="color:#fff; margin-bottom:14px; font-size:15px;">ติดต่อสาขาบ้านโป่ง</h4>
        <div style="font-size:13.5px; line-height:1.8;">
          <div>📞 ฮอตไลน์: 032-999-888, 081-111-1111</div>
          <div>📱 LINE: @aircarebanpong</div>
          <div>📍 142/5 ถ.ทรงพล ต.บ้านโป่ง อ.บ้านโป่ง จ.ราชบุรี</div>
          <div>⏰ เปิดบริการทุกวัน 08:00 - 18:00 น.</div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© 2026 AirCare Pro. All rights reserved. System designed with OOA • OOD • OOP.</div>
      <a href="admin/login.php" style="color:#64748b; text-decoration:none;">เข้าสู่ระบบเจ้าหน้าที่ (Admin Login)</a>
    </div>
  </footer>

</body>
</html>
