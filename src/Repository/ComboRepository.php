<?php

namespace App\Repository;

use App\Entity\Combo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Combo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Combo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Combo[]    findAll()
 * @method Combo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComboRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Combo::class);
    }

    /**
     * @param int $restaurantId
     */
    public function getEnabledCombosForRestaurant(int $restaurantId)
    {
        return $this->findBy(
            ['enabled' => 1, 'restaurantId' => $restaurantId],
            ['id' => 'ASC']
        );
    }
}
