<?php

namespace App\Service\Auth\Vendor;

use App\DTO\Auth\Vendor\RegisterRequest;
use App\Entity\Vendor;
use App\Exception\Auth\EmailAlreadyExistsException;
use App\Repository\VendorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VendorRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VendorRepository $vendorRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function register(RegisterRequest $request): Vendor
    {
        // Email kontrolü
        if ($this->vendorRepository->findOneByEmail($request->getEmail())) {
            throw new EmailAlreadyExistsException('Bu email adresi zaten kullanılıyor');
        }

        // Yeni vendor oluştur
        $vendor = new Vendor();
        $vendor->setName($request->getName());
        $vendor->setEmail($request->getEmail());
        
        // Şifreyi hash'le
        $hashedPassword = $this->passwordHasher->hashPassword($vendor, $request->getPassword());
        $vendor->setPassword($hashedPassword);
        
        $vendor->setCreatedAt(new \DateTimeImmutable());
        $vendor->setUpdatedAt(new \DateTimeImmutable());

        // Veritabanına kaydet
        $this->entityManager->persist($vendor);
        $this->entityManager->flush();

        return $vendor;
    }
}
