<?php
/**
 * OOA/OOD: Payment Entity
 * Handles financial transactions, receipts, and settlement verification for service orders.
 */
class Payment {
    private ?int $id;
    private int $orderId;
    private float $amount;
    private string $paymentMethod;
    private string $paymentStatus;
    private ?string $paymentDate;

    public function __construct(
        ?int $id,
        int $orderId,
        float $amount,
        string $paymentMethod = 'พร้อมเพย์ QR Code',
        string $paymentStatus = 'รอตรวจสอบ',
        ?string $paymentDate = null
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->paymentMethod = trim($paymentMethod);
        $this->paymentStatus = trim($paymentStatus);
        $this->paymentDate = $paymentDate;
    }

    public function getId(): ?int { return $this->id; }
    public function getOrderId(): int { return $this->orderId; }
    public function getAmount(): float { return $this->amount; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getPaymentStatus(): string { return $this->paymentStatus; }
    public function getPaymentDate(): ?string { return $this->paymentDate; }

    public function isPaid(): bool {
        return $this->paymentStatus === 'ชำระแล้ว';
    }

    public function markAsPaid(?string $date = null): void {
        $this->paymentStatus = 'ชำระแล้ว';
        $this->paymentDate = $date ?: date('Y-m-d H:i:s');
    }

    public function getStatusBadge(): string {
        return match ($this->paymentStatus) {
            'ชำระแล้ว' => '<span class="badge badge-success">✓ ชำระแล้ว</span>',
            'รอตรวจสอบ' => '<span class="badge badge-warning">🕒 รอตรวจสอบ</span>',
            'ยกเลิก' => '<span class="badge badge-danger">✕ ยกเลิก</span>',
            default => '<span class="badge">' . htmlspecialchars($this->paymentStatus) . '</span>'
        };
    }

    public function getFormattedAmount(): string {
        return '฿' . number_format($this->amount, 2);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'formatted_amount' => $this->getFormattedAmount(),
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'payment_date' => $this->paymentDate,
            'is_paid' => $this->isPaid()
        ];
    }
}