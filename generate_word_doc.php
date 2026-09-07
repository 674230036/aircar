<?php
/**
 * Generator for AirCare Pro OOA • OOD • OOP Academic Project Report
 * Generates formatted HTML-based .doc and prepares XML files for .docx packaging.
 */

$reportTitle = "รายงานการวิเคราะห์ ออกแบบ และพัฒนาระบบจัดการร้านและจองคิวล้างแอร์มืออาชีพ (AirCare Pro สาขาบ้านโป่ง)";
$reportSubtitle = "โครงงานศึกษาและประยุกต์ใช้หลักการ Object-Oriented Analysis (OOA), Object-Oriented Design (OOD) และ Object-Oriented Programming (OOP)";

$htmlDocContent = <<<HTML
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset="utf-8">
<title>{$reportTitle}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 2.54cm 2.54cm 2.54cm 2.54cm;
    mso-page-orientation: portrait;
  }
  body {
    font-family: 'TH Sarabun New', 'Angsana New', 'Cordia New', Tahoma, sans-serif;
    font-size: 16pt;
    line-height: 1.5;
    color: #000000;
  }
  h1 { font-size: 22pt; font-weight: bold; color: #0f172a; text-align: center; margin-top: 24pt; margin-bottom: 12pt; }
  h2 { font-size: 19pt; font-weight: bold; color: #0284c7; margin-top: 18pt; margin-bottom: 8pt; border-bottom: 2pt solid #0284c7; padding-bottom: 4pt; }
  h3 { font-size: 17pt; font-weight: bold; color: #1e293b; margin-top: 14pt; margin-bottom: 6pt; }
  h4 { font-size: 16pt; font-weight: bold; color: #334155; margin-top: 10pt; margin-bottom: 4pt; }
  p { margin-top: 0; margin-bottom: 6pt; text-align: justify; text-indent: 1.5cm; }
  .no-indent { text-indent: 0; }
  .cover-page { text-align: center; page-break-after: always; padding-top: 80pt; }
  .cover-title { font-size: 26pt; font-weight: bold; color: #0284c7; margin-bottom: 14pt; }
  .cover-sub { font-size: 18pt; color: #334155; margin-bottom: 40pt; }
  .cover-box { border: 2pt solid #0284c7; padding: 24pt; margin: 30pt auto; width: 85%; background: #f0f9ff; }
  .cover-meta { font-size: 16pt; color: #1e293b; line-height: 2; }
  table.doc-table { width: 100%; border-collapse: collapse; margin-top: 10pt; margin-bottom: 14pt; font-size: 14pt; }
  table.doc-table th { background-color: #0284c7; color: #ffffff; border: 1pt solid #0f172a; padding: 8pt; text-align: center; font-weight: bold; }
  table.doc-table td { border: 1pt solid #94a3b8; padding: 6pt 8pt; vertical-align: top; }
  table.doc-table tr:nth-child(even) td { background-color: #f8fafc; }
  .code-block { font-family: 'Consolas', 'Courier New', monospace; font-size: 11pt; background-color: #f1f5f9; border: 1pt solid #cbd5e1; padding: 8pt; margin: 8pt 0; border-radius: 4pt; white-space: pre-wrap; line-height: 1.3; }
  .diagram-box { background-color: #f8fafc; border: 1.5pt dashed #0284c7; padding: 14pt; margin: 12pt 0; text-align: center; font-size: 14pt; border-radius: 6pt; }
  .badge-tag { background-color: #e0f2fe; color: #0369a1; padding: 2pt 6pt; border-radius: 4pt; font-size: 12pt; font-weight: bold; }
  .callout { background-color: #eff6ff; border-left: 4pt solid #0284c7; padding: 10pt 14pt; margin: 10pt 0; }
</style>
</head>
<body>

<!-- ==============================================================================
     หน้าปก (Cover Page)
     ============================================================================== -->
<div class="cover-page">
  <div style="font-size: 40pt; margin-bottom: 10pt;">❄️</div>
  <div class="cover-title">รายงานโครงงานการวิเคราะห์และพัฒนาระบบ</div>
  <div style="font-size: 22pt; font-weight: bold; color: #0f172a; margin-bottom: 12pt;">ระบบจัดการร้านและจองคิวล้างแอร์มืออาชีพ AirCare Pro (สาขาบ้านโป่ง)</div>
  <div class="cover-sub">Air Conditioner Cleaning Management System using OOA, OOD, and OOP</div>

  <div class="cover-box">
    <div class="cover-meta">
      <b>การประยุกต์ใช้ระเบียบวิธีเชิงวัตถุ:</b><br>
      Object-Oriented Analysis (OOA) • Object-Oriented Design (OOD) • Object-Oriented Programming (OOP)<br><br>
      <b>ฟีเจอร์เด่นของระบบ:</b><br>
      • ระบบเลือกช่างแอร์ประจำตัวและจองคิวออนไลน์<br>
      • ระบบแผนที่ Google Maps ปักหมุดบ้านลูกค้าในเขต อ.บ้านโป่ง จ.ราชบุรี<br>
      • ระบบคำนวณราคาแบบ Real-time และออกบิลใบเสร็จรับเงินขนาด A4<br>
      • กระบวนการปิดงาน (Job Completion) และรายงานผลการตรวจเช็คน้ำยาแอร์ (PSI)<br>
      • แดชบอร์ดวิเคราะห์ข้อมูลสถิติพร้อมกราฟแบบโต้ตอบ (Chart.js)<br>
      • การแยกระบบมุมลูกค้า (Customer Portal) ออกจากผู้ดูแลระบบ (Admin) ปลอดภัย 100%
    </div>
  </div>

  <div style="margin-top: 60pt; font-size: 16pt; color: #475569;">
    ภาคการศึกษาที่ 1 ปีการศึกษา 2569<br>
    หลักสูตรวิทยาการคอมพิวเตอร์ / เทคโนโลยีสารสนเทศ
  </div>
</div>

<!-- ==============================================================================
     บทที่ 1: บทนำและความเป็นมา
     ============================================================================== -->
<h2>บทที่ 1: บทนำและความเป็นมาของโครงงาน (Introduction)</h2>

<h3>1.1 ความเป็นมาและความสำคัญของปัญหา</h3>
<p>
ในปัจจุบัน เครื่องปรับอากาศถือเป็นเครื่องใช้ไฟฟ้าที่มีความสำคัญอย่างยิ่งต่อความเป็นอยู่ของประชาชนในประเทศไทย เนื่องจากสภาพภูมิอากาศที่มีอุณหภูมิสูงตลอดทั้งปี โดยเฉพาะอย่างยิ่งในพื้นที่ชุมชนและที่พักอาศัยในอำเภอบ้านโป่ง จังหวัดราชบุรี ซึ่งมีความต้องการในการบำรุงรักษาและล้างทำความสะอาดเครื่องปรับอากาศเป็นจำนวนมาก อย่างไรก็ตาม การบริหารจัดการร้านล้างแอร์ในรูปแบบดั้งเดิมมักประสบปัญหาหลายประการ อาทิเช่น ลูกค้าต้องโทรศัพท์ติดต่อเพื่อนัดหมาย ซึ่งมักเกิดข้อผิดพลาดในการจดบันทึกวันและเวลา, การนัดหมายซ้ำซ้อนของช่าง, ลูกค้าไม่สามารถเลือกช่างที่มีความเชี่ยวชาญเฉพาะทางที่ตนเองไว้วางใจได้, ปัญหาเรื่องช่างหลงทางเนื่องจากไม่ทราบพิกัดบ้านที่แน่นอนในเขตตำบลต่างๆ ของอำเภอบ้านโป่ง, การไม่มีเอกสารบิลการจองหรือใบเสร็จรับเงินที่ระบุรายการอย่างชัดเจน, และการขาดระบบรายงานผลทางเทคนิคหลังการล้างเสร็จสิ้น (เช่น ค่าแรงดันน้ำยาแอร์ PSI หรือกระแสไฟฟ้า) เพื่อให้ลูกค้าตรวจสอบได้
</p>
<p>
ด้วยเหตุนี้ คณะผู้จัดทำจึงได้ทำการวิเคราะห์และพัฒนาระบบจัดการร้านและจองคิวล้างแอร์ <b>AirCare Pro (สาขาบ้านโป่ง)</b> ขึ้น โดยนำหลักการทางวิศวกรรมซอฟต์แวร์เชิงวัตถุ อันประกอบด้วย <b>OOA (Object-Oriented Analysis), OOD (Object-Oriented Design), และ OOP (Object-Oriented Programming)</b> มาใช้เป็นแกนกลางในการออกแบบ เพื่อให้ระบบมีโครงสร้างที่ยืดหยุ่น ปลอดภัย ขยายระบบได้ง่าย และสามารถตอบสนองการใช้งานจริงได้อย่างมีประสิทธิภาพสูงสุด
</p>

<h3>1.2 วัตถุประสงค์ของโครงงาน</h3>
<p class="no-indent">โครงงานนี้มีวัตถุประสงค์หลัก ดังนี้:</p>
<ol style="margin-left: 2cm; margin-bottom: 12pt;">
  <li>เพื่อออกแบบและพัฒนาระบบจองคิวล้างแอร์ออนไลน์ที่ลูกค้าสามารถระบุเลือกช่างแอร์ที่ต้องการจองได้โดยตรง พร้อมระบบคำนวณราคาสุทธิแบบ Real-time</li>
  <li>เพื่อบูรณาการระบบแผนที่ <b>Google Maps</b> ในการระบุและปักหมุดบ้านลูกค้าในเขตอำเภอบ้านโป่ง จังหวัดราชบุรี (ครอบคลุมทั้ง 15 ตำบล) เพื่อให้ช่างสามารถใช้ระบบ GPS นำทางได้อย่างแม่นยำ</li>
  <li>เพื่อพัฒนาระบบออกบิลการจองและใบเสร็จรับเงิน (Booking Bill & Invoice) ที่ได้มาตรฐาน รองรับการสั่งพิมพ์ขนาด A4 และมี QR Code สำหรับการชำระเงิน</li>
  <li>เพื่อสร้างกระบวนการปิดงาน (Job Completion Workflow) และจัดทำรายงานผลการบริการทางเทคนิค (Service Report) สำหรับบันทึกค่าน้ำยาแอร์และคำแนะนำหลังการล้าง</li>
  <li>เพื่อพัฒนาแดชบอร์ดผู้ดูแลระบบพร้อมกราฟข้อมูลสถิติเชิงวิเคราะห์ (Visual Dashboard with Chart.js) แสดงแนวโน้มรายรับ, สัดส่วนสถานะงาน, ประสิทธิภาพช่าง, และประเภทแอร์ยอดนิยม</li>
  <li>เพื่อประยุกต์ใช้หลักการ <b>OOA, OOD, และ OOP</b> (Encapsulation, Inheritance, Polymorphism, Abstraction, และ Service Layer) ในการพัฒนาซอฟต์แวร์อย่างถูกต้องตามหลักวิชาการ</li>
</ol>

<h3>1.3 ขอบเขตของโครงงาน (Project Scope)</h3>
<p><b>1) ขอบเขตด้านผู้ใช้งาน:</b> แบ่งออกเป็น 2 กลุ่มอย่างชัดเจน ได้แก่</p>
<ul style="margin-left: 2cm; margin-bottom: 8pt;">
  <li><b>กลุ่มลูกค้า (Customer):</b> สามารถเข้าใช้งานหน้า Customer Portal ได้โดยตรงเพื่อค้นหาข้อมูล, เลือกช่าง, ปักหมุดแผนที่บ้านโป่ง, จองคิว, พิมพ์บิลใบเสร็จ, และติดตามสถานะงานของตนเอง โดยไม่มีสิทธิ์เข้าถึงหรือแก้ไขข้อมูลในระบบหลังบ้านของผู้ดูแลระบบเด็ดขาด</li>
  <li><b>กลุ่มผู้ดูแลระบบและช่าง (Admin & Technician):</b> สามารถเข้าสู่ระบบผ่านการตรวจสอบสิทธิ์ (Session Login) เพื่อดูแดชบอร์ดกราฟสถิติ, จัดการงานบริการ, มอบหมายงานช่าง, นำทางผ่าน Google Maps, บันทึกปิดงานและรายงานผล, และตรวจสอบการชำระเงิน</li>
</ul>
<p><b>2) ขอบเขตด้านพื้นที่ให้บริการ:</b> ครอบคลุมพื้นที่ 15 ตำบลในอำเภอบ้านโป่ง จังหวัดราชบุรี ได้แก่ ต.บ้านโป่ง, ต.ท่าผา, ต.เบิกไพร, ต.ปากแรต, ต.หนองกบ, ต.กรับใหญ่, ต.คุ้งพยอม, ต.หนองปลาหมอ, ต.ดอนกระเบื้อง, ต.นครชุมน์, ต.บ้านม่วง, ต.เขาขลุง, ต.ลาดบัวขาว ฯลฯ</p>
<p><b>3) ขอบเขตด้านเทคโนโลยี:</b> พัฒนาด้วยภาษา PHP 8.2 (เชิงวัตถุ OOP), ระบบฐานข้อมูล MySQL, ภาษา HTML5, สไตล์ชีต Vanilla CSS ออกแบบเฉพาะตัว (รองรับ Desktop, iPad/Tablet, Mobile), ไลบรารีกราฟสถิติ Chart.js, และ Google Maps Geolocation</p>

<!-- ==============================================================================
     บทที่ 2: การวิเคราะห์เชิงวัตถุ (OOA)
     ============================================================================== -->
<h2>บทที่ 2: การวิเคราะห์เชิงวัตถุ (Object-Oriented Analysis - OOA)</h2>
<p>
การวิเคราะห์เชิงวัตถุ (OOA) เป็นกระบวนการทำความเข้าใจขอบเขตของปัญหา (Problem Domain), พฤติกรรมของระบบ, ผู้ที่มีปฏิสัมพันธ์กับระบบ (Actors), และการค้นหากลุ่มวัตถุหรือเอนทิตี (Entities) ที่จำเป็นต่อการดำเนินธุรกิจของร้านล้างแอร์ AirCare Pro
</p>

<h3>2.1 การวิเคราะห์ผู้มีบทบาทในระบบ (Actors Analysis)</h3>
<table class="doc-table">
  <thead>
    <tr>
      <th style="width: 25%;">Actor (ผู้มีบทบาท)</th>
      <th style="width: 35%;">คำอธิบายบทบาทหน้าที่</th>
      <th style="width: 40%;">สิทธิ์และการกระทำในระบบ</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><b>Customer (ลูกค้า)</b></td>
      <td>ผู้ที่มีความประสงค์จะรับบริการล้างแอร์ในที่พักอาศัยหรือสำนักงานในเขต อ.บ้านโป่ง</td>
      <td>• เลือกบริการและดูอัตราค่าบริการ<br>• เลือกช่างแอร์ประจำตัวที่ตนเองไว้วางใจ<br>• ปักหมุดแผนที่ Google Maps ในบ้านโป่ง<br>• ส่งคำขอจองคิวล้างแอร์<br>• ติดตามสถานะงานผ่านเบอร์โทรศัพท์<br>• พิมพ์บิลการจองและใบเสร็จรับเงิน</td>
    </tr>
    <tr>
      <td><b>Technician (ช่างแอร์)</b></td>
      <td>ช่างเทคนิคผู้เชี่ยวชาญการล้างและบำรุงรักษาเครื่องปรับอากาศ</td>
      <td>• รับงานที่ลูกค้าเลือกระบุหรือได้รับมอบหมาย<br>• ตรวจสอบพิกัดบ้านลูกค้าและกดเปิด GPS นำทาง<br>• ปรับเปลี่ยนสถานะการทำงาน (ว่าง / ไม่ว่าง)<br>• บันทึกค่าน้ำยาแอร์และปิดงานบริการ</td>
    </tr>
    <tr>
      <td><b>Admin (ผู้ดูแลระบบ)</b></td>
      <td>เจ้าหน้าที่บริหารจัดการร้านล้างแอร์ AirCare Pro</td>
      <td>• ล็อกอินเข้าสู่ระบบหลังบ้านด้วยรหัสผ่าน<br>• วิเคราะห์สถิติผ่านแดชบอร์ดและกราฟ Chart.js<br>• จัดการรายการจอง ปรับเปลี่ยนสถานะ มอบหมายช่าง<br>• บันทึกปิดงานและรายงานผลการบริการ (Service Report)<br>• จัดการทีมช่างแอร์และรายชื่อลูกค้า<br>• ตรวจสอบและยืนยันการรับชำระเงิน</td>
    </tr>
  </tbody>
</table>

<h3>2.2 Use Case Diagram & รายละเอียด Use Case</h3>
<div class="diagram-box">
  <b>[Use Case Diagram Summary - AirCare Pro Ban Pong]</b><br><br>
  <b>Customer</b> ───> (UC-01: ดูบริการ & ราคา) ───> (UC-02: เลือกช่างแอร์) ───> (UC-03: ปักหมุดแผนที่บ้านโป่ง)<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;───> (UC-04: จองคิวล้างแอร์ Real-time) ───> (UC-05: เช็คสถานะงาน) ───> (UC-06: พิมพ์บิล/ใบเสร็จ)<br><br>
  <b>Technician</b> ───> (UC-07: รับงานบริการ) ───> (UC-08: นำทาง Google Maps) ───> (UC-09: รายงานผลการล้าง)<br><br>
  <b>Admin</b> ───> (UC-10: ล็อกอินเข้าระบบ) ───> (UC-11: ดูแดชบอร์ดกราฟสถิติ) ───> (UC-12: จัดการงานบริการ)<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;───> (UC-13: บันทึกปิดงาน/จบงาน) ───> (UC-14: จัดการช่างแอร์ & การเงิน)
</div>

<h3>2.3 การค้นหา Domain Entities และ Business Rules</h3>
<p>
จากการวิเคราะห์กระบวนการทางธุรกิจของร้านล้างแอร์ สามารถจำแนกกลุ่มเอนทิตีหลัก (Core Entities) ได้ดังนี้:
</p>
<ul style="margin-left: 2cm; margin-bottom: 10pt;">
  <li><b>User:</b> เอนทิตีแม่เชิงนามธรรม กำหนดคุณลักษณะพื้นฐานของผู้ใช้งานทุกคน (รหัส, ชื่อ, เบอร์โทร, อีเมล)</li>
  <li><b>Customer:</b> เอนทิตีลูกค้า ขยายจาก User เพิ่มเติมสถานที่อยู่, ตำบลใน อ.บ้านโป่ง, และพิกัดละติจูด/ลองจิจูด</li>
  <li><b>Technician:</b> เอนทิตีช่างแอร์ ขยายจาก User เพิ่มเติมความเชี่ยวชาญ, ประสบการณ์, คะแนนรีวิว, และสถานะความพร้อม</li>
  <li><b>Admin:</b> เอนทิตีผู้ดูแลระบบ ขยายจาก User เพิ่มเติมบัญชีผู้ใช้และเมธอดการยืนยันสิทธิ์</li>
  <li><b>AirConditioner:</b> เอนทิตีเครื่องปรับอากาศ เก็บข้อมูลยี่ห้อ, ขนาด BTU, และประเภทแอร์เพื่อคำนวณอัตราค่าบริการ</li>
  <li><b>ServiceOrder:</b> เอนทิตีงานบริการล้างแอร์ เป็นศูนย์กลางของการเชื่อมโยงระหว่างลูกค้า, ช่าง, วันเวลานัดหมาย, สถานะงาน, ยอดเงิน, และพิกัดแผนที่</li>
  <li><b>Payment:</b> เอนทิตีการชำระเงิน จัดการยอดเงิน, ช่องทางการจ่าย (พร้อมเพย์/เงินสด), สถานะการรับเงิน, และเวลาที่ชำระ</li>
  <li><b>ServiceReport:</b> เอนทิตีรายงานผลการให้บริการ บันทึกผลการล้าง, แรงดันน้ำยาแอร์ก่อน-หลัง (PSI), กระแสไฟฟ้า (A), ปัญหาที่พบ และคำแนะนำ</li>
</ul>

<!-- ==============================================================================
     บทที่ 3: การออกแบบเชิงวัตถุ (OOD)
     ============================================================================== -->
<h2>บทที่ 3: การออกแบบเชิงวัตถุ (Object-Oriented Design - OOD)</h2>
<p>
การออกแบบเชิงวัตถุ (OOD) เป็นการนำผลลัพธ์จากการวิเคราะห์ OOA มากำหนดโครงสร้างสถาปัตยกรรมซอฟต์แวร์, แผนภาพคลาส (Class Diagram), ความสัมพันธ์ระหว่างคลาส, แผนภาพลำดับการทำงาน (Sequence Diagram), และการออกแบบโครงสร้างฐานข้อมูล
</p>

<h3>3.1 การออกแบบสถาปัตยกรรมระบบ (Layered Architecture)</h3>
<div class="callout">
  <b>โครงสร้างสถาปัตยกรรมแบบ 4 ชั้น (4-Tier Architecture):</b><br>
  1. <b>Presentation Layer (UI):</b> หน้าเว็บลูกค้า (index.php, track.php, receipt.php) และหน้าเว็บแอดมิน (admin/index.php, orders.php) แสดงผลแบบ Responsive<br>
  2. <b>Service / Manager Layer:</b> คลาสควบคุมกระบวนการทางธุรกิจ เช่น BookingManager (ควบคุม Transaction จองคิวและปิดงาน) และ DashboardAnalytics (ประมวลผลข้อมูลกราฟ Chart.js)<br>
  3. <b>Domain Entity Layer:</b> คลาสแทนวัตถุในระบบ เช่น User, Customer, Technician, AirConditioner, ServiceOrder, Payment, ServiceReport<br>
  4. <b>Data Access Layer:</b> คลาส Database (PDO) จัดการเชื่อมต่อฐานข้อมูล MySQL ปลอดภัยจาก SQL Injection
</div>

<h3>3.2 Class Diagram เชิงออกแบบ (Design Class Diagram)</h3>
<p>
แผนภาพคลาสแสดงคุณลักษณะ (Attributes) และพฤติกรรม (Methods) พร้อมระดับการเข้าถึง (Visibility: Private -, Protected #, Public +) ดังนี้:
</p>

<table class="doc-table">
  <thead>
    <tr>
      <th style="width: 25%;">Class Name</th>
      <th style="width: 35%;">Attributes (คุณลักษณะ)</th>
      <th style="width: 40%;">Methods (พฤติกรรม / ฟังก์ชัน)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><b>User</b><br><i>(Abstract Base Class)</i></td>
      <td># id : ?int<br># name : string<br># phone : string<br># email : ?string</td>
      <td>+ getId() : ?int<br>+ getName() : string<br>+ getPhone() : string<br>+ getEmail() : ?string<br><i>+ abstract getRole() : string</i><br>+ toArray() : array</td>
    </tr>
    <tr>
      <td><b>Customer</b><br><i>(Extends User)</i></td>
      <td>- address : string<br>- subdistrict : string<br>- latitude : float<br>- longitude : float</td>
      <td>+ getAddress() : string<br>+ getSubdistrict() : string<br>+ getLatitude() : float<br>+ getLongitude() : float<br>+ getGoogleMapsUrl() : string<br>+ getRole() : string</td>
    </tr>
    <tr>
      <td><b>Technician</b><br><i>(Extends User)</i></td>
      <td>- skill : string<br>- status : string<br>- rating : float<br>- experience : string<br>- avatar : string</td>
      <td>+ getSkill() : string<br>+ getStatus() : string<br>+ getRating() : float<br>+ isAvailable() : bool<br>+ getStatusBadge() : string<br>+ getRole() : string</td>
    </tr>
    <tr>
      <td><b>Admin</b><br><i>(Extends User)</i></td>
      <td>- username : string</td>
      <td>+ getUsername() : string<br>+ getRole() : string<br>+ static verifyCredentials(...) : bool</td>
    </tr>
    <tr>
      <td><b>AirConditioner</b></td>
      <td>- id : ?int<br>- customerId : int<br>- brand : string<br>- type : string<br>- capacity : string</td>
      <td>+ getInfo() : string<br>+ static getBasePriceForType(type) : float</td>
    </tr>
    <tr>
      <td><b>ServiceOrder</b><br><i>(Core Aggregate)</i></td>
      <td>- id : ?int<br>- customerId : int<br>- technicianId : ?int<br>- serviceDate : ?string<br>- status : string<br>- totalPrice : float<br>- bookingCode : string<br>- acType : string<br>- units : int<br>- subdistrict : string<br>- mapUrl : string</td>
      <td>+ calculatePrice(units, price, addons) : float<br>+ updateStatus(newStatus) : void<br>+ assignTechnician(techId) : void<br>+ completeOrder(report, payment) : void<br>+ getStatusBadge() : string<br>+ getFormattedDate() : string<br>+ toArray() : array</td>
    </tr>
    <tr>
      <td><b>Payment</b></td>
      <td>- id : ?int<br>- orderId : int<br>- amount : float<br>- paymentMethod : string<br>- paymentStatus : string<br>- paymentDate : ?string</td>
      <td>+ isPaid() : bool<br>+ markAsPaid() : void<br>+ getStatusBadge() : string<br>+ getFormattedAmount() : string</td>
    </tr>
    <tr>
      <td><b>ServiceReport</b></td>
      <td>- id : ?int<br>- orderId : int<br>- technicianId : int<br>- cleaningResult : string<br>- gasPsiBefore : ?string<br>- gasPsiAfter : ?string<br>- electricCurrent : ?string<br>- recommendation : string</td>
      <td>+ summary() : string<br>+ toArray() : array</td>
    </tr>
    <tr>
      <td><b>BookingManager</b><br><i>(Service Layer)</i></td>
      <td>- db : PDO</td>
      <td>+ static getBanPongSubdistricts() : array<br>+ getTechnicians() : array<br>+ processBooking(postData) : array<br>+ completeJob(orderId, reportData) : array<br>+ findBookingForCustomer(keyword) : array</td>
    </tr>
    <tr>
      <td><b>DashboardAnalytics</b><br><i>(Analytics Layer)</i></td>
      <td>- db : PDO</td>
      <td>+ getKPIs() : array<br>+ getMonthlyTrends(months) : array<br>+ getStatusBreakdown() : array<br>+ getTechnicianPerformance() : array<br>+ getAcTypeDistribution() : array</td>
    </tr>
  </tbody>
</table>

<h3>3.3 ความสัมพันธ์เชิงวัตถุ (Object Relationships)</h3>
<ul style="margin-left: 2cm; margin-bottom: 12pt;">
  <li><b>Inheritance (การสืบทอด):</b> <code>Customer</code>, <code>Technician</code>, และ <code>Admin</code> สืบทอดคุณสมบัติมาจากคลาสแม่ <code>User</code> (IS-A Relationship)</li>
  <li><b>Association (ความสัมพันธ์เชื่อมโยง):</b> <code>Customer</code> มีความสัมพันธ์กับ <code>ServiceOrder</code> ในลักษณะ 1 ต่อกลุ่ม (One-to-Many) คือลูกค้า 1 คน สามารถมีประวัติการจองได้หลายครั้ง</li>
  <li><b>Aggregation (การรวมกลุ่ม):</b> <code>ServiceOrder</code> อ้างอิงถึง <code>Technician</code> ในลักษณะที่ช่างแอร์สามารถดำรงอยู่ได้แม้ไม่มีงานบริการ (Has-A Relationship)</li>
  <li><b>Composition (ความสัมพันธ์แบบประกอบแน่นแฟ้น):</b> <code>ServiceOrder</code> ประกอบด้วย <code>Payment</code> (1 : 1) และ <code>ServiceReport</code> (1 : 0..1) หากลบงานบริการ รายการชำระเงินและรายงานผลจะถูกลบตามไปด้วย</li>
</ul>

<h3>3.4 Sequence Diagram: กระบวนการปิดงาน (Job Completion Workflow)</h3>
<div class="diagram-box">
  <b>[Sequence Diagram: Complete Job & Service Report]</b><br><br>
  Admin/Technician ──> Admin UI: กดปุ่ม [✓ ปิดงาน] และกรอกผลการล้าง + ค่าน้ำยา PSI<br>
  Admin UI ──> BookingManager::completeJob(orderId, reportData)<br>
  BookingManager ──> Database: BEGIN TRANSACTION<br>
  BookingManager ──> ServiceReport: สร้าง Entity รายงานผลการล้าง (ค่าน้ำยา PSI, กระแสไฟ A)<br>
  BookingManager ──> Database: INSERT INTO service_reports (...)<br>
  BookingManager ──> ServiceOrder: updateStatus('เสร็จสิ้น')<br>
  BookingManager ──> Payment: markAsPaid() -> payment_status = 'ชำระแล้ว'<br>
  BookingManager ──> Technician: setStatus('ว่าง') คืนสถานะพร้อมรับงาน<br>
  BookingManager ──> Database: COMMIT TRANSACTION<br>
  BookingManager ──> Admin UI: แจ้งเตือน "บันทึกปิดงานและรายงานผลสำเร็จ"<br>
  Customer ──> track.php: ตรวจสอบสถานะ เห็น "✓ ล้างเสร็จสิ้นสมบูรณ์" และอ่านรายงานช่างได้ทันที
</div>

<!-- ==============================================================================
     บทที่ 4: การเขียนโปรแกรมเชิงวัตถุ (OOP)
     ============================================================================== -->
<h2>บทที่ 4: การเขียนโปรแกรมเชิงวัตถุ (Object-Oriented Programming - OOP)</h2>
<p>
การเขียนโปรแกรมเชิงวัตถุ (OOP) ในระบบ AirCare Pro ได้นำหลักการพื้นฐาน 4 ประการ (Four Pillars of OOP) มาใช้ในการเขียนโค้ดภาษา PHP อย่างเคร่งครัด ดังนี้:
</p>

<h3>4.1 เสาหลักทั้ง 4 ของ OOP (The Four Pillars of OOP)</h3>

<h4>1) Encapsulation (การห่อหุ้มและการซ่อนข้อมูล)</h4>
<p>
แอตทริบิวต์ทุกตัวในคลาส Entity ถูกกำหนดให้เป็น <code>private</code> หรือ <code>protected</code> เพื่อป้องกันไม่ให้ภายนอกเข้าถึงหรือแก้ไขค่าได้โดยตรง และสร้างเมธอด Getter / Setter สำหรับควบคุมการเข้าถึง ตัวอย่างเช่น ในคลาส <code>ServiceOrder</code>:
</p>
<div class="code-block">
class ServiceOrder {
    private ?int $id;
    private float $totalPrice;
    private string $status;

    // การห่อหุ้มตรรกะการคำนวณราคาไว้ภายใน Entity
    public function calculatePrice(int $units, float $unitPrice, float $addonFee = 0.0): float {
        $this->units = max(1, $units);
        $this->totalPrice = ($this->units * $unitPrice) + $addonFee;
        return $this->totalPrice;
    }
}
</div>

<h4>2) Inheritance (การสืบทอดคุณสมบัติ)</h4>
<p>
การนำคุณลักษณะและพฤติกรรมพื้นฐานจากคลาสแม่มาใช้ซ้ำ โดยคลาส <code>Customer</code>, <code>Technician</code>, และ <code>Admin</code> สืบทอดมาจากคลาสแม่ <code>User</code>:
</p>
<div class="code-block">
// คลาสแม่ User
abstract class User {
    protected ?int $id;
    protected string $name;
    protected string $phone;
    public function __construct(?int $id, string $name, string $phone) { ... }
    abstract public function getRole(): string;
}

// คลาสลูก Customer สืบทอดจาก User
class Customer extends User {
    private string $address;
    private string $subdistrict;

    public function __construct(..., string $subdistrict = 'ต.บ้านโป่ง') {
        parent::__construct($id, $name, $phone, $email);
        $this->subdistrict = $subdistrict;
    }
}
</div>

<h4>3) Polymorphism (การทำงานหลากหลายรูปแบบ)</h4>
<p>
การที่ออบเจกต์ในคลาสลูกสามารถตอบสนองต่อเมธอดเดียวกันในรูปแบบที่ต่างกัน ตัวอย่างเช่น เมธอด <code>getRole()</code> ในคลาสแม่ <code>User</code> ซึ่งถูกคลาสลูก Override:
</p>
<div class="code-block">
// คลาส Customer คืนค่า 'Customer'
public function getRole(): string { return 'Customer'; }

// คลาส Technician คืนค่า 'Technician'
public function getRole(): string { return 'Technician'; }

// คลาส Admin คืนค่า 'Admin'
public function getRole(): string { return 'Admin'; }
</div>

<h4>4) Abstraction (การสร้างส่วนนามธรรม)</h4>
<p>
การซ่อนรายละเอียดความซับซ้อนของการประมวลผลฐานข้อมูลและกระบวนการทางธุรกิจไว้เบื้องหลัง Service Layer ทำให้ส่วนแสดงผล (UI) เรียกใช้งานผ่านฟังก์ชันที่เรียบง่าย เช่น <code>\$bookingManager->processBooking(\$_POST)</code> หรือ <code>\$bookingManager->completeJob(\$id, \$data)</code> โดยไม่ต้องเขียนคำสั่ง SQL ในหน้า UI
</p>

<!-- ==============================================================================
     บทที่ 5: ผลการทดสอบและการใช้งานจริง
     ============================================================================== -->
<h2>บทที่ 5: ผลการทดสอบและการใช้งานจริง (System Verification & Manual)</h2>

<h3>5.1 สรุปผลการทดสอบการทำงานของระบบ (Test Cases & Results)</h3>
<table class="doc-table">
  <thead>
    <tr>
      <th style="width: 15%;">รหัสทดสอบ</th>
      <th style="width: 30%;">กรณีทดสอบ (Test Case)</th>
      <th style="width: 35%;">ผลลัพธ์ที่คาดหวัง</th>
      <th style="width: 20%;">ผลการทดสอบ</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>TC-01</td>
      <td>ลูกค้าเลือกช่างแอร์ประจำตัว และปักหมุดตำบลใน อ.บ้านโป่ง</td>
      <td>ช่างที่เลือกถูกส่งเข้าฟอร์มจอง แผนที่ขยับไปที่ตำบลในบ้านโป่ง และคำนวณราคาสุทธิแบบ Real-time</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-02</td>
      <td>การส่งคำขอจองคิว (Booking Transaction)</td>
      <td>บันทึกข้อมูลลงตาราง customers, service_orders, air_conditioners, payments ครบถ้วน และออกรหัส AC-BP-XXXXX</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-03</td>
      <td>การออกบิลการจองและใบเสร็จรับเงิน (receipt.php)</td>
      <td>แสดงบิลมาตรฐาน มีคิวอาร์โค้ดพร้อมเพย์ ลายเซ็น และกดพิมพ์ A4 / บันทึก PDF ได้พอดีหน้ากระดาษ</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-04</td>
      <td>การปิดงาน/จบงาน โดยช่างหรือแอดมิน</td>
      <td>สถานะงานเปลี่ยนเป็น 'เสร็จสิ้น' บันทึกค่าน้ำยาแอร์ PSI และปลดช่างกลับเป็นสถานะ 'ว่าง'</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-05</td>
      <td>การแสดงผลกราฟสถิติบนแดชบอร์ดแอดมิน (Chart.js)</td>
      <td>กราฟทั้ง 4 รูปแบบ (แนวโน้มรายเดือน, สัดส่วนสถานะ, ผลงานช่าง, ประเภทแอร์) แสดงผลถูกต้องและตอบสนองได้</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-06</td>
      <td>การแยกสิทธิ์ลูกค้ากับผู้ดูแลระบบ</td>
      <td>ลูกค้าเข้าหน้าเว็บได้โดยไม่เห็นปุ่มจัดการหรือรายรับ และแอดมินเข้าถึงได้เมื่อผ่านการล็อกอินด้วย Session เท่านั้น</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
    <tr>
      <td>TC-07</td>
      <td>การแสดงผลบนอุปกรณ์ Mobile และ iPad / Tablet</td>
      <td>หน้าเว็บปรับขนาดแบบ Responsive ปุ่มกดขนาดใหญ่พอเหมาะกับนิ้วสัมผัส และไม่เกิดแถบเลื่อนแนวนอน</td>
      <td><span class="badge-tag">ผ่าน (Pass)</span></td>
    </tr>
  </tbody>
</table>

<h3>5.2 สรุปประโยชน์ที่ได้รับจากโครงงาน</h3>
<ol style="margin-left: 2cm;">
  <li>ช่วยยกระดับการให้บริการของร้านล้างแอร์ในเขตอำเภอบ้านโป่ง ให้มีความสะดวกรวดเร็ว ทันสมัย และเป็นมืออาชีพ</li>
  <li>ลดความผิดพลาดในการนัดหมายและการหลงทางของช่าง ด้วยการนำพิกัด Google Maps มาใช้ในการนำทางแบบแม่นยำ</li>
  <li>สร้างความโปร่งใสและสร้างความมั่นใจให้ลูกค้าด้วยการออกบิลการจอง ใบเสร็จรับเงิน และรายงานผลค่าน้ำยาแอร์หลังเสร็จงาน</li>
  <li>ช่วยให้ผู้บริหารร้านสามารถติดตามผลการดำเนินงานและสถิติรายรับผ่านกราฟแดชบอร์ดได้อย่างมีประสิทธิภาพ</li>
  <li>เป็นตัวอย่างการประยุกต์ใช้ระเบียบวิธีวิเคราะห์และออกแบบเชิงวัตถุ (OOA • OOD • OOP) ที่สมบูรณ์แบบในทางปฏิบัติ</li>
</ol>

</body>
</html>
HTML;

// 1. Save HTML Document format (.doc) that Microsoft Word opens natively
$docFilePath = __DIR__ . '/AirCare_Pro_OOA_OOD_OOP_Report.doc';
file_put_contents($docFilePath, $htmlDocContent);
echo "Generated Word Document: AirCare_Pro_OOA_OOD_OOP_Report.doc\n";

// 2. Also save as standalone web page for instant preview and print-to-Word
$reportPhpPath = __DIR__ . '/report.php';
$reportPhpContent = "<?php\n// Web Viewer for AirCare Pro Project Report\n?>\n" . $htmlDocContent;
// Add interactive action buttons at the top of report.php for web users
$buttonBanner = <<<BTN
<div style="background:#0f172a; padding:12px 24px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
  <div style="color:#fff; font-weight:bold; font-size:14pt; display:flex; align-items:center; gap:8px;">
    <span>❄️</span> AirCare Pro: เอกสารรายงานโครงงาน OOA • OOD • OOP
  </div>
  <div style="display:flex; gap:10px;">
    <a href="AirCare_Pro_OOA_OOD_OOP_Report.doc" download class="btn" style="background:#0284c7; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13pt; font-weight:bold;">
      📥 ดาวน์โหลดไฟล์ Word (.doc)
    </a>
    <button onclick="window.print()" style="background:#10b981; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-size:13pt; font-weight:bold; cursor:pointer;">
      🖨️ พิมพ์เอกสาร / บันทึกเป็น PDF
    </button>
    <a href="index.php" style="background:#334155; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:13pt;">
      ← กลับหน้าร้าน
    </a>
  </div>
</div>
BTN;
$reportPhpContent = str_replace('<body>', '<body>' . $buttonBanner, $reportPhpContent);
file_put_contents($reportPhpPath, $reportPhpContent);
echo "Generated Web Report Viewer: report.php\n";
