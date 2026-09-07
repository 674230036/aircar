<?php
require_once __DIR__ . '/Customer.php';
require_once __DIR__ . '/Technician.php';
require_once __DIR__ . '/ServiceReport.php';
require_once __DIR__ . '/Payment.php';

/**
 * OOA/OOD: ServiceOrder Entity
 * Core aggregate representing an air cleaning appointment, including customer,
 * assigned technician, Ban Pong geolocation coordinates, financial cost,
 * and service state lifecycle.
 */
class ServiceOrder {
    private ?int $id;
    private int $customerId;
    private ?int $technicianId;
    private ?string $serviceDate;
    private string $status;
    private float $totalPrice;
    private string $note;
    private ?string $bookingCode;
    private string $acType;
    private int $units;
    private string $subdistrict;
    private float $latitude;
    private float $longitude;
    private ?string $mapUrl;

    // Aggregation references
    private ?Customer $customer = null;
    private ?Technician $technician = null;
    private ?ServiceReport $serviceReport = null;

    public function __construct(
        ?int $id,
        int $customerId,
        ?int $technicianId = null,
        ?string $serviceDate = null,
        string $status = 'รอดำเนินการ',
        float $totalPrice = 0.0,
        string $note = '',
        ?string $bookingCode = null,
        string $acType = 'แอร์ติดผนัง',
        int $units = 1,
        string $subdistrict = 'ต.บ้านโป่ง',
        float $latitude = 13.8164,
        float $longitude = 99.8774,
        ?string $mapUrl = null
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->technicianId = $technicianId;
        $this->serviceDate = $serviceDate;
        $this->status = trim($status);
        $this->totalPrice = $totalPrice;
        $this->note = trim($note);
        $this->bookingCode = $bookingCode ?: ($id ? 'AC-' . str_pad((string)$id, 5, '0', STR_PAD_LEFT) : null);
        $this->acType = trim($acType);
        $this->units = max(1, $units);
        $this->subdistrict = trim($subdistrict) ?: 'ต.บ้านโป่ง';
        $this->latitude = $latitude ?: 13.8164;
        $this->longitude = $longitude ?: 99.8774;
        $this->mapUrl = $mapUrl ?: "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomerId(): int { return $this->customerId; }
    public function getTechnicianId(): ?int { return $this->technicianId; }
    public function getServiceDate(): ?string { return $this->serviceDate; }
    public function getStatus(): string { return $this->status; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getNote(): string { return $this->note; }
    public function getBookingCode(): string {
        return $this->bookingCode ?: ($this->id ? 'AC-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT) : 'AC-TEMP');
    }
    public function getAcType(): string { return $this->acType; }
    public function getUnits(): int { return $this->units; }
    public function getSubdistrict(): string { return $this->subdistrict; }
    public function getLatitude(): float { return $this->latitude; }
    public function getLongitude(): float { return $this->longitude; }
    public function getMapUrl(): string {
        return $this->mapUrl ?: "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function setCustomer(Customer $customer): void { $this->customer = $customer; }
    public function getCustomer(): ?Customer { return $this->customer; }

    public function setTechnician(Technician $technician): void { $this->technician = $technician; }
    public function getTechnician(): ?Technician { return $this->technician; }

    public function setServiceReport(ServiceReport $report): void { $this->serviceReport = $report; }
    public function getServiceReport(): ?ServiceReport { return $this->serviceReport; }

    /**
     * Business Logic: Complete the order lifecycle
     */
    public function completeOrder(ServiceReport $report, Payment $payment): void {
        $this->status = 'เสร็จสิ้น';
        $this->serviceReport = $report;
        $payment->markAsPaid();
    }

    public function calculatePrice(int $units, float $unitPrice, float $addonFee = 0.0): float {
        $this->units = max(1, $units);
        $this->totalPrice = ($this->units * $unitPrice) + $addonFee;
        return $this->totalPrice;
    }

    public function updateStatus(string $newStatus): void {
        $validStatuses = ['รอดำเนินการ', 'นัดหมายแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'];
        if (in_array($newStatus, $validStatuses)) {
            $this->status = $newStatus;
        }
    }

    public function assignTechnician(int $technicianId): void {
        $this->technicianId = $technicianId;
        if ($this->status === 'รอดำเนินการ') {
            $this->status = 'นัดหมายแล้ว';
        }
    }

    public function canBeCancelled(): bool {
        return in_array($this->status, ['รอดำเนินการ', 'นัดหมายแล้ว']);
    }

    public function isCompleted(): bool {
        return $this->status === 'เสร็จสิ้น';
    }

    public function getStatusBadge(): string {
        return match ($this->status) {
            'รอดำเนินการ' => '<span class="badge badge-warning">🕒 รอดำเนินการ</span>',
            'นัดหมายแล้ว' => '<span class="badge badge-info">📅 นัดหมายแล้ว</span>',
            'กำลังดำเนินการ' => '<span class="badge badge-primary">🔧 กำลังดำเนินการ</span>',
            'เสร็จสิ้น' => '<span class="badge badge-success">✓ เสร็จสิ้น</span>',
            'ยกเลิก' => '<span class="badge badge-danger">✕ ยกเลิก</span>',
            default => '<span class="badge">' . htmlspecialchars($this->status) . '</span>'
        };
    }

    public function getFormattedDate(): string {
        if (!$this->serviceDate) return 'ยังไม่ได้กำหนดวัน';
        $timestamp = strtotime($this->serviceDate);
        if (!$timestamp) return $this->serviceDate;

        $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $day = date('j', $timestamp);
        $month = $thaiMonths[(int)date('n', $timestamp)];
        $year = (int)date('Y', $timestamp) + 543;
        $time = date('H:i', $timestamp);

        return "{$day} {$month} {$year} เวลา {$time} น.";
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'booking_code' => $this->getBookingCode(),
            'customer_id' => $this->customerId,
            'technician_id' => $this->technicianId,
            'service_date' => $this->serviceDate,
            'formatted_date' => $this->getFormattedDate(),
            'status' => $this->status,
            'total_price' => $this->totalPrice,
            'note' => $this->note,
            'ac_type' => $this->acType,
            'units' => $this->units,
            'subdistrict' => $this->subdistrict,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'map_url' => $this->getMapUrl()
        ];
    }
}