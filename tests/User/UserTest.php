<?php

namespace App\Tests\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\RaceMaster;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\AbstractTest;

class UserTest extends AbstractTest
{
    public function testCreateUserWithInvalidParameters()
    {
        $client = $this->getClientObject();

        $data = [
            'email' => 'admin@demo.com'
        ];

        $client->request('POST', '/api/register', ['json' => $data ]);

        $response = $client->getResponse();
        
        $this->assertEquals(422, $response->getStatusCode());
        
        $content = json_decode($response->getBrowserKitResponse()->getContent());
       
        $this->assertEquals('password', $content->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $content->violations[0]->message);

    }

    public function testCreateUser()
    {
        $userData = $this->createUser('admin@demo.com', 'admintest');

        // Assertions about the created user
        $this->assertArrayHasKey('id', $userData);
        $this->assertEquals('admin@demo.com', $userData['email']);
    }

    public function testLogin(): void
    {
        $userData = $this->createUser('admin@demo.com', 'admintest');

        $token = $this->getToken();
    }

}