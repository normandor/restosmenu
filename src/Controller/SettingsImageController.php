<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SettingsImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsImageController extends AbstractController
{
    const PLATFORM_MOBILE = 'mobile';
    const PLATFORM_DESKTOP = 'desktop';

    /**
     * @param string $key
     * @param string $property
     * @param string $value
     * @param string $platform
     *
     * @return JsonResponse
     */
    public function editSetting(string $key, string $property, string $value, string $platform): JsonResponse
    {
        /** @var SettingsImage $setting */
        $setting = $this->getDoctrine()->getRepository(SettingsImage::class)
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

        if (self::PLATFORM_MOBILE === $platform) {
            $setting->setValueMobile($value);
        } else {
            $setting->setValue($value);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($setting);
        $entityManager->flush();

        if ('visibility' === $property) {
            /** @var Category $category */
            $category = $this->getDoctrine()->getRepository(Category::class)
                ->findOneBy([
                    'restaurantId' => $this->getUser()->getRestaurantId(),
                    'categoryType' => PageController::CATEGORY_IMAGE,
                    'name' => $key,
                ]);

            if (null !== $category) {
                $category->setEnabled(('true' === $value) ? 1 : 0);
                $entityManager->persist($category);
                $entityManager->flush();
            }
        }

        return new JsonResponse(['message' => 'OK'], 200);
    }
}
