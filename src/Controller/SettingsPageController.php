<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Currency;
use App\Entity\Dish;
use App\Entity\SettingsPage;
use App\Form\Type\CategoryType;
use App\Form\Type\ComboType;
use App\Service\FileUploader;
use App\Service\PagesService;
use App\Service\SettingsPageService;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Inflector;

class SettingsPageController extends AbstractController
{
    const ORDER_UP = 'up';
    const ORDER_DOWN = 'down';

    public function editSetting($id, $property, $value)
    {
        /** @var SettingsPage $setting */
        $setting = $this->getDoctrine()->getRepository(SettingsPage::class)
            ->findOneBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'key' => $id,
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

    public function editOrder(int $id, int $newPosition, SettingsPageService $settingsPageService)
    {
        /** @var Category $categories */
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
            ],['orderShow' => 'ASC']);

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
}
