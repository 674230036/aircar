<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = (new Database())->connect();
    echo "Connected to MySQL successfully.\n";

    // 1. Ensure columns in technicians
    $cols = $db->query("SHOW COLUMNS FROM technicians")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('rating', $cols)) {
        $db->exec("ALTER TABLE technicians ADD COLUMN rating DECIMAL(2,1) DEFAULT 4.9");
        echo "Added rating to technicians.\n";
    }
    if (!in_array('experience', $cols)) {
        $db->exec("ALTER TABLE technicians ADD COLUMN experience VARCHAR(100) DEFAULT 'ประสบการณ์ 5 ปี'");
        echo "Added experience to technicians.\n";
    }
    if (!in_array('avatar', $cols)) {
        $db->exec("ALTER TABLE technicians ADD COLUMN avatar VARCHAR(255) DEFAULT ''");
        echo "Added avatar to technicians.\n";
    }

    // 2. Ensure columns in service_orders
    $orderCols = $db->query("SHOW COLUMNS FROM service_orders")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('booking_code', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN booking_code VARCHAR(50) NULL");
        echo "Added booking_code to service_orders.\n";
    }
    if (!in_array('ac_type', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN ac_type VARCHAR(100) DEFAULT 'แอร์ติดผนัง'");
        echo "Added ac_type to service_orders.\n";
    }
    if (!in_array('units', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN units INT DEFAULT 1");
        echo "Added units to service_orders.\n";
    }
    if (!in_array('created_at', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "Added created_at to service_orders.\n";
    }

    // 3. Update existing technicians with avatar and rating if empty
    $db->exec("UPDATE technicians SET rating=4.9, experience='ประสบการณ์ 8 ปี เชี่ยวชาญแอร์บ้าน', avatar='tech1.png' WHERE id=1");
    $db->exec("UPDATE technicians SET rating=4.8, experience='ประสบการณ์ 6 ปี เชี่ยวชาญแอร์สำนักงาน/แขวน', avatar='tech2.png' WHERE id=2");
    $db->exec("UPDATE technicians SET rating=5.0, experience='ประสบการณ์ 10 ปี ช่างเทคนิคระบบ Inverter', avatar='tech3.png' WHERE id=3");

    // Add another technician if less than 4
    $techCount = $db->query("SELECT COUNT(*) FROM technicians")->fetchColumn();
    if ($techCount < 4) {
        $stmt = $db->prepare("INSERT INTO technicians (name, phone, skill, status, rating, experience, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['ช่างวิทย์ ชำนาญการ', '0844444444', 'แอร์คาสเซ็ท 4 ทิศทาง', 'ว่าง', 4.9, 'ประสบการณ์ 7 ปี ล้างแอร์ฝังฝ้า 4 ทิศทาง', 'tech4.png']);
        echo "Added 4th technician.\n";
    }

    // 4. Update booking codes for existing orders if empty
    $db->exec("UPDATE service_orders SET booking_code = CONCAT('AC-', LPAD(id, 5, '0')) WHERE booking_code IS NULL OR booking_code = ''");

    // 5. Seed some realistic orders across different months (May - September 2026) for Chart.js if fewer than 10 orders
    $orderCount = $db->query("SELECT COUNT(*) FROM service_orders")->fetchColumn();
    if ($orderCount < 10) {
        echo "Seeding additional service orders for dashboard charts...\n";
        
        // Ensure customers 4, 5, 6 exist
        $custStmt = $db->prepare("INSERT IGNORE INTO customers (id, name, phone, email, address) VALUES 
            (4, 'คุณวิภาวรรณ สดใส', '0891234567', 'wipawan@gmail.com', 'บางแค กรุงเทพมหานคร'),
            (5, 'คุณธนกฤต มั่งมี', '0859876543', 'tanakrit@corp.co.th', 'เมือง นนทบุรี'),
            (6, 'คุณลลิตา วัฒนกุล', '0823456789', 'lalita@outlook.com', 'ศาลายา นครปฐม'),
            (7, 'คุณชาญชัย รุ่งเรือง', '0817778899', 'chanchai@mail.com', 'คลองเตย กรุงเทพมหานคร')
        ");
        $custStmt->execute();

        $sampleOrders = [
            [1, 1, '2026-05-12 10:00:00', 'เสร็จสิ้น', 1200.00, 'ล้างแอร์ติดผนัง 2 เครื่อง + อบโอโซน', 'AC-00004', 'แอร์ติดผนัง', 2, '2026-05-10 09:00:00'],
            [2, 2, '2026-06-05 13:30:00', 'เสร็จสิ้น', 2400.00, 'ล้างแอร์สำนักงาน 3 เครื่อง', 'AC-00005', 'แอร์แขวน/ตั้งพื้น', 3, '2026-06-03 11:00:00'],
            [4, 3, '2026-06-18 10:00:00', 'เสร็จสิ้น', 1000.00, 'ล้างแอร์ระบบ Inverter 2 เครื่อง', 'AC-00006', 'แอร์ติดผนัง', 2, '2026-06-16 14:00:00'],
            [5, 4, '2026-07-02 09:00:00', 'เสร็จสิ้น', 3200.00, 'ล้างแอร์ฝังฝ้า 4 ทิศทาง 2 เครื่อง', 'AC-00007', 'แอร์สี่ทิศทาง (Cassette)', 2, '2026-06-30 16:00:00'],
            [6, 1, '2026-07-20 15:00:00', 'เสร็จสิ้น', 1500.00, 'ล้างแอร์ติดผนัง 3 เครื่อง', 'AC-00008', 'แอร์ติดผนัง', 3, '2026-07-18 10:30:00'],
            [7, 2, '2026-08-08 11:00:00', 'เสร็จสิ้น', 1800.00, 'ล้างแอร์แขวน 2 เครื่อง + เติมน้ำยา', 'AC-00009', 'แอร์แขวน/ตั้งพื้น', 2, '2026-08-06 13:00:00'],
            [4, 3, '2026-08-25 14:00:00', 'เสร็จสิ้น', 1000.00, 'ล้างแอร์ 2 เครื่อง', 'AC-00010', 'แอร์ติดผนัง', 2, '2026-08-23 09:00:00'],
            [5, 1, '2026-09-02 10:00:00', 'เสร็จสิ้น', 1600.00, 'ล้างแอร์บ้าน 2 เครื่อง + อบโอโซน', 'AC-00011', 'แอร์ติดผนัง', 2, '2026-08-31 15:00:00'],
            [6, 4, '2026-09-08 13:30:00', 'นัดหมายแล้ว', 1600.00, 'ล้างแอร์สี่ทิศทาง 1 เครื่อง', 'AC-00012', 'แอร์สี่ทิศทาง (Cassette)', 1, '2026-09-06 12:00:00'],
            [7, 2, '2026-09-09 10:00:00', 'รอดำเนินการ', 1000.00, 'ล้างแอร์บ้าน 2 เครื่อง', 'AC-00013', 'แอร์ติดผนัง', 2, '2026-09-07 08:30:00'],
        ];

        $insOrder = $db->prepare("INSERT INTO service_orders (customer_id, technician_id, service_date, status, total_price, note, booking_code, ac_type, units, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insPay = $db->prepare("INSERT INTO payments (order_id, amount, payment_method, payment_status, payment_date) VALUES (?, ?, ?, ?, ?)");

        foreach ($sampleOrders as $so) {
            $insOrder->execute($so);
            $newOrderId = $db->lastInsertId();
            $status = $so[3];
            $payStatus = ($status === 'เสร็จสิ้น') ? 'ชำระแล้ว' : 'รอตรวจสอบ';
            $payDate = ($status === 'เสร็จสิ้น') ? $so[2] : null;
            $insPay->execute([$newOrderId, $so[4], 'โอนผ่านธนาคาร', $payStatus, $payDate]);
        }
        echo "Sample orders and payments inserted successfully.\n";
    }

    echo "Database setup completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
