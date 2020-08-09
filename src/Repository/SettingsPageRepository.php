<?php

namespace App\Repository;

use App\Entity\SettingsPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsPage[]    findAll()
 * @method SettingsPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsPage::class);
    }

    public function findKeysForRestaurantId(int $restaurantId)
    {
        return $this->createQueryBuilder('u')
            ->select('u.key, u.name')
            ->where('u.restaurantId = :restaurantId')
            ->groupBy('u.key')
            ->setParameter('restaurantId', $restaurantId)
            ->getQuery()
            ->getResult()
            ;
    }

}
