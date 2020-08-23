<?php

namespace App\Service;

use App\Entity\SettingsImage;
use Doctrine\ORM\EntityManagerInterface;

class SettingsImageService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $restaurantId
     *
     * @return array
     */
    public function getPropertiesByRestaurantId(int $restaurantId) : array
    {
        $categories = $this->entityManager->getRepository(SettingsImage::class)
            ->findKeysForRestaurantId($restaurantId);

        $settings = [];
        foreach ($categories as $category) {
            $newSetting = [
                'name' => $category['name'],
            ];

            $properties = $this->entityManager->getRepository(SettingsImage::class)
                ->findBy([
                    'key' => $category['key'],
                    'restaurantId' => $restaurantId,
                ]);

            $newProperties = [];
            /** @var SettingsImage $property */
            foreach ($properties as $property) {
                if (null === $property->getValueMobile()) {
                    $newProperties[$property->getProperty()] = $property->getValue();
                } else {
                    $newProperties[$property->getProperty()] = [
                        'desktop' => $property->getValue(),
                        'mobile' => $property->getValueMobile(),
                    ];
                }
            }
            $newSetting['properties'] = $newProperties;

            $settings[$category['key']] =  $newSetting;
        }

        return $settings;
    }
}
