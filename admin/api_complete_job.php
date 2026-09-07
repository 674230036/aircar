<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/BookingManager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

try {
    $db = (new Database())->connect();
    $bookingManager = new BookingManager($db);

    $orderId = (int)($_POST['order_id'] ?? 0);
    if ($orderId <= 0) {
        throw new Exception("ไม่พบรหัสงานบริการ");
    }

    $result = $bookingManager->completeJob($orderId, $_POST);

    $_SESSION['admin_flash_success'] = "✓ บันทึกปิดงานบริการ #{$orderId} และออกรายงานผลการล้างสำเร็จเรียบร้อยแล้ว!";
    header('Location: orders.php');
    exit;
} catch (Exception $e) {
    $_SESSION['admin_flash_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    header('Location: orders.php');
    exit;
}
