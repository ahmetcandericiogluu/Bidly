<?php

namespace App\DTO\Auth\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'auth.validation.name.required')]
        #[Assert\Length(
            min: 2,
            max: 50,
            minMessage: 'auth.validation.name.min_length',
            maxMessage: 'auth.validation.name.max_length'
        )]
        private readonly string $name,

        #[Assert\NotBlank(message: 'auth.validation.email.required')]
        #[Assert\Email(message: 'auth.validation.email.invalid')]
        private readonly string $email,

        #[Assert\NotBlank(message: 'auth.validation.password.required')]
        #[Assert\Length(
            min: 8,
            minMessage: 'auth.validation.password.min_length'
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
