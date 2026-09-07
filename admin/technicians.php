<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Technician.php';

$db = (new Database())->connect();
$adminUser = getCurrentAdmin();
$msg = '';

// Handle add technician
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_tech') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $skill = trim($_POST['skill']);
    $status = $_POST['status'] ?? 'ว่าง';
    $experience = trim($_POST['experience'] ?? 'ประสบการณ์ 5 ปี');
    $rating = (float)($_POST['rating'] ?? 4.9);

    $stmt = $db->prepare("INSERT INTO technicians (name, phone, skill, status, rating, experience) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $skill, $status, $rating, $experience]);
    $msg = "เพิ่มช่างแอร์ '$name' เรียบร้อยแล้ว";
}

// Handle toggle status
if (isset($_GET['toggle_id'])) {
    $toggleId = (int)$_GET['toggle_id'];
    $currentStatus = $db->query("SELECT status FROM technicians WHERE id = $toggleId")->fetchColumn();
    $newStatus = ($currentStatus === 'ว่าง') ? 'ไม่ว่าง' : 'ว่าง';
    $db->prepare("UPDATE technicians SET status = ? WHERE id = ?")->execute([$newStatus, $toggleId]);
    header('Location: technicians.php');
    exit;
}

// Fetch technicians with stats
$sql = "
    SELECT t.*,
           COUNT(so.id) AS total_orders,
           SUM(CASE WHEN so.status = 'เสร็จสิ้น' THEN 1 ELSE 0 END) AS completed_orders
    FROM technicians t
    LEFT JOIN service_orders so ON so.technician_id = t.id
    GROUP BY t.id
    ORDER BY t.id ASC
";
$techs = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>จัดการทีมช่างแอร์ • AirCare Pro</title>
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
      <a href="technicians.php" class="active"><span class="nav-icon">🔧</span><span>จัดการช่างแอร์</span></a>
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
        <div class="eyebrow-tag"><span>●</span> TECHNICIAN ROSTER & CAPACITY</div>
        <h1>จัดการทีมช่างแอร์มืออาชีพ</h1>
        <p class="top-bar-sub">ดูแลรายชื่อช่าง ความเชี่ยวชาญ และสถานะความพร้อมในการออกงาน</p>
      </div>
      <div class="top-bar-actions">
        <button onclick="document.getElementById('addTechModal').classList.add('active')" class="btn btn-primary">
          <span>＋</span> เพิ่มช่างแอร์คนใหม่
        </button>
      </div>
    </header>

    <?php if ($msg): ?>
      <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 18px; border-radius:10px; margin-bottom:20px;">
        ✓ <?=htmlspecialchars($msg)?>
      </div>
    <?php endif; ?>

    <div class="panel">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>รหัส</th>
              <th>ชื่อช่าง</th>
              <th>เบอร์โทรศัพท์</th>
              <th>ความเชี่ยวชาญ</th>
              <th>ประสบการณ์</th>
              <th>คะแนนรีวิว</th>
              <th>งานทั้งหมด</th>
              <th>สถานะความพร้อม</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($techs as $t): ?>
              <?php
                $techObj = new Technician(
                  $t['id'],
                  $t['name'],
                  $t['phone'] ?? '',
                  $t['skill'] ?? '',
                  $t['status'] ?? 'ว่าง',
                  (float)($t['rating'] ?? 4.9),
                  $t['experience'] ?? '',
                  $t['avatar'] ?? ''
                );
              ?>
              <tr>
                <td><b>#<?=$techObj->getId()?></b></td>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:grid; place-items:center; font-weight:700;">
                      <?=mb_substr($techObj->getName(), 0, 1, 'UTF-8')?>
                    </div>
                    <b><?=htmlspecialchars($techObj->getName())?></b>
                  </div>
                </td>
                <td><?=htmlspecialchars($techObj->getPhone() ?: '-')?></td>
                <td>
                  <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                    <?=htmlspecialchars($techObj->getSkill())?>
                  </span>
                </td>
                <td><small style="color:#64748b;"><?=htmlspecialchars($techObj->getExperience())?></small></td>
                <td>
                  <span style="color:#f59e0b; font-weight:bold;">★ <?=number_format($techObj->getRating(), 1)?></span>
                </td>
                <td>
                  <b><?=number_format($t['total_orders'])?> งาน</b>
                  <div style="font-size:11px; color:#15803d;">เสร็จ <?=number_format($t['completed_orders'])?></div>
                </td>
                <td>
                  <?=$techObj->getStatusBadge()?>
                </td>
                <td>
                  <a href="technicians.php?toggle_id=<?=$techObj->getId()?>" class="btn btn-secondary btn-sm" title="สลับสถานะ ว่าง/ไม่ว่าง">
                    <?=$techObj->isAvailable() ? 'ตั้งเป็นไม่ว่าง' : 'ตั้งเป็นว่าง'?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Add New Technician -->
<div id="addTechModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 style="font-size:18px; font-weight:700;">＋ เพิ่มช่างแอร์ใหม่</h3>
      <button class="modal-close" onclick="document.getElementById('addTechModal').classList.remove('active')">&times;</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="add_tech">
      <div class="field" style="margin-bottom:14px;">
        <label>ชื่อ-นามสกุล / ชื่อช่าง</label>
        <input type="text" name="name" placeholder="เช่น ช่างสมชาย เชี่ยวชาญแอร์" required>
      </div>
      <div class="field" style="margin-bottom:14px;">
        <label>เบอร์โทรศัพท์ติดต่อ</label>
        <input type="text" name="phone" placeholder="08xxxxxxxx" required>
      </div>
      <div class="field" style="margin-bottom:14px;">
        <label>ความเชี่ยวชาญ / ทักษะ</label>
        <input type="text" name="skill" placeholder="เช่น ล้างแอร์บ้าน, แอร์ Inverter, แอร์สำนักงาน" required>
      </div>
      <div class="field" style="margin-bottom:14px;">
        <label>ประสบการณ์การทำงาน</label>
        <input type="text" name="experience" placeholder="เช่น ประสบการณ์ 6 ปี เชี่ยวชาญระบบแอร์ฟอกอากาศ" value="ประสบการณ์ 5 ปี">
      </div>
      <div class="form-grid" style="margin-bottom:20px;">
        <div class="field">
          <label>คะแนนรีวิวเริ่มต้น</label>
          <input type="number" step="0.1" min="1" max="5" name="rating" value="4.9" required>
        </div>
        <div class="field">
          <label>สถานะเริ่มต้น</label>
          <select name="status">
            <option value="ว่าง">ว่าง (พร้อมรับงาน)</option>
            <option value="ไม่ว่าง">ไม่ว่าง / คิวเต็ม</option>
          </select>
        </div>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addTechModal').classList.remove('active')">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">บันทึกข้อมูลช่าง</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
