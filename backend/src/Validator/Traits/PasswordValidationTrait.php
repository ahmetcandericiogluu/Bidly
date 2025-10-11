<?php

namespace App\Validator\Traits;

use App\Exception\Auth\InvalidPasswordException;

trait PasswordValidationTrait
{
    protected function isStrongPassword(string $password): bool
    {
        // En az 8 karakter, büyük harf, küçük harf, rakam ve özel karakter kontrolü
        return strlen($password) >= 8 
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password) 
            && preg_match('/[0-9]/', $password)
            && preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password);
    }

    protected function validatePasswordComplexity(string $password): void
    {
        $rules = [
            'uppercase' => [
                'pattern' => '/[A-Z]/',
                'message' => 'Şifre en az bir büyük harf içermelidir.'
            ],
            'lowercase' => [
                'pattern' => '/[a-z]/',
                'message' => 'Şifre en az bir küçük harf içermelidir.'
            ],
            'number' => [
                'pattern' => '/[0-9]/',
                'message' => 'Şifre en az bir rakam içermelidir.'
            ],
            'special' => [
                'pattern' => '/[!@#$%^&*()\-_=+{};:,<.>]/',
                'message' => 'Şifre en az bir özel karakter içermelidir.'
            ]
        ];

        foreach ($rules as $rule) {
            if (!preg_match($rule['pattern'], $password)) {
                throw new InvalidPasswordException($rule['message']);
            }
        }
    }

    protected function validatePasswordLength(string $password, int $minLength = 8, int $maxLength = 50): void
    {
        $length = strlen($password);

        if ($length < $minLength) {
            throw new InvalidPasswordException("Şifre en az {$minLength} karakter olmalıdır.");
        }

        if ($length > $maxLength) {
            throw new InvalidPasswordException("Şifre en fazla {$maxLength} karakter olabilir.");
        }
    }
}
