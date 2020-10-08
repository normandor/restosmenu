<?php

namespace App\Service;

use App\Entity\PageVisits;
use App\Entity\Dish;
use Codeception\PHPUnit\Constraint\Page;
use Doctrine\ORM\EntityManagerInterface;

class VisitLoggerService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $restaurantId
     *
     * @return array
     */
    public function getVisitsByRestaurantId($restaurantId): array
    {
        $pageVisitRepository = $this->entityManager->getRepository(PageVisits::class);
        $visits = $pageVisitRepository->findBy(
            ['restaurantId' => $restaurantId],
            ['datetime' => 'ASC']
        );

        $visitsArray = [];
        /** @var PageVisits $pageVisit */
        foreach ($visits as $pageVisit) {
            $visitsArray[] = [
                'ip' => $pageVisit->getId(),
                'agent' => $pageVisit->getAgent(),
                'date' => $pageVisit->getDatetime(),
            ];
        }

        return $visitsArray;
    }

    /**
     * @param $ip
     * @param $agent
     * @param $referer
     * @param $restaurantId
     */
    public function logVisit($ip, $agent, $referer, $restaurantId): void
    {
        /** @var PageVisits $pageVisit */
        $pageVisit = new \App\Entity\PageVisits();
        $pageVisit->setIp($ip);
        $pageVisit->setAgent($agent);
        $pageVisit->setReferer($referer);
        $pageVisit->setRestaurantId($restaurantId);
        $pageVisit->setDatetime(new \DateTime());

        $this->entityManager->persist($pageVisit);
        $this->entityManager->flush();
    }
}
