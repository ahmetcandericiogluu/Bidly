<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseValidator
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {}

    /**
     * Symfony validator'ü kullanarak temel validasyonları çalıştırır
     *
     * @throws \App\Exception\ValidationException
     */
    protected function validateConstraints(object $object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            throw new ValidationException($errorMessages);
        }
    }
}
