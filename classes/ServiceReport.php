<?php
/**
 * OOA/OOD: ServiceReport Entity
 * Represents the work outcome, technical inspection results (PSI, Ampere),
 * and recommendations provided by the technician upon completing the job.
 */
class ServiceReport {
    private ?int $id;
    private int $orderId;
    private int $technicianId;
    private string $cleaningResult;
    private string $problemFound;
    private string $recommendation;
    private ?string $gasPsiBefore;
    private ?string $gasPsiAfter;
    private ?string $electricCurrent;
    private ?string $completedAt;

    public function __construct(
        ?int $id,
        int $orderId,
        int $technicianId,
        string $cleaningResult = '',
        string $problemFound = '',
        string $recommendation = '',
        ?string $gasPsiBefore = null,
        ?string $gasPsiAfter = null,
        ?string $electricCurrent = null,
        ?string $completedAt = null
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->technicianId = $technicianId;
        $this->cleaningResult = trim($cleaningResult);
        $this->problemFound = trim($problemFound);
        $this->recommendation = trim($recommendation);
        $this->gasPsiBefore = $gasPsiBefore ? trim($gasPsiBefore) : null;
        $this->gasPsiAfter = $gasPsiAfter ? trim($gasPsiAfter) : null;
        $this->electricCurrent = $electricCurrent ? trim($electricCurrent) : null;
        $this->completedAt = $completedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getOrderId(): int { return $this->orderId; }
    public function getTechnicianId(): int { return $this->technicianId; }
    public function getCleaningResult(): string { return $this->cleaningResult; }
    public function getProblemFound(): string { return $this->problemFound; }
    public function getRecommendation(): string { return $this->recommendation; }
    public function getGasPsiBefore(): ?string { return $this->gasPsiBefore; }
    public function getGasPsiAfter(): ?string { return $this->gasPsiAfter; }
    public function getElectricCurrent(): ?string { return $this->electricCurrent; }
    public function getCompletedAt(): ?string { return $this->completedAt; }

    public function summary(): string {
        $text = "ผลการล้าง: " . ($this->cleaningResult ?: 'ปกติ สะอาดสมบูรณ์');
        if ($this->gasPsiBefore && $this->gasPsiAfter) {
            $text .= " | แรงดันน้ำยา: {$this->gasPsiBefore} -> {$this->gasPsiAfter} PSI";
        }
        if ($this->electricCurrent) {
            $text .= " | กระแสไฟ: {$this->electricCurrent} A";
        }
        if ($this->problemFound) {
            $text .= " | ปัญหาที่พบ: {$this->problemFound}";
        }
        if ($this->recommendation) {
            $text .= " | คำแนะนำ: {$this->recommendation}";
        }
        return $text;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'technician_id' => $this->technicianId,
            'cleaning_result' => $this->cleaningResult,
            'problem_found' => $this->problemFound,
            'recommendation' => $this->recommendation,
            'gas_psi_before' => $this->gasPsiBefore,
            'gas_psi_after' => $this->gasPsiAfter,
            'electric_current' => $this->electricCurrent,
            'completed_at' => $this->completedAt
        ];
    }
}