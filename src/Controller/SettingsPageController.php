<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\SettingsPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsPageController extends AbstractController
{
    /**
     * @param string $key
     * @param string $property
     * @param string $value
     *
     * @return JsonResponse
     */
    public function editSetting(string $key, string $property, string $value): JsonResponse
    {
        /** @var SettingsPage $setting */
        $setting = $this->getDoctrine()->getRepository(SettingsPage::class)
            ->findOneBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'key' => $key,
                'property' => $property,
            ]);

        if (!$setting) {
            return new JsonResponse(['message' => 'Property not found', 'property' => $property], 400);
        }

        if (
            'color' === $property ||
            'background-color' === $property
        ) {
            $value = '#'.$value;
        }
        $setting->setValue($value);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($setting);
        $entityManager->flush();

        return new JsonResponse(['message' => 'OK'], 200);
    }

    /**
     * @param int $id
     * @param int $newPosition
     *
     * @return JsonResponse
     */
    public function updateCategoryOrder(int $id, int $newPosition): JsonResponse
    {
        /** @var Category[] $categories */
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
            ],['orderShow' => 'ASC']);

        if ($newPosition > count($categories)) {
            $newPosition = count($categories);
        }

        $pos = 0;
        /** @var Category $category */
        foreach ($categories as $category ) {
            if ($category->getId() === $id) {
                $category->setOrderShow($newPosition);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();

                continue;
            }

            $pos++;

            if ($pos === $newPosition && $category->getId() !== $id) {
                $pos++;
            }

            $category->setOrderShow($pos);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }

        return new JsonResponse(['message' => 'OK'], 200);
    }

    /**
     * @param int $dishId
     * @param int $categoryId
     * @param int $newPosition
     *
     * @return JsonResponse
     */
    public function updateDishInComboOrder(int $dishId, int $categoryId, int $newPosition): JsonResponse
    {
        /** @var ComboDish[] $comboDishes */
        $comboDishes = $this->getDoctrine()->getRepository(ComboDish::class)
            ->findBy(
                [
                    'restaurantId' => $this->getUser()->getRestaurantId(),
                    'comboId' => $categoryId,
                ],
                ['orderShow' => 'ASC']
            );

        if ($newPosition > count($comboDishes)) {
            $newPosition = count($comboDishes);
        }

        $pos = 0;
        /** @var ComboDish $comboDish */
        foreach ($comboDishes as $comboDish ) {
            if ($comboDish->getDishId() === $dishId) {
                $comboDish->setOrderShow($newPosition);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comboDish);
                $entityManager->flush();

                continue;
            }

            $pos++;

            if ($pos === $newPosition && $comboDish->getDishId() !== $dishId) {
                $pos++;
            }

            $comboDish->setOrderShow($pos);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comboDish);
            $entityManager->flush();
        }

        return new JsonResponse(['message' => 'OK'], 200);
    }

    /**
     * @param int $dishId
     * @param int $categoryId
     * @param int $newPosition
     *
     * @return JsonResponse
     */
    public function updateDishInCategoryOrder(int $dishId, int $categoryId, int $newPosition): JsonResponse
    {
        /** @var Dish[] $dishes */
        $dishes = $this->getDoctrine()->getRepository(Dish::class)
            ->findBy(
                [
                    'restaurantId' => $this->getUser()->getRestaurantId(),
                    'categoryId' => $categoryId,
                ],
                ['orderShow' => 'ASC']
            );

        if ($newPosition > count($dishes)) {
            $newPosition = count($dishes);
        }

        $pos = 0;
        /** @var Dish $dish */
        foreach ($dishes as $dish ) {
            if ($dish->getId() === $dishId) {
                $dish->setOrderShow($newPosition);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($dish);
                $entityManager->flush();

                continue;
            }

            $pos++;

            if ($pos === $newPosition && $dish->getId() !== $dishId) {
                $pos++;
            }

            $dish->setOrderShow($pos);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dish);
            $entityManager->flush();
        }

        return new JsonResponse(['message' => 'OK'], 200);
    }
}
