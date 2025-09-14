<?php

namespace App\Validator\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\Repository\CustomerRepository;
use App\Validator\Traits\EmailValidationTrait;
use App\Validator\Traits\PasswordValidationTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterValidator
{
    use EmailValidationTrait;
    use PasswordValidationTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CustomerRepository $customerRepository
    ) {}

    public function validate(object $data): void
    {
        if (!$data instanceof RegisterRequest) {
            throw new \InvalidArgumentException('Data must be instance of RegisterRequest');
        }

        // 1. Symfony constraint validasyonları (DTO üzerindeki annotationlar)
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new ValidationException($errorMessages);
        }

        // 2. Email validasyonları
        $this->validateEmailFormat($data->getEmail());
        $this->validateEmailUniqueness($data->getEmail(), $this->customerRepository);

        // 3. Şifre validasyonları
        $this->validatePasswordLength($data->getPassword());
        $this->validatePasswordComplexity($data->getPassword());
    }
}
