<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
     * @param int $value
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findParentByChildrenId($value): array
    {
        if (null === $value || 0 === $value) {
            return [0];
        }

        $row = $this->createQueryBuilder('u')
            ->select('u.parent')
            ->andWhere('u.id = :val')
            ->orderBy('u.position')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (0 === $row['parent'])
        {
            return [$row['parent']];
        }

        $menu = $row['parent'];

        $push = $this->findParentByChildrenId($row['parent']);

        if (is_array($push))
        {
            return array_merge([$menu], $push);
        }

        return [$menu, $push];
    }

    /**
     *
     * @param int   $parent_id
     * @param array $roles
     *
     * @return array
     */
    public function findChildrenByParentId($parent_id, $roles): array
    {
        $rows = $this->createQueryBuilder('u')
            ->select('u.id, u.name, u.path, u.icon')
            ->andWhere('u.parent = :parent_id')
            ->setParameter('parent_id', $parent_id)
            ->orderBy('u.position')
            ->getQuery()
            ->getArrayResult()
        ;

        $menu = [];

        foreach ($rows as $row)
        {
            // hardcoded, implement rights
            if (
                !$roles ||
                ('Personal' === $row['name'] && !in_array('ROLE_ADMIN', $roles, true))
            ) {
                continue;
            }

            if (!empty($row['path'])) {
                $tmp = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'path' => $row['path'],
                    'iconclass' => $row['icon'],
                    'values' => $this->findChildrenByParentId($row['id'], $roles),
                ];
            } else {
                $tmp = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'iconclass' => $row['icon'],
                    'values' => $this->findChildrenByParentId($row['id'], $roles),
                ];
            }
            $menu[] = $tmp;
        }
        return $menu;
    }
}
