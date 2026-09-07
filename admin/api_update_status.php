<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/ServiceOrder.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method Not Allowed');
    }

    $db = (new Database())->connect();
    $orderId = (int)($_POST['order_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');

    if ($orderId <= 0) {
        throw new Exception('Invalid Order ID');
    }

    $validStatuses = ['รอดำเนินการ', 'นัดหมายแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'];
    if (!in_array($newStatus, $validStatuses)) {
        throw new Exception('สถานะไม่ถูกต้อง');
    }

    $stmt = $db->prepare("UPDATE service_orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);

    // If marked as 'เสร็จสิ้น', optionally update payment to 'ชำระแล้ว' if pending
    if ($newStatus === 'เสร็จสิ้น') {
        $db->prepare("UPDATE payments SET payment_status = 'ชำระแล้ว', payment_date = NOW() WHERE order_id = ? AND payment_status = 'รอตรวจสอบ'")->execute([$orderId]);
    }

    echo json_encode(['success' => true, 'order_id' => $orderId, 'status' => $newStatus]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
