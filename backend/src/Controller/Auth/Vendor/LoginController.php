<?php

namespace App\Controller\Auth\Vendor;

use App\DTO\Auth\Vendor\LoginRequest;
use App\DTO\Auth\Vendor\LoginResponse;
use App\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/vendor')]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly VendorRepository $vendorRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/login', name: 'vendor_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            // 1. Request'i DTO'ya dönüştür
            /** @var LoginRequest $loginRequest */
            $loginRequest = $this->serializer->deserialize(
                $request->getContent(),
                LoginRequest::class,
                'json'
            );

            // 2. Temel validasyonları çalıştır
            $errors = $this->validator->validate($loginRequest);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                $response = new LoginResponse(
                    success: false,
                    error: implode(', ', $errorMessages)
                );

                return new JsonResponse(
                    $this->serializer->serialize($response, 'json'),
                    400,
                    [],
                    true
                );
            }

            // 3. Vendor'ı bul
            $vendor = $this->vendorRepository->findOneByEmail($loginRequest->getEmail());
            
            if (!$vendor) {
                $response = new LoginResponse(
                    success: false,
                    error: 'Email veya şifre hatalı'
                );

                return new JsonResponse(
                    $this->serializer->serialize($response, 'json'),
                    401,
                    [],
                    true
                );
            }

            // 4. Şifreyi kontrol et
            if (!$this->passwordHasher->isPasswordValid($vendor, $loginRequest->getPassword())) {
                $response = new LoginResponse(
                    success: false,
                    error: 'Email veya şifre hatalı'
                );

                return new JsonResponse(
                    $this->serializer->serialize($response, 'json'),
                    401,
                    [],
                    true
                );
            }

            // 5. Başarılı giriş - vendor bilgilerini döndür
            $vendorData = [
                'id' => $vendor->getId(),
                'name' => $vendor->getName(),
                'email' => $vendor->getEmail(),
                'created_at' => $vendor->getCreatedAt()->format('Y-m-d H:i:s')
            ];

            $response = new LoginResponse(
                success: true,
                vendor: $vendorData,
                token: null // JWT token'ı daha sonra ekleyeceğiz
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                200,
                [],
                true
            );

        } catch (\Exception $e) {
            $response = new LoginResponse(
                success: false,
                error: 'Giriş işlemi başarısız oldu'
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                500,
                [],
                true
            );
        }
    }
}