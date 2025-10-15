<?php

namespace App\Service\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\DTO\Auth\Customer\RegisterResponse;
use App\Entity\Customer;
use App\Validator\Auth\Customer\RegisterValidator;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RegisterValidator $registerValidator,
    ) {}

    public function register(RegisterRequest $request): RegisterResponse
    {
        $this->registerValidator->validate($request);

        $customer = new Customer();
        $customer->setEmail($request->getEmail());
        $customer->setName($request->getName());

        $hashedPassword = $this->passwordHasher->hashPassword(
            $customer,
            $request->getPassword()
        );

        $customer->setPassword($hashedPassword);
        $customer->setCreatedAt(new \DateTimeImmutable());
        $customer->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($customer);
        $this->em->flush();

        return new RegisterResponse(
            success: true,
            customerId: $customer->getId()
        );
    }
}
