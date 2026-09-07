<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Customer.php';

$db = (new Database())->connect();
$adminUser = getCurrentAdmin();

$search = trim($_GET['search'] ?? '');
$query = "
    SELECT c.*,
           COUNT(so.id) AS total_orders,
           COALESCE(MAX(so.service_date), '-') AS last_service
    FROM customers c
    LEFT JOIN service_orders so ON so.customer_id = c.id
    WHERE 1=1
";
$params = [];
if (!empty($search)) {
    $query .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.address LIKE ?)";
    $term = "%{$search}%";
    $params = [$term, $term, $term];
}
$query .= " GROUP BY c.id ORDER BY c.id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ฐานข้อมูลลูกค้า • AirCare Pro</title>
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
      <a href="customers.php" class="active"><span class="nav-icon">👥</span><span>ข้อมูลลูกค้า</span></a>
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
        <div class="eyebrow-tag"><span>●</span> CUSTOMER DATABASE & CRM</div>
        <h1>รายชื่อลูกค้าและการใช้บริการ</h1>
        <p class="top-bar-sub">ประวัติลูกค้า เบอร์โทรศัพท์ และสถานที่ให้บริการล้างแอร์</p>
      </div>
    </header>

    <!-- Search Box -->
    <div style="background:#fff; padding:16px 20px; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:24px;">
      <form method="get" style="display:flex; gap:12px; align-items:center;">
        <input type="text" name="search" value="<?=htmlspecialchars($search)?>" placeholder="ค้นหาชื่อลูกค้า, เบอร์โทร, ที่อยู่..." style="flex:1; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;">
        <button type="submit" class="btn btn-primary">ค้นหาลูกค้า</button>
        <?php if ($search): ?>
          <a href="customers.php" class="btn btn-secondary">ล้างค่า</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="panel">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>รหัสลูกค้า</th>
              <th>ชื่อ-นามสกุล</th>
              <th>เบอร์โทรศัพท์</th>
              <th>อีเมล</th>
              <th>ที่อยู่ / สถานที่บริการ</th>
              <th>ประวัติการจอง</th>
              <th>ใช้บริการล่าสุด</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($customers as $c): ?>
              <?php
                $custObj = new Customer(
                  $c['id'], 
                  $c['name'], 
                  $c['phone'] ?? '', 
                  $c['email'] ?? null, 
                  $c['address'] ?? '',
                  $c['subdistrict'] ?? 'ต.บ้านโป่ง',
                  (float)($c['latitude'] ?? 13.8164),
                  (float)($c['longitude'] ?? 99.8774)
                );
              ?>
              <tr>
                <td><b>#<?=$custObj->getId()?></b></td>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:34px; height:34px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:grid; place-items:center; font-weight:700; font-size:13px;">
                      <?=mb_substr($custObj->getName(), 0, 1, 'UTF-8')?>
                    </div>
                    <b><?=htmlspecialchars($custObj->getName())?></b>
                  </div>
                </td>
                <td><b><?=htmlspecialchars($custObj->getPhone())?></b></td>
                <td><?=htmlspecialchars($custObj->getEmail() ?: '-')?></td>
                <td style="max-width:280px; font-size:13px; color:#475569;">
                  <div><?=htmlspecialchars($custObj->getAddress() ?: '-')?></div>
                  <div style="margin-top:4px;">
                    <span class="badge" style="background:#fef3c7; color:#92400e; font-size:11px;">
                      📍 <?=htmlspecialchars($custObj->getSubdistrict())?> อ.บ้านโป่ง
                    </span>
                    <a href="<?=$custObj->getGoogleMapsUrl()?>" target="_blank" style="font-size:11.5px; color:#0284c7; margin-left:6px; text-decoration:none; font-weight:600;">
                      Google Maps ↗
                    </a>
                  </div>
                </td>
                <td>
                  <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                    <?=number_format($c['total_orders'])?> ครั้ง
                  </span>
                </td>
                <td>
                  <small style="color:#64748b;">
                    <?=$c['last_service'] !== '-' ? date('d/m/Y', strtotime($c['last_service'])) : '-'?>
                  </small>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($customers)): ?>
              <tr><td colspan="7" style="text-align:center; padding:35px; color:#94a3b8;">ไม่พบข้อมูลลูกค้า</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

</body>
</html>
