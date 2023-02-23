<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;

abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;
    public $httpClient;
    use RefreshDatabaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

    }

    protected function getClientObject() 
    {
        $client = static::createClient(array(), array('base_uri' => 'http://demoweb.local/api/'));
        return $client;
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();
        return static::createClient([], ['base_uri' => 'http://demoweb.local/','headers' => ['authorization' => 'Bearer '.$token]]);
    }

    protected function createUser(string $username, string $password):array
    {
        $client = $this->getClientObject();
        $data = [
            'email' => $username,
            'password' => $password,
        ];

        $client->request('POST', '/api/register', ['json' => $data ]);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        return $content;

    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken(): string
    {
        $client = static::createClient(array(), array('base_uri' => 'http://demoweb.local/'));
       
        $response = $client->request('POST', '/authentication_token', ['json' => [
            'email' => 'admin@demo.com',
            'password' => 'admintest',
        ]]);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(),true);
        $this->assertArrayHasKey('token', $content);

        return $content['token'];
  
    }
}