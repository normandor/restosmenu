<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Images
 *
 * @ORM\Table(name="combo_dish", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ComboDishRepository")
 */
class ComboDish
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
     * @var int
     *
     * @ORM\Column(name="combo_id", type="integer", nullable=false)
     */
    private $comboId;

    /**
     * @var int
     *
     * @ORM\Column(name="dish_id", type="integer", nullable=false)
     */
    private $dishId;

    /**
     * @var int
     *
     * @ORM\Column(name="restaurant_id", type="integer", nullable=false)
     */
    private $restaurantId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_show", type="integer", nullable=false)
     */
    private $orderShow;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getComboId(): ?int
    {
        return $this->comboId;
    }

    public function setComboId($comboId)
    {
        return $this->comboId = $comboId;
    }

    public function getDishId(): ?int
    {
        return $this->dishId;
    }

    public function setDishId($dishId)
    {
        return $this->dishId = $dishId;
    }

    public function getRestaurantId(): ?int
    {
        return $this->restaurantId;
    }

    public function setRestaurantId($restaurantId)
    {
        return $this->restaurantId = $restaurantId;
    }

    public function geOrderShow(): int
    {
        return $this->orderShow;
    }

    public function setOrderShow($orderShow)
    {
        return $this->orderShow = $orderShow;
    }
}
