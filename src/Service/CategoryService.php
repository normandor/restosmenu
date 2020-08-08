<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getEnabledCategoriesByRestaurantId($restaurantId): array
    {
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        return $categoryRepository->findBy(['enabled' => 1, 'restaurantId' => $restaurantId], ['id' => 'ASC']);
    }
}
