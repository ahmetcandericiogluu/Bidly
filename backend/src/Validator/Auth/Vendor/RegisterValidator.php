<?php

namespace App\Validator\Auth\Vendor;

use App\DTO\Auth\Vendor\RegisterRequest;
use App\Exception\ValidationException;
use App\Repository\VendorRepository;
use App\Validator\Traits\EmailValidationTrait;
use App\Validator\Traits\PasswordValidationTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterValidator
{
    use EmailValidationTrait;
    use PasswordValidationTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly VendorRepository $vendorRepository
    ) {}

    public function validate(RegisterRequest $request): void
    {
        $errors = [];

        // Temel validasyonlar
        $violations = $this->validator->validate($request);
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        // Email format kontrolü
        if (!$this->isValidEmail($request->getEmail())) {
            $errors[] = 'Geçerli bir email adresi giriniz';
        }

        // Email benzersizlik kontrolü
        if ($this->vendorRepository->findOneByEmail($request->getEmail())) {
            $errors[] = 'Bu email adresi zaten kullanılıyor';
        }

        // Şifre güçlülük kontrolü
        if (!$this->isStrongPassword($request->getPassword())) {
            $errors[] = 'Şifre en az 8 karakter olmalı, büyük harf, küçük harf, rakam ve özel karakter içermelidir';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
