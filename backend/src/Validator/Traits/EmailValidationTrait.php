<?php

namespace App\Validator\Traits;

use App\Exception\Auth\EmailAlreadyExistsException;
use App\Repository\CustomerRepository;

trait EmailValidationTrait
{
    protected function validateEmailUniqueness(string $email, CustomerRepository $repository): void
    {
        if ($repository->findOneByEmail($email)) {
            throw new EmailAlreadyExistsException('Bu email adresi zaten kullanılıyor.');
        }
    }

    protected function validateEmailDomain(string $email, array $allowedDomains = []): void
    {
        if (empty($allowedDomains)) {
            return;
        }

        $domain = substr(strrchr($email, "@"), 1);
        if (!in_array($domain, $allowedDomains)) {
            throw new InvalidEmailException('Bu email domain\'i kabul edilmemektedir.');
        }
    }

    protected function validateEmailFormat(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException('Geçersiz email formatı.');
        }
    }
}
