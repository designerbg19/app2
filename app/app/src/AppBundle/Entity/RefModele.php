<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefModele
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(name="nom_complet", type="string", length=150)
     */
    private $nomComplet;

    /**
     * @var string
     *
     * @ORM\Column(name="prodyear", type="string", length=50)
     */
    private $prodyear;

  /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Modele",  fetch="EAGER")
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

 


    /**
     * Get the value of nomComplet
     *
     * @return  string
     */ 
    public function getNomComplet()
    {
        return $this->nomComplet;
    }

    /**
     * Set the value of nomComplet
     *
     * @param  string  $nomComplet
     * @ORM\PrePersist
     * @return  self
     */ 
    public function setNomComplet()
    {
      //  var_dump('hello');die;
        $this->nomComplet = $this->modele->getNomCompletModele().' '.$this->libelle;

        return $this;
    }
    public function __toString(){
      //  var_dump('hello');die;
        return $this->nomComplet;
    }
}

