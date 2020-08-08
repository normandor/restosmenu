<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;

class ImageService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $imageId
     *
     * @return bool
     */
    public function removeImage($imageId): bool
    {
        $imageRepository = $this->entityManager->getRepository(Image::class);
        $image = $imageRepository->findOneBy(['id' => $imageId]);

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return true;
    }
}
