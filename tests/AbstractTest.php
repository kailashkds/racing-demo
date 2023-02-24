<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\DataFixtures\AppTestFixtures;

abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;
    public $httpClient;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->loadUser();
    }

    public static function loadUser()
    {
        $fixture = new AppTestFixtures();
        $client = static::createClient();
        $fixture->load($client->getContainer()->get('doctrine.orm.entity_manager'));
    }

    protected function getClientObject()
    {
        $client = static::createClient([], ['base_uri' => 'http://demoweb.local/api/']);

        return $client;
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['base_uri' => 'http://demoweb.local/', 'headers' => ['authorization' => 'Bearer '.$token]]);
    }

    protected function createUser(string $username, string $password): array
    {
        $client = $this->getClientObject();
        $data = [
            'email' => $username,
            'password' => $password,
        ];

        $client->request('POST', '/api/register', ['json' => $data]);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        return $content;
    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken(): string
    {
        // $client = static::createClient(array(), array('base_uri' => 'http://demoweb.local/'));
        $client = $this->getClientObject();
        $response = $client->request('POST', '/authentication_token', ['json' => [
            'email' => 'test@test.com',
            'password' => 'test',
        ]]);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $content);

        return $content['token'];
    }

    protected function tearDown(): void
    {
        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Truncate the desired table
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('user', true /* whether to cascade */));
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeUpdate($platform->getTruncateTableSQL('race_master', true /* whether to cascade */));
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $connection->executeUpdate($platform->getTruncateTableSQL('race_details', true /* whether to cascade */));

        // Call the parent tearDown() method
        parent::tearDown();
    }
}
