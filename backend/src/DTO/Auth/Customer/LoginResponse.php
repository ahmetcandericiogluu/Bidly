<?php

namespace App\DTO\Auth\Customer;

class LoginResponse implements \JsonSerializable
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $token = null,
        private readonly ?int $expiresIn = null,
        private readonly ?array $user = null,
        private readonly ?array $errors = null
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'token' => $this->token,
            'expiresIn' => $this->expiresIn,
            'user' => $this->user,
            'errors' => $this->errors
        ];
    }
}
