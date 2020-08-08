<?php

namespace App\Service;

use App\Entity\Combo;
use Doctrine\ORM\EntityManagerInterface;

class ComboService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getEnabledCombosByRestaurantId($restaurantId): array
    {
        $comboRepository = $this->entityManager->getRepository(Combo::class);

        return $comboRepository->findBy(['enabled' => 1, 'restaurantId' => $restaurantId], ['id' => 'ASC']);
    }
}
