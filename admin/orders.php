<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/ServiceOrder.php';
require_once __DIR__ . '/../classes/Customer.php';
require_once __DIR__ . '/../classes/Technician.php';
require_once __DIR__ . '/../classes/BookingManager.php';

$db = (new Database())->connect();
$adminUser = getCurrentAdmin();
$banPongSubs = BookingManager::getBanPongSubdistricts();

$msg = $_SESSION['admin_flash_success'] ?? '';
$err = $_SESSION['admin_flash_error'] ?? '';
unset($_SESSION['admin_flash_success'], $_SESSION['admin_flash_error']);

// Handle new order submission if from admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_order') {
    $customerId = (int)$_POST['customer_id'];
    $technicianId = !empty($_POST['technician_id']) ? (int)$_POST['technician_id'] : null;
    $serviceDate = $_POST['service_date'];
    $units = max(1, (int)$_POST['units']);
    $pricePerUnit = (float)$_POST['price_per_unit'];
    $acType = $_POST['ac_type'] ?? 'แอร์ติดผนัง';
    $subdistrict = $_POST['subdistrict'] ?? 'ต.บ้านโป่ง';
    $totalPrice = $units * $pricePerUnit;
    $note = trim($_POST['note'] ?? '');
    $bookingCode = 'AC-BP-' . date('Ymd') . '-' . rand(1000, 9999);

    $preset = $banPongSubs[$subdistrict] ?? $banPongSubs['ต.บ้านโป่ง'];
    $lat = $preset['lat'];
    $lng = $preset['lng'];
    $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";

    $stmt = $db->prepare("INSERT INTO service_orders (customer_id, technician_id, service_date, status, total_price, note, booking_code, ac_type, units, subdistrict, latitude, longitude, map_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$customerId, $technicianId, $serviceDate, $technicianId ? 'นัดหมายแล้ว' : 'รอดำเนินการ', $totalPrice, $note, $bookingCode, $acType, $units, $subdistrict, $lat, $lng, $mapUrl]);
    $newId = $db->lastInsertId();

    $db->prepare("INSERT INTO payments (order_id, amount, payment_method, payment_status) VALUES (?, ?, 'เงินสด', 'รอตรวจสอบ')")->execute([$newId, $totalPrice]);
    $msg = 'สร้างรายการงานบริการเรียบร้อยแล้ว รหัส: ' . $bookingCode;
}

// Handle reassign or status change from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $techId = !empty($_POST['technician_id']) ? (int)$_POST['technician_id'] : null;
    $stmt = $db->prepare("UPDATE service_orders SET status = ?, technician_id = ? WHERE id = ?");
    $stmt->execute([$status, $techId, $orderId]);
    $msg = 'อัปเดตงานบริการ #' . $orderId . ' สำเร็จ';
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$query = "
    SELECT so.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
           t.name AS technician_name, t.phone AS technician_phone,
           p.payment_status, p.payment_method,
           sr.cleaning_result, sr.gas_psi_before, sr.gas_psi_after
    FROM service_orders so
    JOIN customers c ON c.id = so.customer_id
    LEFT JOIN technicians t ON t.id = so.technician_id
    LEFT JOIN payments p ON p.order_id = so.id
    LEFT JOIN service_reports sr ON sr.order_id = so.id
    WHERE 1=1
";
$params = [];

if (!empty($statusFilter)) {
    $query .= " AND so.status = ?";
    $params[] = $statusFilter;
}

