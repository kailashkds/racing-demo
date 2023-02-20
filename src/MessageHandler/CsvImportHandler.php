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
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class CsvImportHandler implements MessageHandlerInterface
{
    private $eventDispatcher;
    private $em;
    private $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $em,
        LoggerInterface $logger
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function __invoke(CsvImport $message)
    {
        $data = $message->getContent();
        $this->logger->debug(json_encode($data));
        if(count($data) == 1 and isset($data['raceMasterId'])) {
            $this->executeUpdatePlacementEvent((int)$data['raceMasterId']);
            return;
        }
        $this->createRaceDetailEntity($data);
    }

    private function createRaceDetailEntity($data) {
        $raceDetails = new RaceDetails();
        $raceDetails->setFullName($data['fullName']);
        $raceDetails->setDistance($data['distance']);
        $raceDetails->setTime(new DateTimeImmutable($data['time']));
        $raceDetails->setAgeCategory($data['ageCategory']);
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