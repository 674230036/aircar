<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = (new Database())->connect();
    echo "Connected to database.\n";

    // 1. Add subdistrict, latitude, longitude, map_url to customers
    $custCols = $db->query("SHOW COLUMNS FROM customers")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('subdistrict', $custCols)) {
        $db->exec("ALTER TABLE customers ADD COLUMN subdistrict VARCHAR(100) DEFAULT 'ต.บ้านโป่ง'");
        echo "Added subdistrict to customers.\n";
    }
    if (!in_array('latitude', $custCols)) {
        $db->exec("ALTER TABLE customers ADD COLUMN latitude DECIMAL(10,7) DEFAULT 13.8164000");
        echo "Added latitude to customers.\n";
    }
    if (!in_array('longitude', $custCols)) {
        $db->exec("ALTER TABLE customers ADD COLUMN longitude DECIMAL(10,7) DEFAULT 99.8774000");
        echo "Added longitude to customers.\n";
    }

    // 2. Add map_url to service_orders
    $orderCols = $db->query("SHOW COLUMNS FROM service_orders")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('subdistrict', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN subdistrict VARCHAR(100) DEFAULT 'ต.บ้านโป่ง'");
        echo "Added subdistrict to service_orders.\n";
    }
    if (!in_array('map_url', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN map_url VARCHAR(500) NULL");
        echo "Added map_url to service_orders.\n";
    }
    if (!in_array('latitude', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN latitude DECIMAL(10,7) DEFAULT 13.8164000");
        echo "Added latitude to service_orders.\n";
    }
    if (!in_array('longitude', $orderCols)) {
        $db->exec("ALTER TABLE service_orders ADD COLUMN longitude DECIMAL(10,7) DEFAULT 99.8774000");
        echo "Added longitude to service_orders.\n";
    }

    // 3. Add technical measurement columns to service_reports
    $reportCols = $db->query("SHOW COLUMNS FROM service_reports")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('gas_psi_before', $reportCols)) {
        $db->exec("ALTER TABLE service_reports ADD COLUMN gas_psi_before VARCHAR(20) DEFAULT NULL");
        echo "Added gas_psi_before to service_reports.\n";
    }
    if (!in_array('gas_psi_after', $reportCols)) {
        $db->exec("ALTER TABLE service_reports ADD COLUMN gas_psi_after VARCHAR(20) DEFAULT NULL");
        echo "Added gas_psi_after to service_reports.\n";
    }
    if (!in_array('electric_current', $reportCols)) {
        $db->exec("ALTER TABLE service_reports ADD COLUMN electric_current VARCHAR(20) DEFAULT NULL");
        echo "Added electric_current to service_reports.\n";
    }

    // 4. Update existing sample orders with Ban Pong addresses and Google Map links
    $banPongLocations = [
        ['ต.บ้านโป่ง', 13.8164, 99.8774, '142 ถนนทรงพล ต.บ้านโป่ง อ.บ้านโป่ง จ.ราชบุรี (ใกล้หอนาฬิกาบ้านโป่ง)'],
        ['ต.ท่าผา', 13.8055, 99.8920, '55 หมู่ 3 ต.ท่าผา อ.บ้านโป่ง จ.ราชบุรี (ใกล้โรงงานกระดาษ)'],
        ['ต.เบิกไพร', 13.8280, 99.8650, '88/2 หมู่ 1 ต.เบิกไพร อ.บ้านโป่ง จ.ราชบุรี'],
        ['ต.ปากแรต', 13.8010, 99.8550, '19 หมู่ 6 ต.ปากแรต อ.บ้านโป่ง จ.ราชบุรี'],
        ['ต.หนองกบ', 13.8450, 99.9100, '33 หมู่ 4 ต.หนองกบ อ.บ้านโป่ง จ.ราชบุรี'],
        ['ต.กรับใหญ่', 13.9100, 99.8300, '120 หมู่ 2 ต.กรับใหญ่ อ.บ้านโป่ง จ.ราชบุรี']
    ];

    $orders = $db->query("SELECT id FROM service_orders")->fetchAll(PDO::FETCH_COLUMN);
    $idx = 0;
    foreach ($orders as $oid) {
        $loc = $banPongLocations[$idx % count($banPongLocations)];
        $lat = $loc[1];
        $lng = $loc[2];
        $sub = $loc[0];
        $addr = $loc[3];
        $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";
        $db->prepare("UPDATE service_orders SET subdistrict = ?, latitude = ?, longitude = ?, map_url = ? WHERE id = ?")->execute([$sub, $lat, $lng, $mapUrl, $oid]);
        $idx++;
    }

    echo "Updated sample orders with Ban Pong coordinates and Google Map URLs.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
