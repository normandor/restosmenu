<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Images
 *
 * @ORM\Table(name="image", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=false)
     */
    private $name = '';

    /**
     * @var int
     *
     * @ORM\Column(name="section_id", type="integer", nullable=false)
     */
    private $sectionId;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=250, nullable=false)
     */
    private $imageUrl = '';

    /**
     * @var int
     *
     * @ORM\Column(name="enabled", type="integer", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $lastUpdate;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setImageUrl($imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getSectionId(): int
    {
        return $this->sectionId;
    }

    public function setSectionid($sectionId): void
    {
        $this->sectionId = $sectionId;
    }

    public function getEnabled(): int
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }
}
