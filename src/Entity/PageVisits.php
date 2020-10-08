<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageVisits
 *
 * @ORM\Table(name="page_visits")
 * @ORM\Entity(repositoryClass="App\Repository\PageVisitsRepository")
 */
class PageVisits
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
     * @ORM\Column(name="ip", type="string", length=50, nullable=false)
     */
    private $ip;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=3, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city_region", type="string", length=150, nullable=true)
     */
    private $cityRegion;

    /**
     * @var int
     *
     * @ORM\Column(name="restaurant_id", type="integer", nullable=true)
     */
    private $restaurantId;

    /**
     * @var string
     *
     * @ORM\Column(name="referer", type="string", length=255, nullable=false)
     */
    private $referer;

    /**
     * @var string
     *
     * @ORM\Column(name="agent", type="string", length=255, nullable=false)
     */
    private $agent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCityRegion(): ?string
    {
        return $this->cityRegion;
    }

    public function setCityRegion(string $cityRegion): self
    {
        $this->cityRegion = $cityRegion;

        return $this;
    }

    public function getRestaurantId(): ?int
    {
        return $this->restaurantId;
    }

    public function setRestaurantId(?int $restaurantId): self
    {
        $this->restaurantId = $restaurantId;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }
}
