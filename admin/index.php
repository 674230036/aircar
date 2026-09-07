<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/DashboardAnalytics.php';
require_once __DIR__ . '/../classes/ServiceOrder.php';

$db = (new Database())->connect();
$analytics = new DashboardAnalytics($db);

$kpis = $analytics->getKPIs();
$monthlyData = $analytics->getMonthlyTrends(6);
$statusData = $analytics->getStatusBreakdown();
$techPerf = $analytics->getTechnicianPerformance();
$acTypes = $analytics->getAcTypeDistribution();
$recentOrders = $analytics->getRecentOrders(7);
$adminUser = getCurrentAdmin();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard • ระบบจัดการร้านล้างแอร์ AirCare Pro</title>
  <link rel="stylesheet" href="../assets/style.css">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>

<div class="admin-wrapper">
  <!-- Sidebar Navigation -->
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
      <a href="index.php" class="active">
        <span class="nav-icon">📊</span>
        <span>แดชบอร์ด & กราฟสถิติ</span>
      </a>
      <a href="orders.php">
        <span class="nav-icon">❄️</span>
        <span>งานบริการล้างแอร์</span>
      </a>
      <a href="technicians.php">
        <span class="nav-icon">🔧</span>
        <span>จัดการช่างแอร์</span>
      </a>
      <a href="customers.php">
        <span class="nav-icon">👥</span>
        <span>ข้อมูลลูกค้า</span>
      </a>
      <a href="payments.php">
        <span class="nav-icon">💳</span>
        <span>การชำระเงิน</span>
      </a>

      <span class="sidebar-label" style="margin-top:16px;">พอร์ทัลลูกค้า</span>
      <a href="../index.php" target="_blank">
        <span class="nav-icon">🌐</span>
        <span>หน้าระบบลูกค้า (Booking) ↗</span>
      </a>
      <a href="../track.php" target="_blank">
        <span class="nav-icon">🔍</span>
        <span>หน้าเช็คสถานะลูกค้า ↗</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-snippet">
        <div class="user-snippet-info">
          <b><?=htmlspecialchars($adminUser['name'])?></b>
          <span>สิทธิ์: ผู้ดูแลระบบ</span>
        </div>
        <a href="logout.php" class="btn-logout" title="ออกจากระบบ">ออก</a>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="top-bar">
      <div>
        <div class="eyebrow-tag">
          <span>●</span> AIRCARE PRO • VISUAL DASHBOARD
        </div>
        <h1>แดชบอร์ดและการวิเคราะห์ข้อมูล</h1>
        <p class="top-bar-sub">ภาพรวมตัวเลขสถิติ ผลงานช่าง ยอดรายรับ และสถานะงานบริการล้างแอร์</p>
      </div>
      <div class="top-bar-actions">
        <a href="../index.php" target="_blank" class="btn btn-secondary">
          <span>🌐</span> ดูหน้าลูกค้า
        </a>
        <a href="orders.php?add=1" class="btn btn-primary">
          <span>＋</span> สร้างงานบริการใหม่
        </a>
      </div>
    </header>

    <!-- Top KPI Cards -->
    <section class="stats-grid">
      <!-- KPI 1: Revenue -->
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-blue">฿</div>
        <div class="stat-info">
          <small>ยอดรายรับรวมทั้งหมด</small>
          <div class="stat-number">฿<?=number_format($kpis['total_revenue'], 2)?></div>
          <div class="stat-badge stat-badge-up">
            <span>✓</span> ชำระแล้ว ฿<?=number_format($kpis['paid_revenue'], 2)?>
          </div>
        </div>
      </div>

      <!-- KPI 2: Total Orders -->
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-emerald">❄️</div>
        <div class="stat-info">
          <small>งานบริการสะสม</small>
          <div class="stat-number"><?=number_format($kpis['total_orders'])?><span class="stat-unit">งาน</span></div>
          <div class="stat-badge stat-badge-up">
            <span>✓</span> สำเร็จแล้ว <?=number_format($kpis['completed_orders'])?> งาน
          </div>
        </div>
      </div>

      <!-- KPI 3: Available Technicians -->
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-amber">🔧</div>
        <div class="stat-info">
          <small>ช่างแอร์พร้อมรับงาน</small>
          <div class="stat-number"><?=$kpis['available_technicians']?> / <?=$kpis['total_technicians']?><span class="stat-unit">คน</span></div>
          <div class="stat-badge stat-badge-neutral">
            <span>●</span> สถานะว่างพร้อมออกงาน
          </div>
        </div>
      </div>

      <!-- KPI 4: Pending Bookings -->
      <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-violet">🕒</div>
        <div class="stat-info">
          <small>งานรอดำเนินการ / นัดหมาย</small>
          <div class="stat-number"><?=number_format($kpis['pending_orders'])?><span class="stat-unit">รายการ</span></div>
          <div class="stat-badge" style="background:#fef3c7; color:#b45309;">
            <span>⚡</span> รอคอนเฟิร์มและส่งช่าง
          </div>
        </div>
      </div>
    </section>

    <!-- Charts Row 1: Monthly Trends & Status Distribution -->
    <section class="charts-grid-top">
      <!-- Chart 1: Monthly Revenue & Order Volume -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <h3>แนวโน้มรายรับและจำนวนงานบริการรายเดือน</h3>
            <p>สถิติเปรียบเทียบการเติบโตของงานล้างแอร์รอบ 6 เดือนล่าสุด</p>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="monthlyTrendChart"></canvas>
        </div>
      </div>

      <!-- Chart 2: Status Breakdown -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <h3>สัดส่วนสถานะงานบริการ</h3>
            <p>การกระจายตัวของสถานะงานในระบบ</p>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="statusDonutChart"></canvas>
        </div>
      </div>
    </section>

    <!-- Charts Row 2: Technician Performance & AC Types -->
    <section class="charts-grid-bottom">
      <!-- Chart 3: Tech Workload -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <h3>สถิติผลงานช่างแอร์แต่ละคน</h3>
            <p>เปรียบเทียบจำนวนงานล้างแอร์ที่ทำสำเร็จและรายรับที่สร้างได้</p>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="techPerfChart"></canvas>
        </div>
      </div>

      <!-- Chart 4: AC Types Breakdown -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <h3>สัดส่วนประเภทเครื่องปรับอากาศยอดนิยม</h3>
            <p>ประเภทแอร์ที่ลูกค้าทำการจองบริการมากที่สุด</p>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="acTypeChart"></canvas>
        </div>
      </div>
    </section>

    <!-- Recent Orders Panel with Quick Status Update -->
    <section class="panel">
      <div class="panel-head">
        <div>
          <h2>รายการงานบริการล่าสุด</h2>
          <p>ตรวจสอบและอัปเดตสถานะการดำเนินงานของช่าง</p>
        </div>
        <a href="orders.php" class="btn btn-secondary btn-sm">ดูงานบริการทั้งหมด →</a>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>รหัสการจอง</th>
              <th>ลูกค้า & พิกัดบ้านโป่ง</th>
              <th>ช่างผู้รับผิดชอบ</th>
              <th>วันและเวลานัด</th>
              <th>สถานะงาน</th>
              <th>ยอดเงิน</th>
              <th>บิล / นำทาง</th>
              <th>จัดการ / ปิดงาน</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentOrders as $order): ?>
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
                <td><b><?=htmlspecialchars($orderObj->getBookingCode())?></b></td>
                <td>
                  <div><b><?=htmlspecialchars($order['customer_name'])?></b></div>
                  <small style="color:#64748b;"><?=htmlspecialchars($order['customer_phone'])?></small>
                  <div style="font-size:11.5px; color:#0284c7; font-weight:600;">
                    📍 <?=htmlspecialchars($orderObj->getSubdistrict())?> อ.บ้านโป่ง
                  </div>
                </td>
                <td>
                  <?php if (!empty($order['technician_name'])): ?>
                    <span style="font-weight:500; color:#0284c7;">🔧 <?=htmlspecialchars($order['technician_name'])?></span>
                  <?php else: ?>
                    <span style="color:#94a3b8; font-style:italic;">ยังไม่ระบุช่าง</span>
                  <?php endif; ?>
                </td>
                <td><?=$orderObj->getFormattedDate()?></td>
                <td><?=$orderObj->getStatusBadge()?></td>
                <td><b>฿<?=number_format($orderObj->getTotalPrice(), 2)?></b></td>
                <td>
                  <div style="display:flex; flex-direction:column; gap:4px;">
                    <a href="../receipt.php?code=<?=$orderObj->getBookingCode()?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size:11.5px; padding:3px 8px;">
                      🧾 ดูบิล
                    </a>
                    <a href="<?=$orderObj->getMapUrl()?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size:11px; padding:2px 6px; background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">
                      📍 Maps ↗
                    </a>
                  </div>
                </td>
                <td>
                  <div style="display:flex; gap:6px; align-items:center;">
                    <?php if ($order['status'] !== 'เสร็จสิ้น' && $order['status'] !== 'ยกเลิก'): ?>
                      <button type="button" class="btn btn-success btn-sm" onclick="openCompleteJobModal(<?=htmlspecialchars(json_encode($order))?>)" title="บันทึกปิดงานพร้อมรายงานผลการล้าง">
                        ✓ ปิดงาน
                      </button>
                    <?php else: ?>
                      <span style="font-size:11.5px; color:#10b981; font-weight:bold;">ปิดแล้ว</span>
                    <?php endif; ?>

                    <select class="quick-status-select" 
                            data-order-id="<?=$order['id']?>" 
                            onchange="changeOrderStatus(this, <?=$order['id']?>)"
                            style="padding:5px 8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; font-family:inherit; background:#fff;">
                      <option value="รอดำเนินการ" <?=$order['status']==='รอดำเนินการ'?'selected':''?>>รอดำเนินการ</option>
                      <option value="นัดหมายแล้ว" <?=$order['status']==='นัดหมายแล้ว'?'selected':''?>>นัดหมายแล้ว</option>
                      <option value="กำลังดำเนินการ" <?=$order['status']==='กำลังดำเนินการ'?'selected':''?>>กำลังดำเนินการ</option>
                      <option value="เสร็จสิ้น" <?=$order['status']==='เสร็จสิ้น'?'selected':''?>>เสร็จสิ้น</option>
                      <option value="ยกเลิก" <?=$order['status']==='ยกเลิก'?'selected':''?>>ยกเลิก</option>
                    </select>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($recentOrders)): ?>
              <tr>
                <td colspan="8" style="text-align:center; padding:35px; color:#94a3b8;">ยังไม่มีรายการงานบริการในระบบ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<!-- Modal: Complete Job (ปิดงาน / จบงาน) -->
