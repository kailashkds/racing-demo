<?php

namespace App\Tests\RaceDetails;

use App\Entity\RaceDetails;
use App\Entity\RaceMaster;
use App\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RaceDetailsTest extends AbstractTest
{
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a user with a JWT token
        $this->token = $this->getToken();
    }

    public function testGetRaceDetailsCollection(): void
    {
        $this->createRacemasterUsingEntityManager();
        $this->uploadCsvFile();

        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/race_details?raceMaster=1', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(2, $content['hydra:totalItems']);

        foreach ($content['hydra:member'] as $row) {
            $this->assertArrayHasKey('id', $row);
            $this->assertArrayHasKey('fullName', $row);
            $this->assertArrayHasKey('distance', $row);
            $this->assertArrayHasKey('time', $row);
            $this->assertArrayHasKey('ageCategory', $row);
            $this->assertArrayHasKey('overallPlacement', $row);
            $this->assertArrayHasKey('ageCategoryPlacement', $row);
        }

        $this->assertEquals(1, $content['hydra:member'][0]['overallPlacement']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUpdateRaceDetails(): void
    {
        $this->createRacemasterUsingEntityManager();
        $this->uploadCsvFile();

        $client = static::createClient();

        $response = $client->request('PUT', '/api/race_details/1', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
            'json' => [
                'fullName' => 'Testupdate',
                'distance' => 'long',
                'time' => '05:22:06',
                'ageCategory' => 'M30-35',
        ]]);

        $content = json_decode($response->getContent(), true);

        // Assertions about the update race details
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Testupdate', $content['fullName']);
        $this->assertEquals('long', $content['distance']);
        $this->assertEquals('M30-35', $content['ageCategory']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testSortByOverAllPlacement(): void
    {
        $this->createRacemasterUsingEntityManager();

        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $racedetaildata = $em->getRepository(RaceDetails::class)->findBy([],
            ['overallPlacement' => 'DESC']
        );

        $response = $client->request('GET', '/api/race_details?order[overallPlacement]=desc&raceMaster=1', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);

        // Assertions about the sort by overall placement
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
    }

    private function uploadCsvFile()
    {
        $this->createRacemasterUsingEntityManager();

        $client = static::createClient();
        $publicPath = $client->getContainer()->getParameter('kernel.project_dir').'/public/tests/';
        $file = new UploadedFile($publicPath.'validData.csv', 'validData.csv', 'text/csv');

        $response = $client->request('POST', '/api/race_masters/1/importcsv', [
            'headers' => ['Content-Type' => 'multipart/form-data', 'Authorization' => 'Bearer '.$this->token],
            'extra' => [
                'files' => [
                    'csv' => $file,
                ],
            ],
        ]);
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
