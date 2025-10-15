<?php

namespace App\Validator\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\Repository\CustomerRepository;
use App\Trait\TranslatorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterValidator
{
    use TranslatorTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CustomerRepository $customerRepository,
        private readonly TranslatorInterface $translator
    ) {}

    public function validate(object $data): void
    {
        if (!$data instanceof RegisterRequest) {
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

        $this->validateEmail($data->getEmail());

        $this->validatePassword($data->getPassword());
    }

    private function validateEmail(string $email): void
    {
        if ($this->customerRepository->findOneByEmail($email)) {
            throw new \Exception($this->trans('auth.validation.email.exists'));
        }
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \Exception($this->trans('auth.validation.password.min_length', ['limit' => 8]));
        }

        if (strlen($password) > 50) {
            throw new \Exception($this->trans('auth.validation.password.max_length', ['limit' => 50]));
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new \Exception($this->trans('auth.validation.password.uppercase'));
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new \Exception($this->trans('auth.validation.password.lowercase'));
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \Exception($this->trans('auth.validation.password.number'));
        }

        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            throw new \Exception($this->trans('auth.validation.password.special_char'));
        }
    }
}
