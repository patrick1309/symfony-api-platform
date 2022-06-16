<?php

namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

abstract class AbstractTest extends ApiTestCase
{
    private $token;
    private $clientWithCredentials;
    private $entityManager;

    public function setUp(): void
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
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }

    private function createUser($email, $password = 'password') 
    {
        $user = new User();
        $user->setFirstName('Test')
            ->setLastName('TEST')
            ->setEmail($email)
            ->setPlainTextPassword($password)
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $email = 'test@test.com';
        $password = 'password';
        $this->createUser($email, $password);
        

        $response = static::createClient()->request(
            'POST', 
            'authentication_token',
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ], 
                'body' => $body ?: json_encode([
                    'email' => $email,
                    'password' => $password,
                ])
            ]);
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;

        return $data->token;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
    }
}