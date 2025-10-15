<?php

namespace App\DTO\Auth\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email alanı zorunludur')]
        #[Assert\Email(message: 'Geçerli bir email adresi giriniz')]
        private readonly string $email,

        #[Assert\NotBlank(message: 'Şifre alanı zorunludur')]
        private readonly string $password
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
