<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/BookingManager.php';

$db = (new Database())->connect();
$bookingManager = new BookingManager($db);
$technicians = $bookingManager->getTechnicians();
$banPongSubs = BookingManager::getBanPongSubdistricts();

$lastBooking = $_SESSION['last_booking'] ?? null;
$bookingError = $_SESSION['booking_error'] ?? null;
unset($_SESSION['last_booking'], $_SESSION['booking_error']);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
  <title>AirCare Pro บ้านโป่ง • จองคิวล้างแอร์มืออาชีพ เลือกช่างได้ตามใจคุณ</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/customer.css">
  <style>
    /* Ban Pong Area Banner */
    .banpong-hero-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
      padding: 6px 14px;
      border-radius: 30px;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 16px;
    }
    .map-preview-container {
      margin-top: 14px;
      border-radius: 14px;
      overflow: hidden;
      border: 1.5px solid #cbd5e1;
      position: relative;
      height: 220px;
      background: #e2e8f0;
    }
    .map-preview-container iframe {
      width: 100%;
      height: 100%;
      border: 0;
    }
    .map-overlay-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(15, 23, 42, 0.85);
      color: #fff;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
      backdrop-filter: blur(4px);
    }

    /* iPad & Tablet Responsiveness */
    @media (max-width: 1024px) {
      .booking-layout {
        grid-template-columns: 1fr;
      }
      .booking-summary-card {
        position: static;
        margin-top: 24px;
      }
      .services-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .services-grid {
        grid-template-columns: 1fr;
      }
      .hero-title {
        font-size: 32px;
      }
      .nav-links {
        display: none;
      }
      .ac-type-selector {
        grid-template-columns: 1fr;
      }
      .time-slots-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>
</head>
<body class="customer-body">

  <!-- Customer Header Navigation -->
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
        <a href="#home">หน้าแรก</a>
        <a href="#services">บริการและราคา</a>
        <a href="#technicians">ทีมช่างมืออาชีพ</a>
        <a href="#booking-form">จองคิวล้างแอร์</a>
        <a href="track.php">เช็คสถานะการจอง</a>
      </nav>

      <div class="nav-action-group">
        <a href="#booking-form" class="btn-book-nav">＋ จองคิวทันที</a>
        <a href="admin/login.php" class="btn-admin-gate" title="เข้าสู่ระบบผู้ดูแล">
          <span>🔐</span> สำหรับเจ้าหน้าที่
        </a>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="home" class="hero-section">
    <div class="hero-container">
      <div>
        <div class="banpong-hero-pill">
          <span>📍</span> ให้บริการครอบคลุมทั่ว อ.บ้านโป่ง จ.ราชบุรี ทุกตำบล
        </div>
        <h1 class="hero-title">
          จองคิวล้างแอร์ในบ้านโป่ง<br>
          <span>เลือกช่างแอร์ที่คุณไว้วางใจได้</span>
        </h1>
        <p class="hero-desc">
          หมดกังวลเรื่องแอร์ไม่เย็น ลมมีกลิ่นอับ หรือค่าไฟพุ่งสูง ทีมช่างมืออาชีพสาขาบ้านโป่งพร้อมเข้าบริการถึงบ้าน ถอดล้างสะอาดลึกทุกชิ้นส่วนด้วยน้ำยาฆ่าเชื้อเกรดโรงพยาบาล พร้อมรับประกันงาน 30 วัน
        </p>
        <div class="hero-cta-row">
          <a href="#booking-form" class="btn-hero-primary">
            <span>📅</span> จองคิวล้างแอร์ทันที
          </a>
          <a href="track.php" class="btn-hero-track">
            <span>🔍</span> ตรวจสอบสถานะการจอง
          </a>
        </div>

        <div class="trust-badges">
          <div class="trust-item">
            <div class="trust-item-icon">✓</div>
            <div class="trust-item-text">
              <b>รับประกันงาน 30 วัน</b>
              <span>น้ำรั่วไม่เย็นแก้ไขฟรี</span>
            </div>
          </div>
          <div class="trust-item">
            <div class="trust-item-icon">★</div>
            <div class="trust-item-text">
              <b>ช่างผ่านการตรวจประวัติ</b>
              <span>ความพึงพอใจ 4.9/5 ดาว</span>
            </div>
          </div>
          <div class="trust-item">
            <div class="trust-item-icon">📍</div>
            <div class="trust-item-text">
              <b>ทั่วอำเภอบ้านโป่ง</b>
              <span>ไม่มีบวกค่าเดินทางเพิ่ม</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Hero Visual Card -->
      <div class="hero-visual-card">
        <div class="visual-rate-pill">★ 4.9/5 (1,500+ รีวิว)</div>
        <div style="font-size:13px; color:#0284c7; font-weight:700; margin-bottom:12px; letter-spacing:1px;">
          อัตราค่าบริการมาตรฐาน (อ.บ้านโป่ง)
        </div>

        <div class="visual-service-item">
          <div class="visual-service-icon">🏠</div>
          <div class="visual-service-details">
            <b>แอร์ติดผนัง (Wall Type)</b>
            <span>ขนาด 9,000 - 24,000 BTU ล้างลึกพร้อมฉีดสเปรย์หอม</span>
          </div>
          <div class="visual-price">฿500</div>
        </div>

        <div class="visual-service-item">
          <div class="visual-service-icon">🏢</div>
          <div class="visual-service-details">
            <b>แอร์แขวน / ตั้งพื้น</b>
            <span>สำหรับสำนักงานและร้านค้าในบ้านโป่ง ลมแรงฉ่ำใจ</span>
          </div>
          <div class="visual-price">฿800</div>
        </div>

        <div class="visual-service-item">
          <div class="visual-service-icon">✨</div>
          <div class="visual-service-details">
            <b>แอร์ 4 ทิศทาง (Cassette)</b>
            <span>แอร์ฝังฝ้าเพดาน ล้างแผงคอยล์และถาดน้ำทิ้งสะอาดหมดจด</span>
          </div>
          <div class="visual-price">฿1,200</div>
        </div>

        <div style="background:#f0f9ff; border:1px dashed #bae6fd; padding:12px; border-radius:12px; text-align:center; font-size:12.5px; color:#0369a1; margin-top:16px;">
          🛡️ สิทธิพิเศษ: ฟรี! ตรวจเช็คกระแสไฟ แรงดันน้ำยาแอร์ R32/R410A และเช็ครอยรั่ว
        </div>
      </div>
    </div>
  </section>

  <!-- Section: Services & Pricing -->
  <section id="services" class="section-wrapper" style="padding-top:30px;">
    <div class="section-header">
      <span class="section-tag">POPULAR PACKAGES IN BAN PONG</span>
      <h2>ประเภทบริการและแพ็กเกจล้างแอร์</h2>
      <p>เลือกบริการที่เหมาะกับเครื่องปรับอากาศของคุณ ดูแลโดยช่างผู้เชี่ยวชาญพร้อมเครื่องมือมาตรฐาน</p>
    </div>

    <div class="services-grid">
      <div class="service-card service-card-popular">
        <span class="service-popular-badge">ยอดนิยมอันดับ 1</span>
        <div class="service-card-icon">🏠</div>
        <h3>แอร์ติดผนัง (Wall Type)</h3>
        <p>บริการล้างแอร์บ้านพักและคอนโด ถอดล้างแผ่นกรอง ถาดน้ำทิ้ง ฉีดล้างคอยล์เย็นด้วยปั๊มแรงดันสูง ป้องกันน้ำหยดและกลิ่นอับ</p>
        <div class="service-price-tag">฿500 <span>/ เครื่อง</span></div>
        <button type="button" class="btn btn-primary" onclick="quickSelectService('แอร์ติดผนัง', 500)">
          เลือกบริการนี้ →
        </button>
      </div>

      <div class="service-card">
        <div class="service-card-icon">🏢</div>
        <h3>แอร์แขวน / ตั้งพื้น</h3>
        <p>เหมาะสำหรับสำนักงาน ร้านค้า คลินิก ร้านอาหารในบ้านโป่ง แอร์ขนาดใหญ่ 18,000 - 40,000 BTU ล้างพัดลมกรงกระรอกและแผงระบายความร้อน</p>
        <div class="service-price-tag">฿800 <span>/ เครื่อง</span></div>
        <button type="button" class="btn btn-secondary" onclick="quickSelectService('แอร์แขวน/ตั้งพื้น', 800)">
          เลือกบริการนี้ →
        </button>
      </div>

      <div class="service-card">
        <div class="service-card-icon">✨</div>
        <h3>แอร์ 4 ทิศทาง (Cassette)</h3>
        <p>แอร์ฝังฝ้าเพดานที่ต้องใช้ช่างผู้เชี่ยวชาญเฉพาะทาง ถอดหน้ากาก ล้างถาดรองน้ำทิ้ง และตรวจเช็คระบบปั๊มเดรนน้ำทิ้งครบวงจร</p>
        <div class="service-price-tag">฿1,200 <span>/ เครื่อง</span></div>
        <button type="button" class="btn btn-secondary" onclick="quickSelectService('แอร์สี่ทิศทาง (Cassette)', 1200)">
          เลือกบริการนี้ →
        </button>
      </div>
    </div>
  </section>

  <!-- Section: Certified Technicians ("เลือกช่างแอร์ที่คุณไว้วางใจ - จองกับช่างคนนี้ได้") -->
  <section id="technicians" class="section-wrapper">
    <div class="section-header">
      <span class="section-tag">BAN PONG CERTIFIED TECHNICIANS</span>
      <h2>เลือกช่างแอร์ที่คุณไว้วางใจ</h2>
      <p>คุณสามารถเลือกระบุช่างที่ต้องการให้ไปบริการที่บ้านใน อ.บ้านโป่ง ได้โดยตรง ช่างทุกคนผ่านการตรวจประวัติและมีคะแนนประเมินจริงจากลูกค้า</p>
    </div>

    <div class="technicians-grid">
      <?php foreach ($technicians as $tech): ?>
        <?php
          $avatarBg = match($tech->getId() % 4) {
            1 => 'linear-gradient(135deg, #0284c7, #2563eb)',
            2 => 'linear-gradient(135deg, #0d9488, #059669)',
            3 => 'linear-gradient(135deg, #7c3aed, #4f46e5)',
            default => 'linear-gradient(135deg, #d97706, #ea580c)'
          };
        ?>
        <div class="technician-card" id="tech-card-<?=$tech->getId()?>">
          <div class="tech-avatar-box" style="background:<?=$avatarBg?>; color:#fff;">
            <span>👨‍🔧</span>
          </div>
          <h3 class="tech-name"><?=htmlspecialchars($tech->getName())?></h3>
          <div class="tech-skill-badge"><?=htmlspecialchars($tech->getSkill())?></div>
          <p class="tech-meta"><?=htmlspecialchars($tech->getExperience())?></p>
          
          <div class="tech-rating">
            <span class="stars">★★★★★</span>
            <span><?=number_format($tech->getRating(), 1)?></span>
          </div>

          <div style="margin-bottom:14px;">
            <?=$tech->getStatusBadge()?>
          </div>

          <button type="button" 
                  class="btn-select-tech" 
                  onclick="selectTechnician(<?=$tech->getId()?>, '<?=htmlspecialchars(addslashes($tech->getName()))?>', '<?=htmlspecialchars(addslashes($tech->getSkill()))?>')">
            <span>👉</span> จองกับช่างคนนี้
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Section: Booking Form (ฟอร์มจองคิวล้างแอร์ พร้อม Google Maps บ้านโป่ง) -->
  <section id="booking-form" class="booking-section">
    <div class="booking-layout">
      <!-- Left: Form -->
      <div class="booking-form-box">
        <div style="margin-bottom:24px;">
          <span class="section-tag">ONLINE APPOINTMENT • BAN PONG</span>
          <h2 style="font-size:26px; font-weight:700; color:#0f172a; margin-bottom:6px;">แบบฟอร์มจองคิวล้างแอร์</h2>
          <p style="color:#64748b; font-size:14.5px;">กรอกข้อมูลด้านล่างและระบุตำแหน่งบ้านใน อ.บ้านโป่ง เพื่อให้ช่างเดินทางไปถึงได้อย่างแม่นยำ</p>
        </div>

        <?php if (!empty($bookingError)): ?>
          <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:14px; border-radius:12px; margin-bottom:20px; font-weight:500;">
            ⚠️ <?=htmlspecialchars($bookingError)?>
          </div>
        <?php endif; ?>

        <!-- Active Selected Technician Banner (if picked) -->
        <div id="techBanner" class="selected-tech-banner" style="display:none;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:26px;">👨‍🔧</div>
            <div>
              <small style="color:#0369a1; font-weight:600;">ช่างที่คุณเลือกให้บริการ:</small>
              <div id="techBannerName" style="font-weight:700; font-size:16px; color:#0f172a;">-</div>
            </div>
          </div>
          <button type="button" onclick="clearSelectedTech()" style="background:#fff; border:1px solid #cbd5e1; border-radius:8px; padding:6px 12px; font-size:12px; cursor:pointer;">
            เปลี่ยนช่าง
          </button>
        </div>

        <form action="booking_action.php" method="post" id="mainBookingForm">
          <!-- Hidden Inputs -->
          <input type="hidden" name="technician_id" id="form_technician_id" value="">
          <input type="hidden" name="ac_type" id="form_ac_type" value="แอร์ติดผนัง">
          <input type="hidden" name="service_time" id="form_service_time" value="09:00">
          <input type="hidden" name="latitude" id="form_latitude" value="13.8164">
          <input type="hidden" name="longitude" id="form_longitude" value="99.8774">

          <!-- Step 1: ข้อมูลลูกค้า และ แผนที่ Google Maps บ้านโป่ง -->
          <div class="form-step-title">
            <span class="step-num">1</span>
            <span>ข้อมูลลูกค้าและพิกัดบ้านใน อ.บ้านโป่ง (Google Maps)</span>
          </div>

          <div class="form-grid">
            <div class="field">
              <label>ชื่อ-นามสกุลลูกค้า <span style="color:#ef4444;">*</span></label>
              <input type="text" name="customer_name" id="customer_name" placeholder="เช่น คุณสมชาย ใจดี" required>
            </div>
            <div class="field">
              <label>เบอร์โทรศัพท์ติดต่อ <span style="color:#ef4444;">*</span></label>
              <input type="tel" name="customer_phone" id="customer_phone" placeholder="เช่น 0812345678" required>
            </div>
            <div class="field">
              <label>อีเมล (ถ้ามี)</label>
              <input type="email" name="customer_email" placeholder="name@example.com">
            </div>
            <div class="field">
              <label>ตำบลใน อ.บ้านโป่ง จ.ราชบุรี <span style="color:#ef4444;">*</span></label>
              <select name="subdistrict" id="subdistrict_select" onchange="updateBanPongMap(this.value)" required>
                <?php foreach ($banPongSubs as $subName => $subData): ?>
                  <option value="<?=$subName?>"><?=$subName?> (ใกล้ <?=$subData['landmark']?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field full">
              <label>ที่อยู่บ้านพัก / จุดสังเกตในบ้านโป่ง <span style="color:#ef4444;">*</span></label>
              <textarea name="customer_address" id="customer_address" rows="2" placeholder="เช่น 142/5 ถนนทรงพล ต.บ้านโป่ง, ตรงข้าม ร.ร.สารสิทธิ์, ซอยข้างวัดเบิกไพร..." required></textarea>
            </div>

            <!-- Google Map Embed & Navigation Preview -->
            <div class="field full">
              <label style="display:flex; justify-content:space-between; align-items:center;">
                <span>📍 แผนที่ Google Maps พิกัดบ้านลูกค้า (อ.บ้านโป่ง)</span>
                <button type="button" onclick="getCurrentLocation()" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; border-radius:6px; padding:3px 10px; font-size:12px; cursor:pointer;">
                  🎯 ปักหมุดตำแหน่งปัจจุบันของฉัน (GPS)
                </button>
              </label>
              <div class="map-preview-container">
                <div class="map-overlay-badge" id="mapBadge">
                  📍 อ.บ้านโป่ง จ.ราชบุรี
                </div>
                <iframe id="googleMapIframe" src="https://maps.google.com/maps?q=13.8164,99.8774&hl=th&z=14&output=embed"></iframe>
              </div>
              <small style="color:#64748b; font-size:12px; margin-top:6px; display:block;">
                * แผนที่จะอัปเดตอัตโนมัติเมื่อเลือกตำบล เพื่อให้ช่างสามารถใช้ระบบ GPS นำทางไปยังบ้านของคุณได้อย่างแม่นยำ
              </small>
            </div>
          </div>

          <!-- Step 2: เลือกประเภทแอร์ & จำนวน -->
          <div class="form-step-title">
            <span class="step-num">2</span>
            <span>ประเภทเครื่องปรับอากาศและจำนวน</span>
          </div>

          <div class="ac-type-selector">
            <div class="ac-type-option active" id="opt-wall" data-type="แอร์ติดผนัง" data-price="500" onclick="chooseAcType(this, 'แอร์ติดผนัง', 500)">
              <span class="ac-icon">🏠</span>
              <b>แอร์ติดผนัง</b>
              <span>500 ฿ / เครื่อง</span>
            </div>
            <div class="ac-type-option" id="opt-ceiling" data-type="แอร์แขวน/ตั้งพื้น" data-price="800" onclick="chooseAcType(this, 'แอร์แขวน/ตั้งพื้น', 800)">
              <span class="ac-icon">🏢</span>
              <b>แอร์แขวน / ตั้งพื้น</b>
              <span>800 ฿ / เครื่อง</span>
            </div>
            <div class="ac-type-option" id="opt-cassette" data-type="แอร์สี่ทิศทาง (Cassette)" data-price="1200" onclick="chooseAcType(this, 'แอร์สี่ทิศทาง (Cassette)', 1200)">
              <span class="ac-icon">✨</span>
              <b>แอร์ 4 ทิศทาง</b>
              <span>1,200 ฿ / เครื่อง</span>
            </div>
          </div>

          <div class="form-grid" style="margin-bottom:18px;">
            <div class="field">
              <label>จำนวนเครื่องที่ต้องการล้าง</label>
              <div style="display:flex; align-items:center; gap:8px;">
                <button type="button" onclick="adjustUnits(-1)" style="width:44px; height:44px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; font-size:20px; font-weight:bold; cursor:pointer;">-</button>
                <input type="number" name="ac_units" id="ac_units" min="1" max="20" value="1" readonly style="text-align:center; font-weight:700; font-size:17px; height:44px;">
                <button type="button" onclick="adjustUnits(1)" style="width:44px; height:44px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; font-size:20px; font-weight:bold; cursor:pointer;">+</button>
              </div>
            </div>
            <div class="field">
              <label>ช่างผู้ให้บริการ</label>
              <select id="technician_select" onchange="syncTechnicianDropdown(this)">
                <option value="">-- ให้ระบบจัดช่างที่เหมาะสมให้ (แนะนำ) --</option>
                <?php foreach ($technicians as $t): ?>
                  <option value="<?=$t->getId()?>">
                    <?=$t->getName()?> (<?=$t->getSkill()?> - <?=$t->getStatus()?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Step 3: บริการเสริม -->
          <div class="form-step-title">
            <span class="step-num">3</span>
            <span>บริการเสริมพิเศษ (ออปชันเสริม)</span>
          </div>

          <div class="addons-list">
            <label class="addon-label">
              <div class="addon-info">
                <input type="checkbox" name="addons[]" value="ozone" onchange="calculateLiveTotal()">
                <div>
                  <b>อบโอโซนฆ่าเชื้อโรคและกลิ่นอับ</b>
                  <span>กำจัดแบคทีเรียและเชื้อราล้ำลึก</span>
                </div>
              </div>
              <div class="addon-price">+150 ฿ / เครื่อง</div>
            </label>

            <label class="addon-label">
              <div class="addon-info">
                <input type="checkbox" name="addons[]" value="gas_check" onchange="calculateLiveTotal()">
                <div>
                  <b>ตรวจเช็คและเติมน้ำยาแอร์ R32/R410A</b>
                  <span>ไม่เกิน 15 ปอนด์ต่อเครื่อง</span>
                </div>
              </div>
              <div class="addon-price">+200 ฿ / เครื่อง</div>
            </label>

            <label class="addon-label">
              <div class="addon-info">
                <input type="checkbox" name="addons[]" value="deep_clean" onchange="calculateLiveTotal()">
                <div>
                  <b>Deep Clean ถอดล้างทุกชิ้นส่วน</b>
                  <span>ถอดถาดน้ำทิ้งและโบลเวอร์แอร์สะอาดกริบ</span>
                </div>
              </div>
              <div class="addon-price">+300 ฿ / เครื่อง</div>
            </label>
          </div>

          <!-- Step 4: วันและเวลานัดหมาย -->
          <div class="form-step-title">
            <span class="step-num">4</span>
            <span>วันและเวลานัดหมาย</span>
          </div>

          <div class="form-grid" style="margin-bottom:18px;">
            <div class="field">
              <label>เลือกวันที่ต้องการรับบริการ <span style="color:#ef4444;">*</span></label>
              <input type="date" name="service_date" id="service_date" min="<?=date('Y-m-d')?>" value="<?=date('Y-m-d', strtotime('+1 day'))?>" required>
            </div>
            <div class="field">
              <label>ช่องทางการชำระเงิน</label>
              <select name="payment_method">
                <option value="พร้อมเพย์ QR Code">พร้อมเพย์ QR Code (แนะนำ)</option>
                <option value="ชำระเงินสดหน้างาน">ชำระเงินสดกับช่างหลังงานเสร็จ</option>
                <option value="โอนผ่านบัญชีธนาคาร">โอนเงินผ่านบัญชีธนาคาร</option>
              </select>
            </div>
          </div>

          <div class="field" style="margin-bottom:18px;">
            <label>เลือกช่วงเวลาที่สะดวก</label>
            <div class="time-slots-grid">
              <div class="time-slot-btn active" data-time="09:00" onclick="chooseTimeSlot(this, '09:00')">09:00 - 11:00 น.</div>
              <div class="time-slot-btn" data-time="11:00" onclick="chooseTimeSlot(this, '11:00')">11:00 - 13:00 น.</div>
              <div class="time-slot-btn" data-time="13:30" onclick="chooseTimeSlot(this, '13:30')">13:30 - 15:30 น.</div>
              <div class="time-slot-btn" data-time="15:30" onclick="chooseTimeSlot(this, '15:30')">15:30 - 17:30 น.</div>
            </div>
          </div>

          <div class="field full" style="margin-bottom:24px;">
            <label>หมายเหตุเพิ่มเติม (ถ้ามี)</label>
            <textarea name="note" placeholder="เช่น แอร์มีน้ำหยด, ห้องอยู่ชั้น 2 ไม่มีลิฟต์, ติดต่อที่ป้อมยาม..."></textarea>
          </div>

          <button type="submit" class="btn-confirm-booking">
            <span>✓</span> ยืนยันการจองคิวล้างแอร์
          </button>
        </form>
      </div>

      <!-- Right: Real-time Live Price Summary Card -->
      <aside class="booking-summary-card">
        <div class="summary-title">
          <span>สรุปรายการจอง</span>
          <span style="font-size:13px; font-weight:500; color:#0284c7;">Real-time</span>
        </div>

        <div class="summary-row">
          <span id="sum_ac_type_label">แอร์ติดผนัง (1 เครื่อง)</span>
          <b id="sum_base_price">฿500.00</b>
        </div>

        <div class="summary-row" id="sum_addons_row" style="display:none;">
          <span>บริการเสริม</span>
          <b id="sum_addons_price">฿0.00</b>
        </div>

        <div class="summary-row">
          <span>ค่าเดินทางในเขตบ้านโป่ง</span>
          <b style="color:#10b981;">ฟรี</b>
        </div>

        <div class="selected-tech-preview">
          <div class="tech-preview-avatar">👨‍🔧</div>
          <div class="tech-preview-info">
            <b id="previewTechName">ระบบจัดสรรช่างให้</b>
            <span id="previewTechSkill">ช่างผู้ชำนาญการที่พร้อมที่สุด</span>
          </div>
        </div>

        <div class="summary-row total-row">
          <span>ยอดรวมสุทธิ</span>
          <div class="summary-total-price" id="sum_total_price">฿500.00</div>
        </div>

        <div style="font-size:12px; color:#64748b; line-height:1.6; margin-top:16px; background:#f8fafc; padding:12px; border-radius:10px;">
          ✓ รับประกันงาน 30 วัน น้ำรั่วหรือแอร์ไม่เย็นแก้ไขฟรี<br>
          ✓ บริการครอบคลุมทุกตำบลใน อ.บ้านโป่ง จ.ราชบุรี
        </div>

        <button type="button" class="btn-confirm-booking" onclick="document.getElementById('mainBookingForm').requestSubmit()">
          <span>✓</span> ยืนยันการจองคิว
        </button>
      </aside>
    </div>
  </section>

  <!-- Modal: Booking Success Confirmation with Receipt Button -->
  <?php if (!empty($lastBooking)): ?>
  <div id="bookingSuccessModal" class="modal-overlay active">
    <div class="modal-content" style="max-width:560px; text-align:center;">
      <div style="width:68px; height:68px; border-radius:50%; background:#dcfce7; color:#15803d; font-size:32px; display:grid; place-items:center; margin:0 auto 16px;">
        ✓
      </div>
      <h2 style="font-size:24px; font-weight:700; color:#0f172a; margin-bottom:6px;">จองคิวล้างแอร์สำเร็จ!</h2>
      <p style="color:#64748b; font-size:14px; margin-bottom:20px;">เราได้รับข้อมูลการจองของคุณเรียบร้อยแล้ว ช่างจะติดต่อยืนยันก่อนเดินทาง</p>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:20px; text-align:left; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
          <span style="color:#64748b; font-size:13px;">รหัสการจอง:</span>
          <b style="color:#0284c7; font-size:16px;"><?=htmlspecialchars($lastBooking['booking_code'])?></b>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
          <span style="color:#64748b; font-size:13px;">ชื่อลูกค้า:</span>
          <b><?=htmlspecialchars($lastBooking['customer_name'])?> (<?=htmlspecialchars($lastBooking['customer_phone'])?>)</b>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
          <span style="color:#64748b; font-size:13px;">พิกัดสถานที่:</span>
          <b style="color:#0f172a;"><?=htmlspecialchars($lastBooking['subdistrict'])?> อ.บ้านโป่ง จ.ราชบุรี</b>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
          <span style="color:#64748b; font-size:13px;">ช่างผู้ให้บริการ:</span>
          <b style="color:#0284c7;">🔧 <?=htmlspecialchars($lastBooking['technician_name'])?></b>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
          <span style="color:#64748b; font-size:13px;">วันและเวลานัด:</span>
          <b><?=htmlspecialchars($lastBooking['service_date'])?></b>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:1px dashed #cbd5e1; padding-top:10px; margin-top:10px;">
          <b style="font-size:15px; color:#0f172a;">ยอดที่ต้องชำระ:</b>
          <b style="font-size:20px; color:#0284c7;">฿<?=number_format((float)$lastBooking['total_price'], 2)?></b>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
        <a href="receipt.php?code=<?=urlencode($lastBooking['booking_code'])?>" target="_blank" class="btn btn-primary" style="background:#0284c7;">
          🧾 ดูและพิมพ์บิลการจอง
        </a>
        <a href="track.php?q=<?=urlencode($lastBooking['booking_code'])?>" class="btn btn-secondary">
          🔍 เช็คสถานะคิวล้างแอร์
        </a>
      </div>

      <button type="button" class="btn btn-secondary" style="width:100%;" onclick="document.getElementById('bookingSuccessModal').classList.remove('active')">
        ปิดหน้าต่าง
      </button>
    </div>
  </div>
  <?php endif; ?>

  <!-- Selection Toast -->
  <div id="selectionToast">
    <span>👉</span>
    <span id="toastMsg">คุณได้เลือกช่างเรียบร้อยแล้ว</span>
  </div>

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
          <a href="#technicians" style="color:#94a3b8; text-decoration:none;">ทีมช่างแอร์บ้านโป่ง</a>
          <a href="#booking-form" style="color:#94a3b8; text-decoration:none;">จองคิวบริการ</a>
          <a href="track.php" style="color:#94a3b8; text-decoration:none;">ตรวจสอบสถานะงาน</a>
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
      <div>© 2026 AirCare Pro. All rights reserved. Built with OOA • OOD • OOP Architecture.</div>
      <a href="admin/login.php" style="color:#64748b; text-decoration:none; font-size:12.5px;">
        🔐 สำหรับเจ้าหน้าที่ (Admin Portal)
      </a>
    </div>
  </footer>

  <!-- Interactive JavaScript for Live Booking Logic & Ban Pong Maps -->
  <script>
    const banPongPresets = <?=json_encode($banPongSubs, JSON_UNESCAPED_UNICODE)?>;
    let currentBasePrice = 500;
    let currentAcTypeName = 'แอร์ติดผนัง';

    function updateBanPongMap(subName) {
      if (banPongPresets[subName]) {
        const lat = banPongPresets[subName].lat;
        const lng = banPongPresets[subName].lng;
        document.getElementById('form_latitude').value = lat;
        document.getElementById('form_longitude').value = lng;
        document.getElementById('googleMapIframe').src = `https://maps.google.com/maps?q=${lat},${lng}&hl=th&z=14&output=embed`;
        document.getElementById('mapBadge').innerText = `📍 ${subName} อ.บ้านโป่ง (ใกล้ ${banPongPresets[subName].landmark.split(',')[0]})`;
      }
    }

    function getCurrentLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          document.getElementById('form_latitude').value = lat;
          document.getElementById('form_longitude').value = lng;
          document.getElementById('googleMapIframe').src = `https://maps.google.com/maps?q=${lat},${lng}&hl=th&z=16&output=embed`;
          document.getElementById('mapBadge').innerText = `📍 พิกัดปัจจุบันของคุณ (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
          alert('ปักหมุดตำแหน่งปัจจุบันของคุณเรียบร้อยแล้ว!');
        }, () => {
          alert('ไม่สามารถดึงตำแหน่ง GPS ได้ ระบบจะใช้พิกัดเริ่มต้นของตำบลในบ้านโป่ง');
        });
      } else {
        alert('เบราว์เซอร์ไม่รองรับ Geolocation');
      }
    }

    function quickSelectService(typeName, price) {
      if (typeName === 'แอร์ติดผนัง') {
        chooseAcType(document.getElementById('opt-wall'), typeName, price);
      } else if (typeName === 'แอร์แขวน/ตั้งพื้น') {
        chooseAcType(document.getElementById('opt-ceiling'), typeName, price);
      } else {
        chooseAcType(document.getElementById('opt-cassette'), typeName, price);
      }
      document.getElementById('booking-form').scrollIntoView({ behavior: 'smooth' });
    }

    function chooseAcType(element, typeName, price) {
      document.querySelectorAll('.ac-type-option').forEach(el => el.classList.remove('active'));
      element.classList.add('active');

      currentBasePrice = price;
      currentAcTypeName = typeName;
      document.getElementById('form_ac_type').value = typeName;

      calculateLiveTotal();
    }

    function adjustUnits(delta) {
      const unitsInput = document.getElementById('ac_units');
      let currentVal = parseInt(unitsInput.value) || 1;
      currentVal = Math.max(1, Math.min(20, currentVal + delta));
      unitsInput.value = currentVal;

      calculateLiveTotal();
    }

    function chooseTimeSlot(element, timeValue) {
      document.querySelectorAll('.time-slot-btn').forEach(btn => btn.classList.remove('active'));
      element.classList.add('active');
      document.getElementById('form_service_time').value = timeValue;
    }

    // Technician Selection ("จองกับช่างคนนี้ได้")
    function selectTechnician(techId, techName, techSkill) {
      document.querySelectorAll('.technician-card').forEach(c => c.classList.remove('selected-card'));
      const activeCard = document.getElementById('tech-card-' + techId);
      if (activeCard) activeCard.classList.add('selected-card');

      document.getElementById('form_technician_id').value = techId;
      document.getElementById('technician_select').value = techId;

      const banner = document.getElementById('techBanner');
      banner.style.display = 'flex';
      document.getElementById('techBannerName').innerText = techName + ' (' + techSkill + ')';

      document.getElementById('previewTechName').innerText = techName;
      document.getElementById('previewTechSkill').innerText = techSkill;

      const toast = document.getElementById('selectionToast');
      document.getElementById('toastMsg').innerText = 'คุณได้เลือก "' + techName + '" เรียบร้อยแล้ว';
      toast.style.display = 'flex';
      setTimeout(() => { toast.style.display = 'none'; }, 3000);

      document.getElementById('booking-form').scrollIntoView({ behavior: 'smooth' });
    }

    function syncTechnicianDropdown(selectEl) {
      const techId = selectEl.value;
      if (techId) {
        const selectedOption = selectEl.options[selectEl.selectedIndex].text;
        document.getElementById('form_technician_id').value = techId;
        document.getElementById('previewTechName').innerText = selectedOption.split('(')[0].trim();
        document.getElementById('previewTechSkill').innerText = 'ช่างที่เลือกไว้';

        document.getElementById('techBanner').style.display = 'flex';
        document.getElementById('techBannerName').innerText = selectedOption;

        document.querySelectorAll('.technician-card').forEach(c => c.classList.remove('selected-card'));
        const targetCard = document.getElementById('tech-card-' + techId);
        if (targetCard) targetCard.classList.add('selected-card');
      } else {
        clearSelectedTech();
      }
    }

    function clearSelectedTech() {
      document.getElementById('form_technician_id').value = '';
      document.getElementById('technician_select').value = '';
      document.getElementById('techBanner').style.display = 'none';
      document.getElementById('previewTechName').innerText = 'ระบบจัดสรรช่างให้';
      document.getElementById('previewTechSkill').innerText = 'ช่างผู้ชำนาญการที่พร้อมที่สุด';
      document.querySelectorAll('.technician-card').forEach(c => c.classList.remove('selected-card'));
    }

    function calculateLiveTotal() {
      const units = parseInt(document.getElementById('ac_units').value) || 1;
      const baseSubtotal = currentBasePrice * units;

      let addonTotal = 0;
      const checkboxes = document.querySelectorAll('input[name="addons[]"]:checked');
      checkboxes.forEach(cb => {
        if (cb.value === 'ozone') addonTotal += (150 * units);
        if (cb.value === 'gas_check') addonTotal += (200 * units);
        if (cb.value === 'deep_clean') addonTotal += (300 * units);
      });

      const grandTotal = baseSubtotal + addonTotal;

      document.getElementById('sum_ac_type_label').innerText = currentAcTypeName + ' (' + units + ' เครื่อง)';
      document.getElementById('sum_base_price').innerText = '฿' + baseSubtotal.toLocaleString('th-TH', {minimumFractionDigits: 2});

      const addonsRow = document.getElementById('sum_addons_row');
      if (addonTotal > 0) {
        addonsRow.style.display = 'flex';
        document.getElementById('sum_addons_price').innerText = '+฿' + addonTotal.toLocaleString('th-TH', {minimumFractionDigits: 2});
      } else {
        addonsRow.style.display = 'none';
      }

      document.getElementById('sum_total_price').innerText = '฿' + grandTotal.toLocaleString('th-TH', {minimumFractionDigits: 2});
    }

    document.addEventListener('DOMContentLoaded', calculateLiveTotal);
  </script>

</body>
</html>