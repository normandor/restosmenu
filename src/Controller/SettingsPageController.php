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
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Inflector;

class SettingsPageController extends AbstractController
{
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
}
