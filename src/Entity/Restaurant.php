<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="restaurant", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Repository\RestaurantRepository")
 */
class Restaurant
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
     * @ORM\Column(name="enabled", type="integer", nullable=false)
     */
    private $enabled;

    /**
     * @var int
     *
     * @ORM\Column(name="selected", type="integer", nullable=false)
     */
    private $selected;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug = '';

    /**
     * @var string
     *
     * @ORM\Column(name="logo_url", type="string", length=255, nullable=true)
     */
    private $logoUrl = null;

    /**
     * @var string
     *
     * @ORM\Column(name="qr_url", type="string", length=255, nullable=false)
     */
    private $qrUrl = '';

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

    public function getEnabled(): int
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getSelected(): int
    {
        return $this->selected;
    }

    public function setSelected($selected): void
    {
        $this->selected = $selected;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl($logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }

    public function getQrUrl(): string
    {
        return $this->qrUrl;
    }

    public function setQrUrl($qrUrl): void
    {
        $this->qrUrl = $qrUrl;
    }
}
