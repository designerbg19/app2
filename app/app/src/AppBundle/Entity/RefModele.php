<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefModele
 *
 * @ORM\Table(name="ref_modele")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RefModeleRepository")
 */
class RefModele
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
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="prodyear", type="string", length=50)
     */
    private $prodyear;

  /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Modele")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modele;
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return RefModele
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set prodyear
     *
     * @param string $prodyear
     *
     * @return RefModele
     */
    public function setProdyear($prodyear)
    {
        $this->prodyear = $prodyear;

        return $this;
    }

    /**
     * Get prodyear
     *
     * @return string
     */
    public function getProdyear()
    {
        return $this->prodyear;
    }

    /**
     * Get the value of modele
     */ 
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set the value of modele
     *
     * @return  self
     */ 
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

}

