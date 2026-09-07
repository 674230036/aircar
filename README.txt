AIRCARE PRO - ระบบจัดการร้านและจองคิวล้างแอร์มืออาชีพ
============================================================
สถาปัตยกรรม: OOA (Analysis) • OOD (Design) • OOP (Programming)

1. วิธีติดตั้ง
------------------------------------------------------------
1) ติดตั้ง XAMPP แล้วเปิด Apache และ MySQL ใน XAMPP Control Panel
2) วางโฟลเดอร์ air_cleaning_system_pro ไว้ที่ C:\xampp\htdocs\
3) เปิดเบราว์เซอร์ไปที่ http://localhost/phpmyadmin
4) เลือก Import ไฟล์ database.sql เข้าสู่ฐานข้อมูล (สร้างฐานข้อมูล air_cleaning ให้อัตโนมัติ)
5) เข้าใช้งานระบบ:
   • หน้าระบบลูกค้า (Customer Booking):
     http://localhost/air_cleaning_system_pro/
   • หน้าเช็คสถานะการจอง (Order Tracking):
     http://localhost/air_cleaning_system_pro/track.php
   • แดชบอร์ดผู้ดูแลระบบพร้อมกราฟสถิติ (Admin Portal):
     http://localhost/air_cleaning_system_pro/admin/
     - ชื่อผู้ใช้: admin
     - รหัสผ่าน: admin123

2. จุดเด่นของระบบ
------------------------------------------------------------
✓ การแบ่งแยกสิทธิ์ชัดเจน (Customer Portal vs Admin Dashboard)
  - ลูกค้ามีหน้าจองคิวและเช็คสถานะของตนเอง โดยไม่มีสิทธิ์เข้าถึงหรือแก้ไขข้อมูลหลังบ้าน
  - ผู้ดูแลระบบมีระบบตรวจสอบสิทธิ์ Session Login ปลอดภัย 100%

✓ ระบบจองคิวล้างแอร์สำหรับลูกค้า
  - สามารถกรอกชื่อ-นามสกุล เบอร์โทร ที่อยู่
  - เลือกระบุช่างแอร์ที่ต้องการจองได้โดยตรง ("จองกับช่างคนนี้")
  - เลือกประเภทแอร์ (ติดผนัง, แขวน, 4 ทิศทาง) และบริการเสริมพิเศษ
  - ระบบคำนวณราคาสุทธิแบบ Real-time พร้อมออกรหัสใบจอง
  - ลูกค้าตรวจสอบสถานะงานผ่าน track.php ด้วยเบอร์โทรศัพท์

✓ แดชบอร์ดวิเคราะห์ข้อมูลผู้ดูแลระบบพร้อมกราฟสถิติ (Chart.js)
  - กราฟแนวโน้มรายรับและจำนวนงานบริการรายเดือน (Area/Line Chart)
  - กราฟสัดส่วนสถานะงานบริการ (Doughnut Chart)
  - กราฟสถิติผลงานช่างแอร์แต่ละคน (Bar Chart)
  - กราฟสัดส่วนประเภทเครื่องปรับอากาศยอดนิยม (Polar Chart)
  - ตารางงานล่าสุดพร้อม Dropdown อัปเดตสถานะงานได้ทันที

3. โครงสร้างคลาส OOA • OOD • OOP (โฟลเดอร์ classes/)
------------------------------------------------------------
• classes/User.php               Base Class รองรับ Inheritance & Encapsulation
• classes/Customer.php           Entity ลูกค้า (Extends User)
• classes/Technician.php         Entity ช่างแอร์ (Extends User)
• classes/Admin.php              Entity ผู้ดูแลระบบ (Extends User)
• classes/AirConditioner.php     Entity เครื่องปรับอากาศและอัตราค่าบริการ
• classes/ServiceOrder.php       Entity งานบริการและตรรกะการคำนวณราคา
• classes/Payment.php            Entity การชำระเงินและตรวจสอบสถานะ
• classes/ServiceReport.php      Entity รายงานผลการให้บริการ
• classes/BookingManager.php     Service Layer จัดการ Transaction การจอง
• classes/DashboardAnalytics.php Analytics Layer ประมวลผลกราฟ Chart.js
