<?php

namespace App\Tests\User;

use App\Tests\AbstractTest;

class UserTest extends AbstractTest
{
    public function testCreateUserWithInvalidParameters()
    {
        $client = $this->getClientObject();

        $data = [
            'email' => 'admin@demo.com',
        ];

        $client->request('POST', '/api/register', ['json' => $data]);

        $response = $client->getResponse();
        $content = json_decode($response->getBrowserKitResponse()->getContent());

        // Assertions about the created user with Invalid Parameters
        $this->assertEquals(422, $response->getStatusCode());
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
        // Getting token
        $token = $this->getToken();
    }
}
