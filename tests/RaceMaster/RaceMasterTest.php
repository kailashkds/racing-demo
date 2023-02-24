<?php

namespace App\Tests\RaceMaster;

use App\Entity\RaceMaster;
use App\Tests\AbstractTest;

class RaceMasterTest extends AbstractTest
{
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a user with a JWT token
        $this->token = $this->getToken();
    }

    public function testGetRaceMasterCollection(): void
    {
        $this->createRacemasterUsingEntityManager();

        $response = static::createClient()->request('GET', '/api/race_masters', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);
        $content = json_decode($response->getContent(), true);

        // Assertions about the get collcation of Race Master
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(3, $content['hydra:totalItems']);

        foreach ($content['hydra:member'] as $row) {
            $this->assertArrayHasKey('id', $row);
            $this->assertArrayHasKey('raceTitle', $row);
            $this->assertArrayHasKey('raceDate', $row);
            $this->assertArrayHasKey('avgTimeLongDistance', $row);
            $this->assertArrayHasKey('avgTimeMediumDistance', $row);
        }
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(RaceMaster::class);
    }

    public function testFilterByRaceTitle(): void
    {
        $this->createRacemasterUsingEntityManager();

        $response = static::createClient()->request('GET', '/api/race_masters?raceTitle=abc', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);

        $content = json_decode($response->getContent(), true);

        // Assertions about the Filter by Race Title
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());

        foreach ($content['hydra:member'] as $row) {
            $this->assertEquals('abc', $row['raceTitle']);
        }
    }

    public function testSortByRaceDate(): void
    {
        $this->createRacemasterUsingEntityManager();

        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $racemasterdata = $em->getRepository(RaceMaster::class)->findBy([],
            ['raceDate' => 'DESC']
        );

        $response = $client->request('GET', '/api/race_masters?order[raceDate]=desc', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);

        $content = json_decode($response->getContent(), true);

        // Assertions about the Sort by Race Date
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($racemasterdata[0]->getId(), $content['hydra:member'][0]['id']);
        $this->assertEquals($racemasterdata[0]->getRaceTitle(), $content['hydra:member'][0]['raceTitle']);
    }

    public function testCreateRacemaster(): void
    {
        $response = static::createClient()->request('POST', '/api/race_masters', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
            'json' => [
                'raceTitle' => 'Test Marathon',
                'raceDate' => '2023-02-18',
        ]]);

        // Assertions about the Sort by Race Date
        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesRegularExpression('~^/api/race_masters/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(RaceMaster::class);
    }

    public function testCreateInvalidRaceMaster()
    {
        $response = static::createClient()->request('POST', '/api/race_masters', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
            'json' => [
                'raceTitle' => 'Test Marathon',
        ]]);

        $content = json_decode($response->getBrowserKitResponse()->getContent());

        // Assertions about the create invalid Race master
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('raceDate', $content->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $content->violations[0]->message);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    private function createRacemasterUsingEntityManager()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $racemaster = new RaceMaster();
        $racemaster->setRaceTitle('abc');
        $racemaster->setRaceDate(new \DateTimeImmutable('2023-02-23'));
        $em->persist($racemaster);

        $racemaster1 = new RaceMaster();
        $racemaster1->setRaceTitle('def');
        $racemaster1->setRaceDate(new \DateTimeImmutable('2023-02-25'));
        $em->persist($racemaster1);

        $racemaster2 = new RaceMaster();
        $racemaster2->setRaceTitle('hij');
        $racemaster2->setRaceDate(new \DateTimeImmutable('2023-02-20'));
        $em->persist($racemaster2);

        $em->flush();
    }
}
