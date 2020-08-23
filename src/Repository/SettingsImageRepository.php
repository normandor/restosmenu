<?php

namespace App\Repository;

use App\Entity\SettingsImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsImage[]    findAll()
 * @method SettingsImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsImage::class);
    }

    public function findKeysForRestaurantId(int $restaurantId)
    {
        return $this->createQueryBuilder('u')
            ->select('u.key, u.name')
            ->where('u.restaurantId = :restaurantId')
            ->groupBy('u.key')
            ->setParameter('restaurantId', $restaurantId)
            ->orderBy('u.name')
            ->getQuery()
            ->getResult()
            ;
    }

}
