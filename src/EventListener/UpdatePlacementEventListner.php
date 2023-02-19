<?php

namespace App\EventListener;

use App\Event\UpdatePlacementEvent;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePlacementEventListner
{
    private $entityManager;

    const UPDATE_PLACEMENT_QUERY = "UPDATE race_details SET `overall_placement` = ( SELECT COUNT(*) FROM ( SELECT DISTINCT `time` FROM race_details where distance = 'long' and race_master_id = %s ) AS t WHERE `time` < race_details.`time`) + 1 where distance = 'long'  and race_master_id = %s ;";
    const UPDATE_AGE_PLACEMENT_QUERY = "UPDATE race_details AS r1 LEFT JOIN ( SELECT r1.Id, COALESCE(COUNT(r2.Id), 0) + 1 AS rank FROM race_details AS r1 LEFT JOIN race_details AS r2 ON r1.age_category = r2.age_category AND r1.distance = r2.distance AND  r1.race_master_id = r2.race_master_id AND (r1.time > r2.time OR (r1.time = r2.time AND r1.overall_placement < r2.overall_placement)) WHERE r1.distance = 'long' and r1.race_master_id = %s GROUP BY r1.Id ) AS t ON r1.Id = t.Id SET r1.age_category_placement = t.rank;";

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function onUpdatePlacementEvent(UpdatePlacementEvent $event)
    {
        try{
            $raceMasterId = $event->getData();

            $stmt = $this->entityManager->getConnection()->prepare(sprintf(UPDATE_PLACEMENT_QUERY,$raceMasterId));
            $stmt->executeQuery();

            $stmt = $this->entityManager->getConnection()->prepare(sprintf(UPDATE_AGE_PLACEMENT_QUERY,$raceMasterId));
            $stmt->executeQuery();
        } catch (\Throwable $e)
        {

        }
    }
}
