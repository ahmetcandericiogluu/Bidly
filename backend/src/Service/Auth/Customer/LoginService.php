<?php

namespace App\Service\Auth\Customer;

use App\DTO\Auth\Customer\LoginRequest;
use App\DTO\Auth\Customer\LoginResponse;
use App\Repository\CustomerRepository;
use App\Validator\Auth\Customer\LoginValidator;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LoginService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly ParameterBagInterface $params,
        private readonly LoginValidator $loginValidator,
        private readonly EntityManagerInterface $em
    ) {}

    public function login(LoginRequest $request): LoginResponse
    {
        $this->loginValidator->validate($request);

        $customer = $this->customerRepository->findOneByEmail($request->getEmail());
        $customer->setLastLoginAt(new \DateTimeImmutable());

        $this->em->persist($customer);
        $this->em->flush();

        $token = $this->jwtManager->create($customer);

        $userData = [
            'id' => $customer->getId(),
            'email' => $customer->getEmail(),
            'name' => $customer->getName()
        ];

        return new LoginResponse(
            success: true,
            token: $token,
            expiresIn: $this->params->get('lexik_jwt_authentication.token_ttl'),
            user: $userData
        );
    }
}
