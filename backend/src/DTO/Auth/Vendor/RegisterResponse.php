<?php

namespace App\DTO\Auth\Vendor;

class RegisterResponse
{
    public function __construct(
        private readonly bool $success,
        private readonly ?int $vendorId = null,
        private readonly ?string $error = null,
        private readonly ?array $errors = null
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getVendorId(): ?int
    {
        return $this->vendorId;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
