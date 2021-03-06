<?php

namespace App\Service;

use App\Entity\SettingsPage;
use Doctrine\ORM\EntityManagerInterface;

class SettingsPageService
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
        $categories = $this->entityManager->getRepository(SettingsPage::class)
            ->findKeysForRestaurantId($restaurantId);

        $settings = [];
        foreach ($categories as $category) {
            $newSetting = [
                'name' => $category['name'],
            ];

            $properties = $this->entityManager->getRepository(SettingsPage::class)
                ->findBy([
                    'key' => $category['key'],
                    'restaurantId' => $restaurantId,
                ]);

            $newProperties = [];
            /** @var SettingsPage $property */
            foreach ($properties as $property) {
                $newProperties[$property->getProperty()] = $property->getValue();
            }
            $newSetting['properties'] = $newProperties;

            $settings[$category['key']] =  $newSetting;
        }

        return $settings;
    }
}
