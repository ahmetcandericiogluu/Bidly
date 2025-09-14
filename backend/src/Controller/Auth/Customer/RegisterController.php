<?php

namespace App\Controller\Auth\Customer;

use App\DTO\Auth\Customer\RegisterRequest;
use App\DTO\Auth\Customer\RegisterResponse;
use App\Exception\Auth\EmailAlreadyExistsException;
use App\Exception\Auth\InvalidPasswordException;
use App\Exception\ValidationException;
use App\Service\Auth\Customer\CustomerRegistrationService;
use App\Validator\Auth\Customer\RegisterValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/customer')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RegisterValidator $registerValidator,
        private readonly CustomerRegistrationService $registrationService
    ) {}

    #[Route('/register', name: 'customer_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        //dd($request);
        try {
            // 1. Request'i DTO'ya dönüştür
            /** @var RegisterRequest $registerRequest */
            $registerRequest = $this->serializer->deserialize(
                $request->getContent(),
                RegisterRequest::class,
                'json'
            );
            //dd($registerRequest);

            // 2. Validasyonları çalıştır
            $this->registerValidator->validate($registerRequest);

            //dd($registerRequest);

            // 3. Kayıt işlemini gerçekleştir
            $customer = $this->registrationService->register($registerRequest);


            // 4. Başarılı response dön
            $response = new RegisterResponse(
                success: true,
                customerId: $customer->getId()
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
        } catch (InvalidPasswordException $e) {
            $response = new RegisterResponse(
                success: false,
                error: $e->getMessage()
            );

            return new JsonResponse(
                $this->serializer->serialize($response, 'json'),
                400,
                [],
                true
            );
        } catch (\Exception $e) {
            $response = new RegisterResponse(
                success: false,
                error: 'Beklenmeyen bir hata olustu.'
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
