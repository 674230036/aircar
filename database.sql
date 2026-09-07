CREATE DATABASE IF NOT EXISTS air_cleaning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE air_cleaning;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS service_reports, payments, service_orders, air_conditioners, technicians, customers;
SET FOREIGN_KEY_CHECKS=1;

-- 1. Customers Table (ข้อมูลลูกค้า)
CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100) NULL,
  address TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Technicians Table (ข้อมูลช่างแอร์)
CREATE TABLE technicians (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  skill VARCHAR(100) NOT NULL,
  status VARCHAR(30) DEFAULT 'ว่าง',
  rating DECIMAL(2,1) DEFAULT 4.9,
  experience VARCHAR(100) DEFAULT 'ประสบการณ์ 5 ปี',
  avatar VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Air Conditioners Table (ข้อมูลเครื่องปรับอากาศ)
CREATE TABLE air_conditioners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  brand VARCHAR(100) DEFAULT 'General',
  model VARCHAR(100) DEFAULT 'Standard',
  type VARCHAR(50) DEFAULT 'แอร์ติดผนัง',
  capacity VARCHAR(50) DEFAULT '12000 BTU',
  location VARCHAR(255) DEFAULT 'ที่พักอาศัย',
  last_cleaning DATE NULL,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Service Orders Table (งานบริการจองคิวล้างแอร์)
CREATE TABLE service_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  technician_id INT NULL,
  service_date DATETIME NULL,
  status VARCHAR(50) DEFAULT 'รอดำเนินการ',
  total_price DECIMAL(10,2) DEFAULT 0.00,
  note TEXT NULL,
  booking_code VARCHAR(50) NULL,
  ac_type VARCHAR(100) DEFAULT 'แอร์ติดผนัง',
  units INT DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY(technician_id) REFERENCES technicians(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Payments Table (ข้อมูลการชำระเงิน)
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  payment_method VARCHAR(50) DEFAULT 'พร้อมเพย์ QR Code',
  payment_status VARCHAR(30) DEFAULT 'รอตรวจสอบ',
  payment_date DATETIME NULL,
  FOREIGN KEY(order_id) REFERENCES service_orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Service Reports Table (รายงานผลการบริการ)
CREATE TABLE service_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  technician_id INT NOT NULL,
  cleaning_result TEXT NULL,
  problem_found TEXT NULL,
  recommendation TEXT NULL,
  completed_at DATETIME NULL,
  FOREIGN KEY(order_id) REFERENCES service_orders(id) ON DELETE CASCADE,
  FOREIGN KEY(technician_id) REFERENCES technicians(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- Sample Initial Data (ข้อมูลจำลองเริ่มต้นสำหรับการทดสอบและแสดงกราฟสถิติ)
-- ==============================================================================

-- Customers
INSERT INTO customers (id, name, phone, email, address) VALUES
(1, 'คุณสมชาย ใจดี', '0812345678', 'somchai@example.com', '12/4 หมู่ 5 ต.สนามจันทร์ อ.เมือง จ.นครปฐม'),
(2, 'คุณสุภณัฐ จันทร์เปรม', '0898765432', 'suphanat@example.com', '88/2 ต.พระปฐมเจดีย์ อ.เมือง จ.นครปฐม'),
(3, 'คุณกิตติพงษ์ สายลม', '0865551234', 'kittipong@example.com', '45/12 ถนนพหลโยธิน แขวงลาดยาว เขตจตุจักร กรุงเทพฯ'),
(4, 'คุณวิภาวรรณ สดใส', '0891234567', 'wipawan@gmail.com', '789 ซอยเพชรเกษม 63 แขวงหลักสอง เขตบางแค กรุงเทพฯ'),
(5, 'คุณธนกฤต มั่งมี', '0859876543', 'tanakrit@corp.co.th', '101/5 หมู่บ้านมงคล ต.บางกร่าง อ.เมือง จ.นนทบุรี'),
(6, 'คุณลลิตา วัฒนกุล', '0823456789', 'lalita@outlook.com', '24 หมู่ 4 ต.ศาลายา อ.พุทธมณฑล จ.นครปฐม'),
(7, 'คุณชาญชัย รุ่งเรือง', '0817778899', 'chanchai@mail.com', '33/9 ถนนพระราม 4 แขวงคลองเตย เขตคลองเตย กรุงเทพฯ');

-- Technicians (พร้อมคะแนน ประสบการณ์ และความชำนาญ)
INSERT INTO technicians (id, name, phone, skill, status, rating, experience, avatar) VALUES
(1, 'ช่างเอก (หัวหน้าช่าง)', '0811111111', 'ล้างแอร์บ้าน / ระบบล้างล้ำลึก', 'ว่าง', 4.9, 'ประสบการณ์ 8 ปี เชี่ยวชาญแอร์บ้านและคอนโด', 'tech1.png'),
(2, 'ช่างบอย', '0822222222', 'แอร์สำนักงาน / แอร์แขวน', 'ว่าง', 4.8, 'ประสบการณ์ 6 ปี เชี่ยวชาญแอร์สำนักงานและร้านค้า', 'tech2.png'),
(3, 'ช่างนัท', '0833333333', 'ระบบ Inverter ประหยัดไฟ', 'ว่าง', 5.0, 'ประสบการณ์ 10 ปี ช่างเทคนิคระบบอินเวอร์เตอร์ไดกิ้น/มิตซู', 'tech3.png'),
(4, 'ช่างวิทย์ ชำนาญการ', '0844444444', 'แอร์สี่ทิศทาง (Cassette)', 'ว่าง', 4.9, 'ประสบการณ์ 7 ปี เชี่ยวชาญแอร์ฝังฝ้า 4 ทิศทาง', 'tech4.png');

-- Air Conditioners
INSERT INTO air_conditioners (customer_id, brand, model, type, capacity, location, last_cleaning) VALUES
(1, 'Daikin', 'FTKF Super Cool', 'แอร์ติดผนัง', '12000 BTU', 'ห้องนอนใหญ่', '2026-06-10'),
(2, 'Mitsubishi', 'MSY-GR Series', 'แอร์ติดผนัง', '18000 BTU', 'ห้องรับแขก', '2026-07-05'),
(3, 'Carrier', 'XInverter Plus', 'แอร์ติดผนัง', '12000 BTU', 'สำนักงาน', '2026-05-20'),
(4, 'Panasonic', 'Aero Series', 'แอร์ติดผนัง', '15000 BTU', 'บ้านพัก', '2026-06-15'),
(5, 'Daikin', 'SkyAir Cassette', 'แอร์สี่ทิศทาง (Cassette)', '30000 BTU', 'ออฟฟิศชั้น 2', '2026-06-30');

-- Service Orders (กระจายตามเดือนพฤษภาคม - กันยายน 2026 สำหรับกราฟแดชบอร์ด)
INSERT INTO service_orders (id, customer_id, technician_id, service_date, status, total_price, note, booking_code, ac_type, units, created_at) VALUES
(1, 1, 1, '2026-05-12 10:00:00', 'เสร็จสิ้น', 1200.00, 'ล้างแอร์ติดผนัง 2 เครื่อง + อบโอโซนฆ่าเชื้อ', 'AC-202605-001', 'แอร์ติดผนัง', 2, '2026-05-10 09:00:00'),
(2, 2, 2, '2026-06-05 13:30:00', 'เสร็จสิ้น', 2400.00, 'ล้างแอร์แขวนสำนักงาน 3 เครื่อง', 'AC-202606-002', 'แอร์แขวน/ตั้งพื้น', 3, '2026-06-03 11:00:00'),
(3, 4, 3, '2026-06-18 10:00:00', 'เสร็จสิ้น', 1000.00, 'ล้างแอร์ระบบ Inverter 2 เครื่อง', 'AC-202606-003', 'แอร์ติดผนัง', 2, '2026-06-16 14:00:00'),
(4, 5, 4, '2026-07-02 09:00:00', 'เสร็จสิ้น', 3200.00, 'ล้างแอร์ฝังฝ้า 4 ทิศทาง 2 เครื่อง', 'AC-202607-004', 'แอร์สี่ทิศทาง (Cassette)', 2, '2026-06-30 16:00:00'),
(5, 6, 1, '2026-07-20 15:00:00', 'เสร็จสิ้น', 1500.00, 'ล้างแอร์ติดผนัง 3 เครื่อง', 'AC-202607-005', 'แอร์ติดผนัง', 3, '2026-07-18 10:30:00'),
(6, 7, 2, '2026-08-08 11:00:00', 'เสร็จสิ้น', 1800.00, 'ล้างแอร์แขวน 2 เครื่อง + เติมน้ำยาแอร์', 'AC-202608-006', 'แอร์แขวน/ตั้งพื้น', 2, '2026-08-06 13:00:00'),
(7, 4, 3, '2026-08-25 14:00:00', 'เสร็จสิ้น', 1000.00, 'ล้างแอร์บ้าน 2 เครื่อง', 'AC-202608-007', 'แอร์ติดผนัง', 2, '2026-08-23 09:00:00'),
(8, 1, 1, '2026-09-02 10:00:00', 'เสร็จสิ้น', 1600.00, 'ล้างแอร์บ้าน 2 เครื่อง + อบโอโซน', 'AC-202609-008', 'แอร์ติดผนัง', 2, '2026-08-31 15:00:00'),
(9, 3, 4, '2026-09-08 13:30:00', 'นัดหมายแล้ว', 1600.00, 'ล้างแอร์สี่ทิศทาง 1 เครื่อง', 'AC-202609-009', 'แอร์สี่ทิศทาง (Cassette)', 1, '2026-09-06 12:00:00'),
(10, 7, 2, '2026-09-09 10:00:00', 'รอดำเนินการ', 1000.00, 'ล้างแอร์บ้าน 2 เครื่อง', 'AC-202609-010', 'แอร์ติดผนัง', 2, '2026-09-07 08:30:00'),
(11, 2, 3, '2026-09-10 14:00:00', 'กำลังดำเนินการ', 1500.00, 'ล้างแอร์บ้าน 3 เครื่อง', 'AC-202609-011', 'แอร์ติดผนัง', 3, '2026-09-07 09:15:00');

-- Payments
INSERT INTO payments (order_id, amount, payment_method, payment_status, payment_date) VALUES
(1, 1200.00, 'โอนผ่านธนาคาร', 'ชำระแล้ว', '2026-05-12 12:30:00'),
(2, 2400.00, 'โอนผ่านธนาคาร', 'ชำระแล้ว', '2026-06-05 16:00:00'),
(3, 1000.00, 'พร้อมเพย์ QR Code', 'ชำระแล้ว', '2026-06-18 11:45:00'),
(4, 3200.00, 'โอนผ่านธนาคาร', 'ชำระแล้ว', '2026-07-02 12:00:00'),
(5, 1500.00, 'พร้อมเพย์ QR Code', 'ชำระแล้ว', '2026-07-20 17:30:00'),
(6, 1800.00, 'เงินสด', 'ชำระแล้ว', '2026-08-08 13:00:00'),
(7, 1000.00, 'พร้อมเพย์ QR Code', 'ชำระแล้ว', '2026-08-25 16:15:00'),
(8, 1600.00, 'พร้อมเพย์ QR Code', 'ชำระแล้ว', '2026-09-02 12:20:00'),
(9, 1600.00, 'พร้อมเพย์ QR Code', 'รอตรวจสอบ', NULL),
(10, 1000.00, 'เงินสด', 'รอตรวจสอบ', NULL),
(11, 1500.00, 'พร้อมเพย์ QR Code', 'รอตรวจสอบ', NULL);

-- Service Reports
INSERT INTO service_reports (order_id, technician_id, cleaning_result, problem_found, recommendation, completed_at) VALUES
(1, 1, 'ล้างแผงคอยล์เย็น โบลเวอร์ และแผ่นกรองสะอาดเอี่ยม', 'พบฝุ่นสะสมหนาแน่นเล็กน้อย', 'แนะนำให้ล้างซ้ำทุก 6 เดือน', '2026-05-12 12:00:00'),
(2, 2, 'ล้างแอร์แขวนขนาดใหญ่ ลมแรงขึ้น 40%', 'มอเตอร์พัดลมมีเสียงดังเล็กน้อยแต่ใช้งานได้ปกติ', 'ควรตรวจเช็คลูกปืนมอเตอร์ในครั้งถัดไป', '2026-06-05 15:30:00'),
(8, 1, 'ล้างแอร์บ้าน 2 เครื่อง อบโอโซนฆ่าเชื้อหอมสดชื่น', 'ท่อน้ำทิ้งมีเมือกสะสม ได้ทำการเป่าล้างโล่งแล้ว', 'ควรหมั่นล้างแผ่นฟิลเตอร์ทุกเดือน', '2026-09-02 12:00:00');
