<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Depot
 *
 * @ORM\Table(name="depot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=50)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="nTel1", type="string", length=50)
     */
    private $nTel1;

    /**
     * @var string
     *
     * @ORM\Column(name="nTel2", type="string", length=50)
     */
    private $nTel2;

        /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Depot
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Depot
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set nTel1
     *
     * @param string $nTel1
     *
     * @return Depot
     */
    public function setNTel1($nTel1)
    {
        $this->nTel1 = $nTel1;

        return $this;
    }

    /**
     * Get nTel1
     *
     * @return string
     */
    public function getNTel1()
    {
        return $this->nTel1;
    }

    /**
     * Set nTel2
     *
     * @param string $nTel2
     *
     * @return Depot
     */
    public function setNTel2($nTel2)
    {
        $this->nTel2 = $nTel2;

        return $this;
    }

    /**
     * Get nTel2
     *
     * @return string
     */
    public function getNTel2()
    {
        return $this->nTel2;
    }

    /**
     * Get the value of region
     */ 
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the value of region
     *
     * @return  self
     */ 
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }
}

