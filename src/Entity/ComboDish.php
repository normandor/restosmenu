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
     * @ORM\Column(name="comboId", type="integer", nullable=false)
     */
    private $comboId;

    /**
     * @var int
     *
     * @ORM\Column(name="dishId", type="integer", nullable=false)
     */
    private $dishId;

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
}
