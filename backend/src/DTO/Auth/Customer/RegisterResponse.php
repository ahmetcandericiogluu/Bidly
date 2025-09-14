<?php

namespace App\DTO\Auth\Customer;

class RegisterResponse
{
    public function __construct(
        private readonly bool $success,
        private readonly ?array $errors = null,
        private readonly ?string $error = null,
        private readonly ?string $customerId = null
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }
}
