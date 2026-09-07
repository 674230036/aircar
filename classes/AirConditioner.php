<?php
/**
 * OOA/OOD: AirConditioner Entity
 * Encapsulates the air conditioning appliance details and base servicing rates.
 */
class AirConditioner {
    private ?int $id;
    private int $customerId;
    private string $brand;
    private string $model;
    private string $type;
    private string $capacity;
    private string $location;
    private ?string $lastCleaning;

    public function __construct(
        ?int $id,
        int $customerId,
        string $brand = 'General',
        string $model = 'Standard',
        string $type = 'แอร์ติดผนัง',
        string $capacity = '12000 BTU',
        string $location = 'ห้องรับแขก',
        ?string $lastCleaning = null
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->brand = trim($brand);
        $this->model = trim($model);
        $this->type = trim($type);
        $this->capacity = trim($capacity);
        $this->location = trim($location);
        $this->lastCleaning = $lastCleaning;
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomerId(): int { return $this->customerId; }
    public function getBrand(): string { return $this->brand; }
    public function getModel(): string { return $this->model; }
    public function getType(): string { return $this->type; }
    public function getCapacity(): string { return $this->capacity; }
    public function getLocation(): string { return $this->location; }
    public function getLastCleaning(): ?string { return $this->lastCleaning; }

    public function getInfo(): string {
        return "{$this->brand} {$this->model} • {$this->capacity} ({$this->type})";
    }

    /**
     * Standard price calculation based on air conditioner type
     */
    public static function getBasePriceForType(string $type): float {
        return match ($type) {
            'แอร์แขวน/ตั้งพื้น' => 800.0,
            'แอร์สี่ทิศทาง (Cassette)' => 1200.0,
            'แอร์ท่อลม (Duct Type)' => 1500.0,
            default => 500.0 // แอร์ติดผนัง (Wall Type)
        };
    }
}