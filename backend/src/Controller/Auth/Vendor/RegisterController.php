<?php

namespace App\Controller\Auth\Vendor;

use App\DTO\Auth\Vendor\RegisterRequest;
use App\DTO\Auth\Vendor\RegisterResponse;
use App\Exception\Auth\EmailAlreadyExistsException;
use App\Exception\ValidationException;
use App\Service\Auth\Vendor\VendorRegistrationService;
use App\Validator\Auth\Vendor\RegisterValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/vendor')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RegisterValidator $validator,
        private readonly VendorRegistrationService $registrationService
    ) {}

    #[Route('/register', name: 'vendor_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            // 1. Request'i DTO'ya dönüştür
            /** @var RegisterRequest $registerRequest */
            $registerRequest = $this->serializer->deserialize(
                $request->getContent(),
                RegisterRequest::class,
                'json'
            );

            // 2. Validasyonları çalıştır
            $this->validator->validate($registerRequest);

            // 3. Vendor'ı kaydet
            $vendor = $this->registrationService->register($registerRequest);

            // 4. Başarılı response
            $response = new RegisterResponse(
                success: true,
                vendorId: $vendor->getId()
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                201,
                [],
                true
            );

        } catch (ValidationException $e) {
            $response = new RegisterResponse(
                success: false,
                error: 'Validasyon hatası',
                errors: $e->getErrors()
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                400,
                [],
                true
            );

        } catch (EmailAlreadyExistsException $e) {
            $response = new RegisterResponse(
                success: false,
                error: $e->getMessage()
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                409,
                [],
                true
            );

        } catch (\Exception $e) {
            $response = new RegisterResponse(
                success: false,
                error: 'Beklenmeyen bir hata oluştu'
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