<div id="completeJobModal" class="modal-overlay">
  <div class="modal-content" style="max-width:560px;">
    <div class="modal-header">
      <div>
        <h3 style="font-size:18px; font-weight:700; color:#15803d;">📝 บันทึกปิดงานและรายงานผลการล้าง</h3>
        <p style="font-size:12px; color:#64748b; margin:0;" id="complete_job_subtitle">งาน #...</p>
      </div>
      <button class="modal-close" onclick="document.getElementById('completeJobModal').classList.remove('active')">&times;</button>
    </div>
    <form action="api_complete_job.php" method="post">
      <input type="hidden" name="order_id" id="complete_order_id">
      <input type="hidden" name="technician_id" id="complete_tech_id">

      <div class="field" style="margin-bottom:12px;">
        <label>ผลการล้างทำความสะอาด <span style="color:#ef4444;">*</span></label>
        <textarea name="cleaning_result" rows="2" required>ล้างทำความสะอาดแผงคอยล์เย็น โบลเวอร์ และถาดน้ำทิ้งสะอาดสมบูรณ์ ลมเย็นฉ่ำปกติ</textarea>
      </div>

      <div class="form-grid" style="margin-bottom:12px;">
        <div class="field">
          <label>แรงดันน้ำยาแอร์ก่อนล้าง (PSI)</label>
          <input type="text" name="gas_psi_before" value="68 PSI">
        </div>
        <div class="field">
          <label>แรงดันน้ำยาแอร์หลังล้าง (PSI)</label>
          <input type="text" name="gas_psi_after" value="75 PSI">
        </div>
      </div>

      <div class="form-grid" style="margin-bottom:12px;">
        <div class="field">
          <label>กระแสไฟ (Ampere)</label>
          <input type="text" name="electric_current" value="4.5 A">
        </div>
        <div class="field">
          <label>ช่องทางชำระเงินที่รับ</label>
          <select name="payment_method">
            <option value="พร้อมเพย์ QR Code">พร้อมเพย์ QR Code</option>
            <option value="เงินสด">เงินสด (รับเงินหน้างาน)</option>
            <option value="โอนผ่านธนาคาร">โอนผ่านบัญชีธนาคาร</option>
          </select>
        </div>
      </div>

      <div class="field" style="margin-bottom:12px;">
        <label>ปัญหาที่ตรวจพบ</label>
        <input type="text" name="problem_found" value="ไม่มีรอยรั่วซึม แผ่นฟิลเตอร์มีฝุ่นสะสมปานกลาง ล้างออกหมดแล้ว">
      </div>

      <div class="field" style="margin-bottom:16px;">
        <label>คำแนะนำสำหรับลูกค้า</label>
        <textarea name="recommendation" rows="2">ควรหมั่นถอดล้างแผ่นกรองฝุ่นเดือนละ 1 ครั้ง และทำการล้างใหญ่รอบถัดไปในอีก 6 เดือน</textarea>
      </div>

      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:10px; font-size:12px; color:#166534; margin-bottom:16px;">
        ✓ เมื่อกดบันทึก: สถานะงานกลายเป็น <b>'เสร็จสิ้น'</b>, การเงินเป็น <b>'ชำระแล้ว'</b>, ช่างเป็น <b>'ว่าง'</b> พร้อมแสดงรายงานในหน้าติดตามสถานะ
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('completeJobModal').classList.remove('active')">ยกเลิก</button>
        <button type="submit" class="btn btn-success" style="padding:10px 20px;">
          ✓ ยืนยันปิดงาน
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Dynamic Chart.js Script with Modern Styling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Chart Colors & Config
  const primaryBlue = '#0284c7';
  const accentBlue = '#2563eb';
  const cyanTeal = '#06b6d4';
  const fontConfig = { family: 'Prompt, Kanit, sans-serif' };

  // 1. Monthly Trends Chart (Combined Bar & Line)
  const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
  const gradientRevenue = monthlyCtx.createLinearGradient(0, 0, 0, 260);
  gradientRevenue.addColorStop(0, 'rgba(2, 132, 199, 0.35)');
  gradientRevenue.addColorStop(1, 'rgba(2, 132, 199, 0.02)');

  new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: <?=json_encode($monthlyData['labels'], JSON_UNESCAPED_UNICODE)?>,
      datasets: [
        {
          label: 'รายรับ (บาท)',
          data: <?=json_encode($monthlyData['revenue'])?>,
          borderColor: primaryBlue,
          backgroundColor: gradientRevenue,
          fill: true,
          tension: 0.35,
          borderWidth: 3,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: primaryBlue,
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
          yAxisID: 'y'
        },
        {
          label: 'จำนวนงาน (รายการ)',
          data: <?=json_encode($monthlyData['orders'])?>,
          borderColor: '#10b981',
          backgroundColor: '#10b981',
          type: 'bar',
          borderRadius: 6,
          barThickness: 24,
          yAxisID: 'y1'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: { font: fontConfig, boxWidth: 14 }
        },
        tooltip: {
          padding: 12,
          boxPadding: 6,
          titleFont: fontConfig,
          bodyFont: fontConfig
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: fontConfig }
        },
        y: {
          type: 'linear',
          position: 'left',
          grid: { color: '#f1f5f9' },
          ticks: {
            font: fontConfig,
            callback: value => '฿' + value.toLocaleString()
          }
        },
        y1: {
          type: 'linear',
          position: 'right',
          grid: { display: false },
          ticks: {
            font: fontConfig,
            stepSize: 1
          }
        }
      }
    }
  });

  // 2. Status Breakdown Donut Chart
  const statusCtx = document.getElementById('statusDonutChart').getContext('2d');
  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: <?=json_encode($statusData['labels'], JSON_UNESCAPED_UNICODE)?>,
      datasets: [{
        data: <?=json_encode($statusData['data'])?>,
        backgroundColor: <?=json_encode($statusData['colors'])?>,
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: fontConfig, boxWidth: 12, padding: 12 }
        }
      }
    }
  });

  // 3. Technician Workload Bar Chart
  const techCtx = document.getElementById('techPerfChart').getContext('2d');
  new Chart(techCtx, {
    type: 'bar',
    data: {
      labels: <?=json_encode($techPerf['names'], JSON_UNESCAPED_UNICODE)?>,
      datasets: [
        {
          label: 'งานที่เสร็จสิ้น',
          data: <?=json_encode($techPerf['completed'])?>,
          backgroundColor: '#0284c7',
          borderRadius: 8
        },
        {
          label: 'งานทั้งหมดที่ได้รับ',
          data: <?=json_encode($techPerf['total'])?>,
          backgroundColor: '#cbd5e1',
          borderRadius: 8
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: { font: fontConfig, boxWidth: 14 }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: fontConfig }
        },
        y: {
          grid: { color: '#f1f5f9' },
          ticks: { font: fontConfig, stepSize: 1 }
        }
      }
    }
  });

  // 4. AC Type Distribution
  const acTypeCtx = document.getElementById('acTypeChart').getContext('2d');
  new Chart(acTypeCtx, {
    type: 'polarArea',
    data: {
      labels: <?=json_encode($acTypes['labels'], JSON_UNESCAPED_UNICODE)?>,
      datasets: [{
        data: <?=json_encode($acTypes['data'])?>,
        backgroundColor: [
          'rgba(2, 132, 199, 0.75)',
          'rgba(13, 148, 136, 0.75)',
          'rgba(99, 102, 241, 0.75)',
          'rgba(245, 158, 11, 0.75)'
        ],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: fontConfig, boxWidth: 12 }
        }
      },
      scales: {
        r: {
          ticks: { display: false }
        }
      }
    }
  });
});

// Quick status change handler
function changeOrderStatus(selectEl, orderId) {
  const newStatus = selectEl.value;
  const originalBg = selectEl.style.backgroundColor;
  selectEl.style.backgroundColor = '#fef3c7';

  const formData = new FormData();
  formData.append('order_id', orderId);
  formData.append('status', newStatus);

  fetch('api_update_status.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      selectEl.style.backgroundColor = '#dcfce7';
      setTimeout(() => {
        location.reload();
      }, 400);
    } else {
      alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถอัปเดตสถานะได้'));
      selectEl.style.backgroundColor = originalBg;
    }
  })
  .catch(err => {
    alert('เชื่อมต่อเซิร์ฟเวอร์ล้มเหลว');
    selectEl.style.backgroundColor = originalBg;
  });
function openCompleteJobModal(order) {
  document.getElementById('complete_order_id').value = order.id;
  document.getElementById('complete_tech_id').value = order.technician_id || 1;
  document.getElementById('complete_job_subtitle').innerText = 'รหัสการจอง: ' + (order.booking_code || '#' + order.id) + ' • ลูกค้า: ' + order.customer_name;
  document.getElementById('completeJobModal').classList.add('active');
}
</script>

</body>
</html>
