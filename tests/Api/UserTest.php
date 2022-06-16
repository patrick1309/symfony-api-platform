<?php

namespace App\Tests\Api;

use App\Tests\Api\AbstractTest;

class UserTest extends AbstractTest
{
    public function testGetUsers(): void
    {
        $response = static::createClientWithCredentials()->request('GET', '/api/users');
        
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api/users']);
    }

    public function testCreateUser()
    {
        $response = static::createClientWithCredentials()->request('POST', '/api/users', [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'body' => json_encode([
                'firstName' => 'Test',
                'lastName' => 'Test',
                'email' => uniqid().'@test.com',
                'plainTextPassword' => 'password'
            ])
        ]);

        $this->assertResponseIsSuccessful();
    }
}
