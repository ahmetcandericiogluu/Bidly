<?php

namespace App\Service\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerRegistrationService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function register(RegisterRequest $request): Customer
    {
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

        return $customer;
    }
}
