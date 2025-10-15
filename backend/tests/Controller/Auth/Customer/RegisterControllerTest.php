<?php

namespace App\Tests\Controller\Auth\Customer;

use App\Controller\Auth\Customer\RegisterController;
use App\DTO\Auth\Customer\RegisterRequest;
use App\Entity\Customer;
use App\Service\Auth\Customer\CustomerRegistrationService;
use App\Validator\Auth\Customer\RegisterValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterControllerTest extends TestCase
{
    private RegisterController $controller;
    private SerializerInterface $serializer;
    private RegisterValidator $validator;
    private CustomerRegistrationService $registrationService;

    protected function setUp(): void
    {
        // Mock bağımlılıkları oluşturur (Mock DB'ye bağlanmadan test verisi kullanmamıza yarıyor)
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(RegisterValidator::class);
        $this->registrationService = $this->createMock(CustomerRegistrationService::class);

        // Test edilecek controller'ı oluştur
        $this->controller = new RegisterController(
            $this->serializer,
            $this->validator,
            $this->registrationService
        );
    }

    public function testRegister_WhenAllInputsAreValid_ReturnsSuccessResponse(): void
    {
        // Test verileri
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);

        // Request nesnesini oluştur
        $request = new Request([], [], [], [], [], [], $jsonContent);

        // RegisterRequest nesnesini oluştur
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        // Mock Customer nesnesi oluştur
        $customer = $this->createMock(Customer::class);
        $customer->method('getId')->willReturn(123);

        // Mock davranışlarını ayarla
        // JSON'ı DTO'ya çevirme
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($jsonContent, RegisterRequest::class, 'json')
            ->willReturn($registerRequest);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($registerRequest);

        $this->registrationService
            ->expects($this->once())
            ->method('register')
            ->with($registerRequest)
            ->willReturn($customer);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => true,
                'customerId' => 123
            ]));

        // Test
        $response = $this->controller->register($request);

        // Sonuçlar
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(123, $responseData['customerId']);
    }

    public function testRegister_WhenValidationFails_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'name' => 'Test User',
            'email' => 'invalid-email', // Geçersiz email
            'password' => '123'         // Çok kısa şifre
        ];
        $jsonContent = json_encode($requestData);

        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new \Exception('Geçersiz email formatı'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Geçersiz email formatı']
            ]));

        $response = $this->controller->register($request);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertNotEmpty($responseData['errors']);
        $this->assertContains('Geçersiz email formatı', $responseData['errors']);
    }

    public function testRegister_WhenEmailAlreadyExists_ReturnsConflictResponse(): void
    {
        // Test verileri - Var olan email
        $requestData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);

        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new \Exception('Bu email adresi zaten kullanılıyor'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Bu email adresi zaten kullanılıyor']
            ]));

        $response = $this->controller->register($request);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertNotEmpty($responseData['errors']);
        $this->assertContains('Bu email adresi zaten kullanılıyor', $responseData['errors']);
    }

    public function testRegister_WhenPasswordIsTooShort_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123'  // Çok kısa şifre
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate')
            ->willThrowException(new \Exception('Şifre en az 8 karakter olmalıdır'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Şifre en az 8 karakter olmalıdır']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertContains('Şifre en az 8 karakter olmalıdır', $responseData['errors']);
    }

    public function testRegister_WhenJsonIsInvalid_ReturnsBadRequestResponse(): void
    {
        $invalidJson = '{invalid-json}';
        $request = new Request([], [], [], [], [], [], $invalidJson);

        $this->serializer
            ->method('deserialize')
            ->willThrowException(new \Exception('Invalid JSON format'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Geçersiz istek formatı']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    public function testRegister_WhenNameIsMissing_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'email' => 'test@example.com',
            'password' => 'Test123!'
            // name eksik
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);

        $this->serializer
            ->method('deserialize')
            ->willThrowException(new \Exception('İsim alanı zorunludur'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['İsim alanı zorunludur']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertContains('İsim alanı zorunludur', $responseData['errors']);
    }

    public function testRegister_WhenServiceFails_ReturnsErrorResponse(): void
    {
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate');

        $this->registrationService
            ->method('register')
            ->willThrowException(new \Exception('Kayıt işlemi başarısız'));

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Kayıt işlemi başarısız']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertContains('Kayıt işlemi başarısız', $responseData['errors']);
    }

    public function testRegister_WhenPasswordHasNoUpperCase_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'test123!'  // Büyük harf yok
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate')
            ->willThrowException(new \Exception('Şifre en az bir büyük harf içermelidir'));

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Şifre en az bir büyük harf içermelidir']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Şifre en az bir büyük harf içermelidir', json_decode($response->getContent(), true)['errors']);
    }

    public function testRegister_WhenEmailHasNoDomain_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'name' => 'Test User',
            'email' => 'testexample',  // @ işareti yok
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate')
            ->willThrowException(new \Exception('Geçerli bir email adresi giriniz'));

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Geçerli bir email adresi giriniz']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Geçerli bir email adresi giriniz', json_decode($response->getContent(), true)['errors']);
    }

    public function testRegister_WhenNameIsTooShort_ReturnsBadRequestResponse(): void
    {
        $requestData = [
            'name' => 'A',  // Çok kısa isim
            'email' => 'test@example.com',
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate')
            ->willThrowException(new \Exception('İsim en az 2 karakter olmalıdır'));

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['İsim en az 2 karakter olmalıdır']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('İsim en az 2 karakter olmalıdır', json_decode($response->getContent(), true)['errors']);
    }

    public function testRegister_WhenSerializerGetsError_ReturnsErrorResponse(): void
    {
        $request = new Request([], [], [], [], [], [], 'invalid data');

        $this->serializer
            ->method('deserialize')
            ->willThrowException(new \Exception('Veri dönüştürme hatası'));

        $this->validator
            ->expects($this->never())
            ->method('validate');

        $this->registrationService
            ->expects($this->never())
            ->method('register');

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => false,
                'errors' => ['Veri dönüştürme hatası']
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Veri dönüştürme hatası', json_decode($response->getContent(), true)['errors']);
    }

    public function testRegister_WhenNameHasSpecialCharacters_ReturnsSuccessResponse(): void
    {
        $requestData = [
            'name' => 'Test Üser Ñáme',  // Özel karakterli isim
            'email' => 'test@example.com',
            'password' => 'Test123!'
        ];
        $jsonContent = json_encode($requestData);
        $request = new Request([], [], [], [], [], [], $jsonContent);
        $registerRequest = new RegisterRequest(
            name: $requestData['name'],
            email: $requestData['email'],
            password: $requestData['password']
        );

        $customer = $this->createMock(Customer::class);
        $customer->method('getId')->willReturn(123);

        $this->serializer
            ->method('deserialize')
            ->willReturn($registerRequest);

        $this->validator
            ->method('validate');

        $this->registrationService
            ->method('register')
            ->willReturn($customer);

        $this->serializer
            ->method('serialize')
            ->willReturn(json_encode([
                'success' => true,
                'customerId' => 123
            ]));

        $response = $this->controller->register($request);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(json_decode($response->getContent(), true)['success']);
    }
}
