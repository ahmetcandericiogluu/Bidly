<?php

namespace App\DTO\Auth\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'İsim alanı zorunludur')]
        #[Assert\Length(
            min: 2,
            max: 50,
            minMessage: 'İsim en az {{ limit }} karakter olmalıdır',
            maxMessage: 'İsim en fazla {{ limit }} karakter olabilir'
        )]
        private readonly string $name,

        #[Assert\NotBlank(message: 'Email alanı zorunludur')]
        #[Assert\Email(message: 'Geçerli bir email adresi giriniz')]
        private readonly string $email,

        #[Assert\NotBlank(message: 'Şifre alanı zorunludur')]
        #[Assert\Length(
            min: 8,
            minMessage: 'Şifre en az {{ limit }} karakter olmalıdır'
        )]
        private readonly string $password
    ) {}

    // Getter metodları
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

    // Request array'inden DTO oluşturma
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );
    }
}
