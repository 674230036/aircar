<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAdminAuth(): void {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentAdmin(): array {
    return [
        'username' => $_SESSION['admin_username'] ?? 'admin',
        'name' => $_SESSION['admin_name'] ?? 'ผู้ดูแลระบบ AirCare'
    ];
}
