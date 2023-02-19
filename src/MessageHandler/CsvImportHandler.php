<?php
namespace App\MessageHandler;

use App\Message\CsvImport;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\UpdatePlacementEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RaceDetails;
use App\Entity\RaceMaster;

class CsvImportHandler implements MessageHandlerInterface
{
    private $eventDispatcher;
    private $em;
    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
    }

    public function __invoke(CsvImport $message)
    {
        $data = $message->getContent();
        if(count($data) == 1 and isset($data['raceMasterId'])) {
            $this->executeUpdatePlacementEvent($data['raceMasterId']);
            return;
        }
        $this->createRaceDetailEntity($data);
    }

    private function  createRaceDetailEntity($data) {
        $raceDetails = new RaceDetails();
        $raceDetails->setFullName($data['fullName']);
        $raceDetails->setAgeCategory($data['ageCategory']);
        $raceDetails->setDistance($data['distance']);
        $raceDetails->setTime($data['time']);
        $raceMaster = $this->em->getRepository(RaceMaster::class)->find($data['raceMasterId']);
        $raceDetails->setRaceMaster($raceMaster);
        $this->em->persist($raceDetails);
        $this->em->flush();
    }
    private function executeUpdatePlacementEvent(int $raceMasterId) {
        $event = new UpdatePlacementEvent($raceMasterId);
        $this->eventDispatcher->dispatch($event, 'update.placement.event');
    }
}