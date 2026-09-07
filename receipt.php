<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/ServiceOrder.php';
require_once __DIR__ . '/classes/Customer.php';
require_once __DIR__ . '/classes/Technician.php';
require_once __DIR__ . '/classes/Payment.php';

$db = (new Database())->connect();
$code = trim($_GET['code'] ?? '');
$id = (int)($_GET['id'] ?? 0);

if (empty($code) && $id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("
    SELECT so.*, 
           c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email, c.address AS customer_address,
           t.name AS technician_name, t.phone AS technician_phone, t.skill AS technician_skill,
           p.amount AS payment_amount, p.payment_method, p.payment_status, p.payment_date,
           sr.cleaning_result, sr.gas_psi_before, sr.gas_psi_after, sr.electric_current
    FROM service_orders so
    JOIN customers c ON c.id = so.customer_id
    LEFT JOIN technicians t ON t.id = so.technician_id
    LEFT JOIN payments p ON p.order_id = so.id
    LEFT JOIN service_reports sr ON sr.order_id = so.id
    WHERE so.booking_code = ? OR so.id = ?
    LIMIT 1
");
$stmt->execute([$code, $id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("ไม่พบเอกสารใบเสร็จหรือรหัสการจองดังกล่าว กรุณาตรวจสอบรหัสอีกครั้ง");
}

$units = max(1, (int)($order['units'] ?? 1));
$acType = $order['ac_type'] ?: 'แอร์ติดผนัง';
$totalPrice = (float)$order['total_price'];
$invoiceNo = 'INV-' . date('Ym', strtotime($order['created_at'] ?? 'now')) . '-' . str_pad((string)$order['id'], 5, '0', STR_PAD_LEFT);
$subdistrict = $order['subdistrict'] ?: 'ต.บ้านโป่ง';
$lat = $order['latitude'] ?: 13.8164;
$lng = $order['longitude'] ?: 99.8774;
$mapUrl = $order['map_url'] ?: "https://www.google.com/maps?q={$lat},{$lng}";
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>บิลการจองและใบเสร็จรับเงิน • <?=$order['booking_code']?> • AirCare Pro</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #f1f5f9;
      padding: 30px 16px;
      font-family: 'Prompt', 'Kanit', sans-serif;
    }
    .receipt-page {
      max-width: 820px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 18px;
      padding: 44px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
      border: 1px solid #e2e8f0;
      position: relative;
    }
    .receipt-actions {
      max-width: 820px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .receipt-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #0284c7;
      padding-bottom: 24px;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 20px;
    }
    .company-logo-block {
      display: flex;
      gap: 14px;
      align-items: center;
    }
    .company-logo {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: linear-gradient(135deg, #0284c7, #2563eb);
      color: #fff;
      font-size: 26px;
      font-weight: bold;
      display: grid;
      place-items: center;
    }
    .company-info h2 {
      font-size: 22px;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .company-info p {
      font-size: 12.5px;
      color: #64748b;
      line-height: 1.5;
    }
    .receipt-title-block {
      text-align: right;
    }
    .receipt-badge-title {
      font-size: 20px;
      font-weight: 800;
      color: #0284c7;
      text-transform: uppercase;
      letter-spacing: -0.5px;
    }
    .receipt-meta-row {
      font-size: 13px;
      color: #475569;
      margin-top: 4px;
    }
    .receipt-meta-row b {
      color: #0f172a;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 24px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 20px;
      margin-bottom: 28px;
    }
    .info-block h4 {
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #0284c7;
      margin-bottom: 8px;
      font-weight: 700;
    }
    .info-block p {
      font-size: 13.5px;
      color: #1e293b;
      line-height: 1.6;
      margin-bottom: 4px;
    }
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    .invoice-table th {
      background: #0284c7;
      color: #ffffff;
      padding: 12px 14px;
      font-size: 13px;
      text-align: left;
      font-weight: 600;
    }
    .invoice-table td {
      padding: 14px;
      font-size: 13.5px;
      border-bottom: 1px solid #e2e8f0;
      color: #1e293b;
    }
    .invoice-table tr:last-child td {
      border-bottom: 2px solid #cbd5e1;
    }
    .calc-row {
      display: flex;
      justify-content: flex-end;
      gap: 30px;
      font-size: 14px;
      padding: 6px 14px;
      color: #475569;
    }
    .calc-row.grand-total {
      font-size: 18px;
      font-weight: 800;
      color: #0284c7;
      border-top: 2px solid #0284c7;
      padding-top: 12px;
      margin-top: 8px;
    }
    .bottom-section {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 24px;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px dashed #cbd5e1;
    }
    .qr-payment-box {
      border: 1.5px solid #bae6fd;
      background: #f0f9ff;
      border-radius: 14px;
      padding: 16px;
      display: flex;
      gap: 16px;
      align-items: center;
    }
    .qr-mock {
      width: 90px;
      height: 90px;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      display: grid;
      place-items: center;
      font-size: 11px;
      text-align: center;
      padding: 6px;
      flex-shrink: 0;
    }
    .signature-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      text-align: center;
    }
    .sign-line {
      border-top: 1px solid #94a3b8;
      margin-top: 50px;
      padding-top: 6px;
      font-size: 12px;
      color: #475569;
    }
    .status-stamp {
      display: inline-block;
      border: 2px solid #10b981;
      color: #10b981;
      font-size: 14px;
      font-weight: 800;
      padding: 4px 14px;
      border-radius: 8px;
      text-transform: uppercase;
      letter-spacing: 1px;
      transform: rotate(-5deg);
    }
    .status-stamp.pending {
      border-color: #f59e0b;
      color: #f59e0b;
    }

    /* Print media query */
    @media print {
      body {
        background: #fff;
        padding: 0;
      }
      .receipt-actions {
        display: none !important;
      }
      .receipt-page {
        box-shadow: none;
        border: none;
        padding: 0;
        max-width: 100%;
      }
    }

    @media (max-width: 768px) {
      .info-grid, .bottom-section {
        grid-template-columns: 1fr;
      }
      .receipt-title-block {
        text-align: left;
      }
      .receipt-page {
        padding: 24px;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Bar -->
  <div class="receipt-actions">
    <a href="index.php" class="btn btn-secondary">
      <span>←</span> กลับหน้าหลัก
    </a>
    <div style="display:flex; gap:10px;">
      <a href="<?=$mapUrl?>" target="_blank" class="btn btn-secondary" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">
        <span>📍</span> แผนที่บ้านลูกค้า (อ.บ้านโป่ง) ↗
      </a>
      <button onclick="window.print()" class="btn btn-primary">
        <span>🖨️</span> พิมพ์บิล / บันทึกเป็น PDF
      </button>
    </div>
  </div>

  <!-- Main Invoice Page -->
  <div class="receipt-page">
    <!-- Header -->
    <header class="receipt-header">
      <div class="company-logo-block">
        <div class="company-logo">AC</div>
        <div class="company-info">
          <h2>AirCare Pro (สาขาบ้านโป่ง)</h2>
          <p>
            ศูนย์บริการล้าง ซ่อม บำรุงรักษาเครื่องปรับอากาศครบวงจร<br>
            142/5 ถนนทรงพล ต.บ้านโป่ง อ.บ้านโป่ง จ.ราชบุรี 70110<br>
            โทร: 032-999-888, 081-111-1111 | เลขประจำตัวผู้เสียภาษี: 0-7055-69001-23-4
          </p>
        </div>
      </div>

      <div class="receipt-title-block">
        <div class="receipt-badge-title">
          <?=$order['payment_status'] === 'ชำระแล้ว' ? 'ใบเสร็จรับเงิน / ใบกำกับภาษี' : 'ใบแจ้งหนี้ / ใบสรุปการจอง'?>
        </div>
        <div class="receipt-meta-row">เลขที่เอกสาร: <b><?=$invoiceNo?></b></div>
        <div class="receipt-meta-row">รหัสการจอง: <b style="color:#0284c7; font-size:14px;"><?=$order['booking_code']?></b></div>
        <div class="receipt-meta-row">วันที่ออกเอกสาร: <b><?=date('d/m/Y', strtotime($order['created_at'] ?? 'now'))?></b></div>
        <div style="margin-top:10px;">
          <?php if ($order['payment_status'] === 'ชำระแล้ว'): ?>
            <div class="status-stamp">✓ ชำระเงินแล้ว</div>
          <?php else: ?>
            <div class="status-stamp pending">🕒 รอชำระเงิน</div>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- Info Block: Customer & Appointment -->
    <section class="info-grid">
      <div class="info-block">
        <h4>ข้อมูลลูกค้าและสถานที่ให้บริการ</h4>
        <p><b>ชื่อลูกค้า:</b> <?=htmlspecialchars($order['customer_name'])?></p>
        <p><b>เบอร์โทรศัพท์:</b> <?=htmlspecialchars($order['customer_phone'])?></p>
        <p><b>อีเมล:</b> <?=htmlspecialchars($order['customer_email'] ?: '-')?></p>
        <p>
          <b>ที่อยู่บริการ:</b> <?=htmlspecialchars($order['customer_address'])?><br>
          <span style="color:#0284c7; font-weight:600;">📍 พิกัด: <?=htmlspecialchars($subdistrict)?> อ.บ้านโป่ง จ.ราชบุรี</span>
        </p>
      </div>

      <div class="info-block">
        <h4>ข้อมูลนัดหมายและช่างผู้ให้บริการ</h4>
        <?php
          $soObj = new ServiceOrder($order['id'], $order['customer_id'], $order['technician_id'], $order['service_date']);
        ?>
        <p><b>วันและเวลานัดหมาย:</b> <?=$soObj->getFormattedDate()?></p>
        <p>
          <b>ช่างผู้รับผิดชอบ:</b> 
          <?php if (!empty($order['technician_name'])): ?>
            <span style="color:#0284c7; font-weight:bold;">🔧 <?=htmlspecialchars($order['technician_name'])?></span>
            (โทร: <?=htmlspecialchars($order['technician_phone'] ?: '-')?>)
          <?php else: ?>
            <span>ช่างที่ระบบจัดสรรให้</span>
          <?php endif; ?>
        </p>
        <p><b>สถานะงานบริการ:</b> <?=$soObj->getStatusBadge()?></p>
        <p><b>ช่องทางชำระเงิน:</b> <?=htmlspecialchars($order['payment_method'] ?: 'พร้อมเพย์ QR Code')?></p>
      </div>
    </section>

    <!-- Itemized Service Table -->
    <table class="invoice-table">
      <thead>
        <tr>
          <th style="width:60px; text-align:center;">ลำดับ</th>
          <th>รายการบริการล้างแอร์</th>
          <th style="width:100px; text-align:center;">จำนวน</th>
          <th style="width:130px; text-align:right;">ราคา/หน่วย</th>
          <th style="width:140px; text-align:right;">จำนวนเงิน (บาท)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="text-align:center;">1</td>
          <td>
            <b>บริการล้างแอร์ <?=htmlspecialchars($acType)?></b>
            <div style="font-size:12px; color:#64748b;">
              ถอดล้างฟิลเตอร์ โบลเวอร์ แผงคอยล์เย็น-ร้อน ฉีดล้างท่อน้ำทิ้ง และฉีดสเปรย์หอมสดชื่น
            </div>
          </td>
          <td style="text-align:center;"><?=$units?> เครื่อง</td>
          <td style="text-align:right;">
            ฿<?=number_format($totalPrice / $units, 2)?>
          </td>
          <td style="text-align:right;">
            <b>฿<?=number_format($totalPrice, 2)?></b>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Calculation Totals -->
    <div style="display:flex; justify-content:flex-end;">
      <div style="width:340px;">
        <div class="calc-row">
          <span>รวมเป็นเงิน (Subtotal):</span>
          <b>฿<?=number_format($totalPrice, 2)?></b>
        </div>
        <div class="calc-row">
          <span>ส่วนลดพิเศษ (Discount):</span>
          <span>฿0.00</span>
        </div>
        <div class="calc-row">
          <span>ค่าเดินทางในเขตบ้านโป่ง:</span>
          <b style="color:#10b981;">ฟรี</b>
        </div>
        <div class="calc-row grand-total">
          <span>ยอดรวมสุทธิ (Grand Total):</span>
          <span>฿<?=number_format($totalPrice, 2)?></span>
        </div>
      </div>
    </div>

    <!-- Technical Report snippet if job completed -->
    <?php if (!empty($order['cleaning_result'])): ?>
      <div style="margin-top:24px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px;">
        <h4 style="font-size:13px; color:#15803d; font-weight:700; margin-bottom:6px;">✓ รายงานผลการให้บริการทางเทคนิค (Service Report)</h4>
        <p style="font-size:13px; color:#166534; margin:0;">
          <?=htmlspecialchars($order['cleaning_result'])?>
          <?php if (!empty($order['gas_psi_after'])): ?>
            | แรงดันน้ำยาแอร์: <b><?=htmlspecialchars(rtrim($order['gas_psi_after'], ' PSIpsi'))?> PSI</b>
          <?php endif; ?>
          <?php if (!empty($order['electric_current'])): ?>
            | กระแสไฟฟ้า: <b><?=htmlspecialchars(rtrim($order['electric_current'], ' AaAmp'))?> A</b>
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>

    <!-- Bottom: Payment QR & Signatures -->
    <footer class="bottom-section">
      <div>
        <div class="qr-payment-box">
          <div class="qr-mock">
            <span style="font-size:24px;">📱</span>
            <b>พร้อมเพย์</b>
            <span>สแกนจ่าย</span>
          </div>
          <div style="font-size:12.5px; color:#334155; line-height:1.5;">
            <b>การชำระเงินผ่านพร้อมเพย์:</b><br>
            หมายเลข: <b>0-7055-69001-23-4</b> (บจก. แอร์แคร์ โปร)<br>
            ยอดชำระ: <b style="color:#0284c7; font-size:15px;">฿<?=number_format($totalPrice, 2)?></b><br>
            <small style="color:#64748b;">(สามารถชำระเงินสดกับช่างได้หลังบริการเสร็จสิ้น)</small>
          </div>
        </div>

        <div style="font-size:11.5px; color:#64748b; margin-top:14px; line-height:1.5;">
          * เงื่อนไขการรับประกัน: รับประกันงานล้าง 30 วัน นับจากวันให้บริการ หากแอร์มีน้ำรั่วซึม ลมไม่เย็น หรือมีเสียงดังผิดปกติ ยินดีส่งช่างเข้าตรวจสอบแก้ไขฟรีภายใน 24 ชม.
        </div>
      </div>

      <div class="signature-grid">
        <div>
          <div class="sign-line">
            <b><?=htmlspecialchars($order['customer_name'])?></b><br>
            ผู้รับบริการ / ลูกค้า
          </div>
        </div>
        <div>
          <div class="sign-line">
            <b><?=htmlspecialchars($order['technician_name'] ?: 'ช่างผู้ชำนาญการ')?></b><br>
            ผู้ให้บริการ / ช่างแอร์ AirCare Pro
          </div>
        </div>
      </div>
    </footer>
  </div>

</body>
</html>
