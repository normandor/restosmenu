<?php

namespace App\Repository;

use App\Entity\ComboDish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComboDish|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComboDish|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComboDish[]    findAll()
 * @method ComboDish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComboDishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComboDish::class);
    }

    public function findGroupedByComboId(int $comboId)
    {
        return $this->createQueryBuilder('u')
            ->select('u.dishId, count(u.dishId) as count')
            ->where('u.comboId = :comboId')
            ->groupBy('u.dishId')
            ->setParameter('comboId', $comboId)
            ->getQuery()
            ->getResult()
        ;
    }
}