if (!empty($search)) {
    $query .= " AND (c.name LIKE ? OR c.phone LIKE ? OR so.booking_code LIKE ? OR so.subdistrict LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$query .= " ORDER BY so.id DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// All customers & technicians for dropdowns
$customers = $db->query("SELECT id, name, phone FROM customers ORDER BY name ASC")->fetchAll();
$technicians = $db->query("SELECT id, name, skill, status FROM technicians ORDER BY name ASC")->fetchAll();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>จัดการงานบริการ • AirCare Pro (บ้านโป่ง)</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .btn-map-nav {
      background: #e0f2fe;
      color: #0284c7;
      border: 1px solid #bae6fd;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 11.5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      margin-top: 4px;
    }
    .btn-map-nav:hover {
      background: #0284c7;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="admin-wrapper">
  <!-- Sidebar -->
  <aside class="sidebar">
    <a href="index.php" class="sidebar-brand">
      <div class="brand-logo-icon">AC</div>
      <div class="brand-info">
        <b>AirCare Pro</b>
        <span>สาขาบ้านโป่ง ราชบุรี</span>
      </div>
    </a>
    <nav class="sidebar-nav">
      <span class="sidebar-label">การจัดการหลัก</span>
      <a href="index.php"><span class="nav-icon">📊</span><span>แดชบอร์ด & กราฟสถิติ</span></a>
      <a href="orders.php" class="active"><span class="nav-icon">❄️</span><span>งานบริการล้างแอร์</span></a>
      <a href="technicians.php"><span class="nav-icon">🔧</span><span>จัดการช่างแอร์</span></a>
      <a href="customers.php"><span class="nav-icon">👥</span><span>ข้อมูลลูกค้า</span></a>
      <a href="payments.php"><span class="nav-icon">💳</span><span>การชำระเงิน</span></a>
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
        <div class="eyebrow-tag"><span>●</span> BAN PONG DISTRICT • SERVICE ORDER MANAGEMENT</div>
        <h1>รายการงานบริการล้างแอร์ (อ.บ้านโป่ง)</h1>
        <p class="top-bar-sub">จัดการรายการจอง นำทางพิกัด Google Maps และบันทึกปิดงานพร้อมรายงานผลการล้าง</p>
      </div>
      <div class="top-bar-actions">
        <button onclick="document.getElementById('newOrderModal').classList.add('active')" class="btn btn-primary">
          <span>＋</span> สร้างงานบริการใหม่
        </button>
      </div>
    </header>

    <?php if ($msg): ?>
      <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 18px; border-radius:10px; margin-bottom:20px;">
        <?=htmlspecialchars($msg)?>
      </div>
    <?php endif; ?>

    <?php if ($err): ?>
      <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 18px; border-radius:10px; margin-bottom:20px;">
        <?=htmlspecialchars($err)?>
      </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div style="background:#fff; padding:16px 20px; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:24px; display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
      <form method="get" style="display:flex; gap:12px; flex:1; flex-wrap:wrap;">
        <input type="text" name="search" value="<?=htmlspecialchars($search)?>" placeholder="ค้นหาชื่อลูกค้า, เบอร์โทร, รหัสจอง, ตำบลในบ้านโป่ง..." style="flex:1; min-width:220px; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;">
        <select name="status" style="padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;">
          <option value="">-- ทุกสถานะงาน --</option>
          <option value="รอดำเนินการ" <?=$statusFilter==='รอดำเนินการ'?'selected':''?>>รอดำเนินการ</option>
          <option value="นัดหมายแล้ว" <?=$statusFilter==='นัดหมายแล้ว'?'selected':''?>>นัดหมายแล้ว</option>
          <option value="กำลังดำเนินการ" <?=$statusFilter==='กำลังดำเนินการ'?'selected':''?>>กำลังดำเนินการ</option>
          <option value="เสร็จสิ้น" <?=$statusFilter==='เสร็จสิ้น'?'selected':''?>>เสร็จสิ้น</option>
          <option value="ยกเลิก" <?=$statusFilter==='ยกเลิก'?'selected':''?>>ยกเลิก</option>
        </select>
        <button type="submit" class="btn btn-primary">ค้นหา</button>
        <?php if ($statusFilter || $search): ?>
          <a href="orders.php" class="btn btn-secondary">ล้างการค้นหา</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Orders Table -->
    <div class="panel">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>รหัสงาน</th>
              <th>ลูกค้า & พิกัดบ้านโป่ง</th>
              <th>ประเภทแอร์</th>
              <th>ช่างผู้รับผิดชอบ</th>
              <th>วันนัดหมาย</th>
              <th>ยอดเงิน</th>
              <th>สถานะงาน</th>
              <th>บิล / นำทาง</th>
              <th>จัดการ / ปิดงาน</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <?php
                $orderObj = new ServiceOrder(
                  $order['id'],
                  $order['customer_id'],
                  $order['technician_id'],
                  $order['service_date'],
                  $order['status'],
                  (float)$order['total_price'],
                  $order['note'] ?? '',
                  $order['booking_code'] ?? null,
                  $order['ac_type'] ?? 'แอร์ติดผนัง',
                  (int)($order['units'] ?? 1),
                  $order['subdistrict'] ?? 'ต.บ้านโป่ง',
                  (float)($order['latitude'] ?? 13.8164),
                  (float)($order['longitude'] ?? 99.8774),
                  $order['map_url'] ?? null
                );
              ?>
              <tr>
                <td>
                  <b><?=htmlspecialchars($orderObj->getBookingCode())?></b>
                </td>
                <td>
                  <b><?=htmlspecialchars($order['customer_name'])?></b>
                  <div style="font-size:12px; color:#64748b;"><?=htmlspecialchars($order['customer_phone'])?></div>
                  <div style="font-size:11.5px; color:#0284c7; font-weight:600; margin-top:2px;">
                    📍 <?=htmlspecialchars($orderObj->getSubdistrict())?> อ.บ้านโป่ง
                  </div>
                </td>
                <td>
                  <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                    <?=htmlspecialchars($orderObj->getAcType())?> (<?=$orderObj->getUnits()?> เครื่อง)
                  </span>
                </td>
                <td>
                  <?php if (!empty($order['technician_name'])): ?>
                    <b style="color:#0284c7;">🔧 <?=htmlspecialchars($order['technician_name'])?></b>
                  <?php else: ?>
                    <span style="color:#94a3b8; font-style:italic;">ยังไม่มอบหมาย</span>
                  <?php endif; ?>
                </td>
                <td><?=$orderObj->getFormattedDate()?></td>
                <td><b>฿<?=number_format($orderObj->getTotalPrice(), 2)?></b></td>
                <td><?=$orderObj->getStatusBadge()?></td>
                <td>
                  <div style="display:flex; flex-direction:column; gap:4px;">
                    <a href="../receipt.php?code=<?=$orderObj->getBookingCode()?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size:11.5px; padding:3px 8px;">
                      🧾 ดูบิล / ใบเสร็จ
                    </a>
                    <a href="<?=$orderObj->getMapUrl()?>" target="_blank" class="btn-map-nav">
                      📍 Google Maps ↗
                    </a>
                  </div>
                </td>
                <td>
                  <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <?php if ($order['status'] !== 'เสร็จสิ้น' && $order['status'] !== 'ยกเลิก'): ?>
                      <button type="button" class="btn btn-success btn-sm" onclick="openCompleteJobModal(<?=htmlspecialchars(json_encode($order))?>)" title="บันทึกปิดงานพร้อมรายงานผลการล้าง">
                        ✓ ปิดงาน
                      </button>
                    <?php else: ?>
                      <span style="font-size:11.5px; color:#10b981; font-weight:bold;">ปิดงานแล้ว</span>
                    <?php endif; ?>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="openEditModal(<?=htmlspecialchars(json_encode($order))?>)">
                      แก้ไข
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
              <tr><td colspan="9" style="text-align:center; padding:35px; color:#94a3b8;">ไม่พบรายการงานบริการที่ค้นหา</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Complete Job (ปิดงาน / จบงาน) -->
<div id="completeJobModal" class="modal-overlay">
  <div class="modal-content" style="max-width:580px;">
    <div class="modal-header">
      <div>
        <h3 style="font-size:19px; font-weight:700; color:#15803d;">📝 บันทึกปิดงานและรายงานผลการล้าง</h3>
        <p style="font-size:12.5px; color:#64748b; margin:0;" id="complete_job_subtitle">งาน #...</p>
      </div>
      <button class="modal-close" onclick="document.getElementById('completeJobModal').classList.remove('active')">&times;</button>
    </div>
    <form action="api_complete_job.php" method="post">
      <input type="hidden" name="order_id" id="complete_order_id">
      <input type="hidden" name="technician_id" id="complete_tech_id">

      <div class="field" style="margin-bottom:14px;">
        <label>ผลการล้างทำความสะอาด <span style="color:#ef4444;">*</span></label>
        <textarea name="cleaning_result" rows="2" placeholder="เช่น ล้างแผงคอยล์เย็น โบลเวอร์ และถาดน้ำทิ้งสะอาดสมบูรณ์ ลมแรงขึ้นชัดเจน" required>ล้างทำความสะอาดแผงคอยล์เย็น โบลเวอร์ และถาดน้ำทิ้งสะอาดสมบูรณ์ ลมเย็นฉ่ำปกติ</textarea>
      </div>

      <div class="form-grid" style="margin-bottom:14px;">
        <div class="field">
          <label>แรงดันน้ำยาแอร์ก่อนล้าง (PSI)</label>
          <input type="text" name="gas_psi_before" placeholder="เช่น 65 PSI" value="68 PSI">
        </div>
        <div class="field">
          <label>แรงดันน้ำยาแอร์หลังล้าง (PSI)</label>
          <input type="text" name="gas_psi_after" placeholder="เช่น 75 PSI" value="75 PSI">
        </div>
      </div>

      <div class="form-grid" style="margin-bottom:14px;">
        <div class="field">
          <label>กระแสไฟฟ้าขณะทำงาน (Ampere)</label>
          <input type="text" name="electric_current" placeholder="เช่น 4.2 A" value="4.5 A">
        </div>
        <div class="field">
          <label>ช่องทางการชำระเงินที่ลูกค้าจ่าย</label>
          <select name="payment_method">
            <option value="พร้อมเพย์ QR Code">พร้อมเพย์ QR Code</option>
            <option value="เงินสด">เงินสด (รับเงินหน้างาน)</option>
            <option value="โอนผ่านธนาคาร">โอนผ่านบัญชีธนาคาร</option>
          </select>
        </div>
      </div>

      <div class="field" style="margin-bottom:14px;">
        <label>ปัญหาที่ตรวจพบ (ถ้ามี)</label>
        <input type="text" name="problem_found" value="ไม่มีรอยรั่วซึม แผ่นฟิลเตอร์มีฝุ่นสะสมปานกลาง ล้างออกหมดแล้ว">
      </div>

      <div class="field" style="margin-bottom:20px;">
        <label>คำแนะนำสำหรับลูกค้า</label>
        <textarea name="recommendation" rows="2">ควรหมั่นถอดล้างแผ่นกรองฝุ่นเดือนละ 1 ครั้ง และทำการล้างใหญ่รอบถัดไปในอีก 6 เดือน</textarea>
      </div>

      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px; font-size:12.5px; color:#166534; margin-bottom:20px;">
        ✓ เมื่อกดบันทึก: สถานะงานจะกลายเป็น <b>'เสร็จสิ้น'</b>, การเงินกลายเป็น <b>'ชำระแล้ว'</b>, และช่างแอร์จะถูกปลดเป็น <b>'ว่าง'</b> พร้อมรับงานต่อไป
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('completeJobModal').classList.remove('active')">ยกเลิก</button>
        <button type="submit" class="btn btn-success" style="padding:10px 22px;">
          ✓ ยืนยันบันทึกปิดงาน
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Create New Order -->
<div id="newOrderModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 style="font-size:18px; font-weight:700;">＋ สร้างงานบริการใหม่ (อ.บ้านโป่ง)</h3>
      <button class="modal-close" onclick="document.getElementById('newOrderModal').classList.remove('active')">&times;</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="create_order">
      <div class="field" style="margin-bottom:14px;">
        <label>เลือกลูกค้า</label>
        <select name="customer_id" required>
          <?php foreach ($customers as $c): ?>
            <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?> (<?=$c['phone']?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-grid" style="margin-bottom:14px;">
        <div class="field">
          <label>ตำบลใน อ.บ้านโป่ง</label>
          <select name="subdistrict">
            <?php foreach ($banPongSubs as $subName => $subData): ?>
              <option value="<?=$subName?>"><?=$subName?> (<?=$subData['landmark']?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>ช่างผู้รับผิดชอบ</label>
          <select name="technician_id">
            <option value="">-- ยังไม่มอบหมายช่าง --</option>
            <?php foreach ($technicians as $t): ?>
              <option value="<?=$t['id']?>"><?=htmlspecialchars($t['name'])?> (<?=$t['skill']?> - <?=$t['status']?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field" style="margin-bottom:14px;">
        <label>ประเภทแอร์</label>
        <select name="ac_type">
          <option value="แอร์ติดผนัง">แอร์ติดผนัง (Wall Type)</option>
          <option value="แอร์แขวน/ตั้งพื้น">แอร์แขวน/ตั้งพื้น (Ceiling/Floor Type)</option>
          <option value="แอร์สี่ทิศทาง (Cassette)">แอร์สี่ทิศทาง (Cassette Type)</option>
        </select>
      </div>
      <div class="form-grid" style="margin-bottom:14px;">
        <div class="field">
          <label>จำนวนเครื่อง</label>
          <input type="number" name="units" min="1" value="1" required>
        </div>
        <div class="field">
          <label>ราคาต่อเครื่อง (บาท)</label>
          <input type="number" name="price_per_unit" min="0" step="50" value="500" required>
        </div>
      </div>
      <div class="field" style="margin-bottom:14px;">
        <label>วันและเวลานัดหมาย</label>
        <input type="datetime-local" name="service_date" required>
      </div>
      <div class="field" style="margin-bottom:20px;">
        <label>จุดสังเกต / หมายเหตุ</label>
        <textarea name="note" placeholder="จุดสังเกต เช่น ตรงข้าม ร.ร.สารสิทธิ์, ซอยข้างวัดบ้านโป่ง..."></textarea>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('newOrderModal').classList.remove('active')">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">บันทึกงานบริการ</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Order -->
<div id="editOrderModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 style="font-size:18px; font-weight:700;">แก้ไขสถานะและมอบหมายช่าง</h3>
      <button class="modal-close" onclick="document.getElementById('editOrderModal').classList.remove('active')">&times;</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="update_order">
      <input type="hidden" name="order_id" id="edit_order_id">
      <div class="field" style="margin-bottom:14px;">
        <label>สถานะงาน</label>
        <select name="status" id="edit_status">
          <option value="รอดำเนินการ">รอดำเนินการ</option>
          <option value="นัดหมายแล้ว">นัดหมายแล้ว</option>
          <option value="กำลังดำเนินการ">กำลังดำเนินการ</option>
          <option value="เสร็จสิ้น">เสร็จสิ้น</option>
          <option value="ยกเลิก">ยกเลิก</option>
        </select>
      </div>
      <div class="field" style="margin-bottom:20px;">
        <label>มอบหมายช่างแอร์</label>
        <select name="technician_id" id="edit_technician_id">
          <option value="">-- ยังไม่มอบหมายช่าง --</option>
          <?php foreach ($technicians as $t): ?>
            <option value="<?=$t['id']?>"><?=htmlspecialchars($t['name'])?> (<?=$t['skill']?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editOrderModal').classList.remove('active')">ปิด</button>
        <button type="submit" class="btn btn-primary">อัปเดตงาน</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(order) {
  document.getElementById('edit_order_id').value = order.id;
  document.getElementById('edit_status').value = order.status;
  document.getElementById('edit_technician_id').value = order.technician_id || '';
  document.getElementById('editOrderModal').classList.add('active');
}

function openCompleteJobModal(order) {
  document.getElementById('complete_order_id').value = order.id;
  document.getElementById('complete_tech_id').value = order.technician_id || 1;
  document.getElementById('complete_job_subtitle').innerText = 'รหัสการจอง: ' + (order.booking_code || '#' + order.id) + ' • ลูกค้า: ' + order.customer_name;
  document.getElementById('completeJobModal').classList.add('active');
}
</script>

</body>
</html>
