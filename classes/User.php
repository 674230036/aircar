<?php
/**
 * OOA/OOD: Base User Class (Abstract Concept)
 * Represents a person in the AirCare domain model with basic identity and contact details.
 */
abstract class User {
    protected ?int $id;
    protected string $name;
    protected string $phone;
    protected ?string $email;

    public function __construct(?int $id, string $name, string $phone, ?string $email = null) {
        $this->id = $id;
        $this->name = trim($name);
        $this->phone = trim($phone);
        $this->email = $email ? trim($email) : null;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = trim($name);
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function setPhone(string $phone): void {
        $this->phone = trim($phone);
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email ? trim($email) : null;
    }

    /**
     * Polymorphic method to get the user's role in the system
     */
    abstract public function getRole(): string;

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => $this->getRole()
        ];
    }
}