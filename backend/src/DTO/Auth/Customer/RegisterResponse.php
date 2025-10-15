<?php

namespace App\DTO\Auth\Customer;

class RegisterResponse implements \JsonSerializable
{
    public function __construct(
        private readonly bool $success,
        private readonly ?array $errors = null,
        private readonly ?string $customerId = null
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
            'customerId' => $this->customerId
        ];
    }
}
