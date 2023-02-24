<?php

namespace App\Tests\RaceMaster;

use App\Entity\RaceMaster;
use App\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileTest extends AbstractTest
{
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a user with a JWT token
        $this->token = $this->getToken();
    }

    public function testUploadInvalidCsvFile()
    {
        // Call for create Raceaster
        $this->createRacemasterUsingEntityManager();

        $client = static::createClient();

        $response = $client->request('POST', '/api/race_masters/1/importcsv', ['headers' => [
            'Authorization' => 'Bearer '.$this->token,
            ],
        ],
            []);

        $content = json_decode($response->getBrowserKitResponse()->getContent(), true);

        // Assertions about the Upload Invalid Csv File
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('CSV File is required', $content['hydra:description']);
    }

    public function testUploadCsvFile(): void
    {
        // Call for create Raceaster
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

        $content = json_decode($response->getContent(), true);

        // Assertions about the upload csv file with valid parameters
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Data is imported, It may take a little while to process it.', $content['success']);
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
