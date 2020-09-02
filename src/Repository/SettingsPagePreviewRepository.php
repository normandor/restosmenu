<?php

namespace App\Repository;

use App\Entity\SettingsPagePreview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsPagePreview|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsPagePreview|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsPagePreview[]    findAll()
 * @method SettingsPagePreview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsPagePreviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsPagePreview::class);
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
