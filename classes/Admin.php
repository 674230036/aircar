<?php
require_once __DIR__ . '/User.php';

/**
 * OOA/OOD: Admin Entity
 * Extends User; represents a system administrator managing orders, technicians, and analytics.
 */
class Admin extends User {
    private string $username;

    public function __construct(?int $id, string $name, string $username, string $phone = '', ?string $email = null) {
        parent::__construct($id, $name, $phone, $email);
        $this->username = $username;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getRole(): string {
        return 'Admin';
    }

    public static function verifyCredentials(string $username, string $password): bool {
        // Standard default admin credentials for AirCare Pro
        return ($username === 'admin' && $password === 'admin123') ||
               ($username === 'manager' && $password === 'admin123');
    }
}
