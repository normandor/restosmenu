<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageVisits
 *
 * @ORM\Table(name="page_visits")
 * @ORM\Entity
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
     * @ORM\Column(name="datetime", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datetime = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=3, nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city_region", type="string", length=150, nullable=false)
     */
    private $cityRegion;


}
