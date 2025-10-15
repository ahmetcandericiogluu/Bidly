<?php

namespace App\Controller\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\DTO\Auth\Customer\RegisterResponse;
use App\Service\Auth\Customer\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/customer')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RegisterService $registerService
    ) {}

    #[Route('/register', name: 'customer_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            /** @var RegisterRequest $registerRequest */
            $registerRequest = $this->serializer->deserialize(
                $request->getContent(),
                RegisterRequest::class,
                'json'
            );

            $registerResponse = $this->registerService->register($registerRequest);

            return new JsonResponse($registerResponse, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $errorResponse = new RegisterResponse(
                success: false,
                errors: [$e->getMessage()]
            );
            return new JsonResponse($errorResponse, Response::HTTP_BAD_REQUEST);
        }
    }
}
