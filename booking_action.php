<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/BookingManager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $db = (new Database())->connect();
    $bookingManager = new BookingManager($db);

    $result = $bookingManager->processBooking($_POST);

    // Store in session for success display
    $_SESSION['last_booking'] = [
        'order_id' => $result['order_id'],
        'booking_code' => $result['booking_code'],
        'customer_name' => $result['customer']->getName(),
        'customer_phone' => $result['customer']->getPhone(),
        'customer_address' => $result['customer']->getAddress(),
        'technician_name' => $result['technician'] ? $result['technician']->getName() : 'ทางร้านจัดส่งช่างที่เหมาะสมให้',
        'technician_phone' => $result['technician'] ? $result['technician']->getPhone() : '',
        'total_price' => $result['total_price'],
        'service_date' => $result['formatted_date'],
        'ac_type' => $result['ac_type'],
        'units' => $result['units'],
        'payment_method' => $result['payment_method']
    ];

    header('Location: index.php?booking_success=1#booking-success');
    exit;
} catch (Exception $e) {
    $_SESSION['booking_error'] = $e->getMessage();
    header('Location: index.php?booking_error=1#booking-form');
    exit;
}
