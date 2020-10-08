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

    public function getAllRestaurants()
    {
        $qb = $this->createQueryBuilder('pv');
        $qb
            ->select('pv.restaurantId, r.name')
            ->leftJoin(
                'App\Entity\Restaurant',
                'r',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'r.id = pv.restaurantId'
            )
            ->groupBy('pv.restaurantId');

        $ret = [];
        foreach($qb->getQuery()->getResult() as $item) {
            $ret[] = [
                'id' => $item['restaurantId'],
                'name' => $item['name'],
            ];
        }

        return $ret;
    }

    public function getCountByRestaurantIdAndYearMonth($restaurantId, $yearMonth)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,7) = :laDate AND restaurant_id = :restaurantId';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['laDate' => $yearMonth, 'restaurantId' => $restaurantId]);

        return $stmt->fetchColumn(0);
    }
}
