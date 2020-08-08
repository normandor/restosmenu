<?php

namespace App\Service;

use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DishService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int categoryId
     *
     * @return array
     */
    public function getDishesByCategoryId($categoryId): array
    {
        $dishRepository = $this->entityManager->getRepository(Dish::class);
        $dishes = $dishRepository->findBy(['categoryId' => $categoryId, 'enabled' => 1]);

        $dishArray = [];
        /** @var Dish $dish */
        foreach ($dishes as $dish) {
            $dishArray[] = [
                'label' => $dish->getName(),
                'descripcion' => $dish->getDescription(),
                'image' => $dish->getImage(),
                'icons' => [],
                'price' =>  $dish->getPrice(),
                'currency' =>  $dish->getCurrency()->getSymbol(),
                'available' => $dish->getEnabled(),
            ];
        }

        return $dishArray;
    }

    /**
     * @param int categoryId
     *
     * @return array
     */
    public function getDishesByComboId($comboId): array
    {
        $comboDishRepository = $this->entityManager->getRepository(ComboDish::class);
        $comboDishes = $comboDishRepository->findGroupedByComboId($comboId);

        $comboDishArray = [];
        foreach ($comboDishes as $comboDish) {
            $dishRepository = $this->entityManager->getRepository(Dish::class);
            $dish = $dishRepository->findOneBy(['id' => $comboDish['dishId'], 'enabled' => 1]);

            if (null !== $dish) {
                $comboDishArray[] = [
                    'count' => $comboDish['count'],
                    'label' => $dish->getName(),
                    'descripcion' => $dish->getDescription(),
                    'image' => $dish->getImage(),
                    'icons' => [],
                ];
            }
        }

        return $comboDishArray;
    }
}