<?php

namespace App\Repository;

use App\Entity\PageVisits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageVisits|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageVisits|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageVisits[]    findAll()
 * @method PageVisits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageVisitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageVisits::class);
    }
}
