<?php

namespace App\Validator\Auth\Customer;

use App\DTO\Auth\Customer\LoginRequest;
use App\Repository\CustomerRepository;
use App\Trait\TranslatorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginValidator
{
    use TranslatorTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CustomerRepository $customerRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TranslatorInterface $translator
    ) {}

    public function validate(object $data): void
    {
        if (!$data instanceof LoginRequest) {
            throw new \InvalidArgumentException($this->trans('auth.validation.invalid_request'));
        }

        // DTO üzerindeki annotation validasyonları
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception(implode(', ', $errorMessages));
        }

        $customer = $this->validateEmail($data->getEmail());
        $this->validatePassword($data->getPassword(), $customer);
        $this->validateAccountStatus($customer);
    }

    private function validateEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($this->trans('auth.validation.email.invalid'));
        }

        $customer = $this->customerRepository->findOneByEmail($email);
        if (!$customer) {
            throw new \Exception($this->trans('auth.errors.login.failed'));
        }

        return $customer;
    }

    private function validatePassword(string $password, $customer): void
    {
        if (strlen($password) < 8) {
            throw new \Exception($this->trans('auth.validation.password.min_length', ['limit' => 8]));
        }

        if (!$this->passwordHasher->isPasswordValid($customer, $password)) {
            throw new \Exception($this->trans('auth.errors.login.failed'));
        }
    }

    private function validateAccountStatus($customer): void
    {
        if (!$customer->isActive()) {
            throw new \Exception($this->trans('auth.errors.login.account_inactive'));
        }
    }
}
