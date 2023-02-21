<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\RaceMaster;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class RaceMasterTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    // use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/race_masters');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/RaceMaster',
            '@id' => '/api/race_masters',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(30, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(RaceMaster::class);
    }

    public function testCreateRacemaster(): void
    {
        $response = static::createClient()->request('POST', '/api/race_masters', ['json' => [
            'raceTitle' => 'Test Marathon',
            'raceDate' => '2023-02-18',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/RaceMaster',
            '@type' => 'RaceMaster',
            'raceTitle' => 'Test Marathon',
            'raceDate' => '2023-02-18',
        ]);
        $this->assertMatchesRegularExpression('~^/api/race_masters/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(RaceMaster::class);
    }

    public function testCreateInvalidRaceMaster(): void
    {
        static::createClient()->request('POST', '/api/race_masters', ['json' => [
            'raceTitle' => 'invalid',
            'raceDate' => 'invalid',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'The data is either an empty string or null, you should pass a string that can be parsed with the passed format or a valid DateTime string.',
        ]);
    }

    public function testLogin(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'hit@demo.com',
            'password' => 'hitdemo',
        ]]);
        
        $this->assertResponseIsSuccessful();
    }
}