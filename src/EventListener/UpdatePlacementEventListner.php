<?php

namespace App\EventListener;

use App\Entity\RaceDetails;
use App\Event\UpdatePlacementEvent;
use App\Message\CsvImport;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdatePlacementEventListner
{
    private $entityManager;
    private $messageBus;
    private $logger;
    public const UPDATE_PLACEMENT_QUERY = "UPDATE race_details SET `overall_placement` = ( SELECT COUNT(*) FROM ( SELECT DISTINCT `time` FROM race_details where distance = 'long' and race_master_id = %s ) AS t WHERE `time` < race_details.`time`) + 1 where distance = 'long'  and race_master_id = %s ;";
    public const UPDATE_AGE_PLACEMENT_QUERY = "UPDATE race_details AS r1 LEFT JOIN ( SELECT r1.Id, COALESCE(COUNT(r2.Id), 0) + 1 AS rank FROM race_details AS r1 LEFT JOIN race_details AS r2 ON r1.age_category = r2.age_category AND r1.distance = r2.distance AND  r1.race_master_id = r2.race_master_id AND (r1.time > r2.time OR (r1.time = r2.time AND r1.overall_placement < r2.overall_placement)) WHERE r1.distance = 'long' and r1.race_master_id = %s GROUP BY r1.Id ) AS t ON r1.Id = t.Id SET r1.age_category_placement = t.rank;";
    public const UPDATE_AVG_TIME_LONG_DISTANCE_QUERY = "UPDATE race_master SET `avg_time_long_distance` = ( SELECT SEC_TO_TIME(AVG(time)) AS average_time FROM race_details where distance = 'long' and race_master_id = %s) where id= %s;";
    public const UPDATE_AVG_TIME_MEDIUM_DISTANCE_QUERY = "UPDATE race_master SET `avg_time_medium_distance` = ( SELECT SEC_TO_TIME(AVG(time)) AS average_time FROM race_details where distance = 'medium' and race_master_id = %s) where id= %s;";

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function onUpdatePlacementEvent(UpdatePlacementEvent $event)
    {
        try {
            $raceMasterId = $event->getData();
            $this->udapteQueryExecution($raceMasterId);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTrace());
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if ('PUT' == $event->getRequest()->getMethod()
            && 0 === strpos($event->getRequest()->getRequestUri(), '/api/race_details')
            && 200 == $event->getResponse()->getStatusCode()
        ) {
            $raceDetail = $this->entityManager->getRepository(RaceDetails::class)->find($event->getRequest()->get('id'));
            $csvImportMessage = new CsvImport(['raceMasterId' => $raceDetail->getRaceMaster()->getId()]);
            $this->messageBus->dispatch($csvImportMessage);
        }
    }

    private function udapteQueryExecution(int $raceMasterId)
    {
        try {
            $stmt = $this->entityManager->getConnection()->prepare(sprintf(self::UPDATE_PLACEMENT_QUERY, $raceMasterId, $raceMasterId));
            $stmt->executeQuery();

            $stmt = $this->entityManager->getConnection()->prepare(sprintf(self::UPDATE_AGE_PLACEMENT_QUERY, $raceMasterId));
            $stmt->executeQuery();

            $stmt = $this->entityManager->getConnection()->prepare(sprintf(self::UPDATE_AVG_TIME_LONG_DISTANCE_QUERY, $raceMasterId, $raceMasterId));
            $stmt->executeQuery();

            $stmt = $this->entityManager->getConnection()->prepare(sprintf(self::UPDATE_AVG_TIME_MEDIUM_DISTANCE_QUERY, $raceMasterId, $raceMasterId));
            $stmt->executeQuery();
        } catch (\Throwable $e) {
            $this->logger->error($raceMasterId);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTrace());
        }
    }
}
