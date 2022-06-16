<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private EntityManager $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //In case leftover entries exist
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate tables for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testCreateUser(): void
    {
        $email = 'test@test.com';
        $user = new User();
        $user->setFirstName('Test')
            ->setLastName('TEST')
            ->setEmail($email)
            ->setPassword('password')
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertEquals(1, count($this->userRepository->findBy(['email' => $email])));
    }

    


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
    }
}
