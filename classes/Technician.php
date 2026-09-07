<?php
require_once __DIR__ . '/User.php';

/**
 * OOA/OOD: Technician Entity
 * Extends User; represents an air conditioner cleaning technician.
 */
class Technician extends User {
    private string $skill;
    private string $status;
    private float $rating;
    private string $experience;
    private string $avatar;

    public function __construct(
        ?int $id,
        string $name,
        string $phone,
        string $skill = 'ล้างแอร์ทั่วไป',
        string $status = 'ว่าง',
        float $rating = 4.9,
        string $experience = 'ประสบการณ์ 5 ปี',
        string $avatar = ''
    ) {
        parent::__construct($id, $name, $phone, null);
        $this->skill = trim($skill);
        $this->status = trim($status);
        $this->rating = $rating;
        $this->experience = trim($experience);
        $this->avatar = trim($avatar);
    }

    public function getSkill(): string {
        return $this->skill;
    }

    public function setSkill(string $skill): void {
        $this->skill = trim($skill);
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = trim($status);
    }

    public function getRating(): float {
        return $this->rating;
    }

    public function setRating(float $rating): void {
        $this->rating = $rating;
    }

    public function getExperience(): string {
        return $this->experience;
    }

    public function setExperience(string $experience): void {
        $this->experience = trim($experience);
    }

    public function getAvatar(): string {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): void {
        $this->avatar = trim($avatar);
    }

    public function isAvailable(): bool {
        return $this->status === 'ว่าง';
    }

    public function getRole(): string {
        return 'Technician';
    }

    public function getStatusBadge(): string {
        if ($this->isAvailable()) {
            return '<span class="badge badge-success"><span class="pulse-dot"></span> พร้อมรับงาน</span>';
        }
        return '<span class="badge badge-warning">คิวงานเต็ม / ไม่ว่าง</span>';
    }

    public function toArray(): array {
        $data = parent::toArray();
        $data['skill'] = $this->skill;
        $data['status'] = $this->status;
        $data['rating'] = $this->rating;
        $data['experience'] = $this->experience;
        $data['avatar'] = $this->avatar;
        $data['is_available'] = $this->isAvailable();
        return $data;
    }
}