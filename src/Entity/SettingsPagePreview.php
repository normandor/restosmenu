<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Images
 *
 * @ORM\Table(name="settings_page_preview", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Repository\SettingsPagePreviewRepository")
 */
class SettingsPagePreview
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
     * @ORM\Column(name="key", type="string", length=250, nullable=false)
     */
    private $key = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="property", type="string", length=250, nullable=false)
     */
    private $property = '';

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=250, nullable=false)
     */
    private $value = '';

    /**
     * @var int
     *
     * @ORM\Column(name="restaurant_id", type="integer", nullable=false)
     */
    private $restaurantId;

    /**
     * @var int
     *
     * @ORM\Column(name="is_synced", type="integer", nullable=false)
     */
    private $isSynced;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRestaurantId(): int
    {
        return $this->restaurantId;
    }

    public function setRestaurantId($restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey($key): void
    {
        $this->key = $key;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty($property): void
    {
        $this->property = $property;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getIsSynced(): int
    {
        return $this->isSynced;
    }

    public function setIsSynced($isSynced): void
    {
        $this->isSynced = $isSynced;
    }
}
