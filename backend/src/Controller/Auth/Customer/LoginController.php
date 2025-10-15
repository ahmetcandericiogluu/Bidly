<?php

namespace App\Controller\Auth\Customer;

use App\DTO\Auth\Customer\LoginRequest;
use App\DTO\Auth\Customer\LoginResponse;
use App\Service\Auth\Customer\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/customer')]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly LoginService $loginService
    ) {}

    #[Route('/login', name: 'customer_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            /** @var LoginRequest $loginRequest */
            $loginRequest = $this->serializer->deserialize(
                $request->getContent(),
                LoginRequest::class,
                'json'
            );

            /** @var LoginResponse $loginResponse */
            $loginResponse = $this->loginService->login($loginRequest);

            return new JsonResponse($loginResponse, Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorResponse = new LoginResponse(
                success: false,
                errors: [$e->getMessage()]
            );

            return new JsonResponse($errorResponse, Response::HTTP_UNAUTHORIZED);
        }
    }
}
