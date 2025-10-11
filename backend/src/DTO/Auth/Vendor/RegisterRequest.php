<?php

namespace App\DTO\Auth\Vendor;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Şirket adı zorunludur')]
        #[Assert\Length(min: 2, max: 100, minMessage: 'Şirket adı en az 2 karakter olmalıdır', maxMessage: 'Şirket adı en fazla 100 karakter olmalıdır')]
        private readonly string $name,

        #[Assert\NotBlank(message: 'Email alanı zorunludur')]
        #[Assert\Email(message: 'Geçerli bir email adresi giriniz')]
        private readonly string $email,

        #[Assert\NotBlank(message: 'Şifre alanı zorunludur')]
        #[Assert\Length(min: 8, minMessage: 'Şifre en az 8 karakter olmalıdır')]
        private readonly string $password
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );
    }
}
