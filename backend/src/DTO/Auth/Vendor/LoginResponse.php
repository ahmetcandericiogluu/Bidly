<?php

namespace App\DTO\Auth\Vendor;

class LoginResponse
{
    public function __construct(
        private readonly bool $success,
        private readonly ?array $vendor = null,
        private readonly ?string $token = null,
        private readonly ?string $error = null
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getVendor(): ?array
    {
        return $this->vendor;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
