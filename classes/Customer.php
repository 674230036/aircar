<?php
require_once __DIR__ . '/User.php';

/**
 * OOA/OOD: Customer Entity
 * Extends User; represents a client booking air conditioning cleaning services,
 * with Ban Pong subdistrict address and Google Map geolocation.
 */
class Customer extends User {
    private string $address;
    private string $subdistrict;
    private float $latitude;
    private float $longitude;

    public function __construct(
        ?int $id,
        string $name,
        string $phone,
        ?string $email = null,
        string $address = '',
        string $subdistrict = 'ต.บ้านโป่ง',
        float $latitude = 13.8164,
        float $longitude = 99.8774
    ) {
        parent::__construct($id, $name, $phone, $email);
        $this->address = trim($address);
        $this->subdistrict = trim($subdistrict);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function setAddress(string $address): void {
        $this->address = trim($address);
    }

    public function getSubdistrict(): string {
        return $this->subdistrict ?: 'ต.บ้านโป่ง';
    }

    public function setSubdistrict(string $subdistrict): void {
        $this->subdistrict = trim($subdistrict);
    }

    public function getLatitude(): float {
        return $this->latitude;
    }

    public function getLongitude(): float {
        return $this->longitude;
    }

    public function setCoordinates(float $lat, float $lng): void {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function getGoogleMapsUrl(): string {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function getRole(): string {
        return 'Customer';
    }

    public function getShortAddress(int $limit = 40): string {
        if (mb_strlen($this->address, 'UTF-8') <= $limit) {
            return $this->address;
        }
        return mb_substr($this->address, 0, $limit, 'UTF-8') . '...';
    }

    public function toArray(): array {
        $data = parent::toArray();
        $data['address'] = $this->address;
        $data['subdistrict'] = $this->subdistrict;
        $data['latitude'] = $this->latitude;
        $data['longitude'] = $this->longitude;
        $data['google_maps_url'] = $this->getGoogleMapsUrl();
        return $data;
    }
}