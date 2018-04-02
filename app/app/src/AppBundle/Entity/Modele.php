<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modele
 *
 * @ORM\Table(name="modele")
 *  @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeleRepository")
 */
class Modele
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
     * @ORM\Column(name="nom_complet_modele", type="string", length=120)
     */
    private $nomCompletModele;
   /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Marque",  fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $marque;

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
     * @return Modele
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
     * @return 
     */ 
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set the value of marque
     *
     * @return  self
     */ 
    public function setMarque(\AppBundle\Entity\Marque $marque)
    {
        $this->marque = $marque;

        return $this;
    }


    /**
     * Get the value of nomCompletModele
     *
     * @return  string
     */ 
    public function getNomCompletModele()
    {
        return $this->nomCompletModele;
    }

    /**
     * Set the value of nomCompletModele
     *
     * @param  string  $nomCompletModele
     * @ORM\PrePersist
     * @return  self
     */ 
    public function setNomCompletModele()
    {
        $this->nomCompletModele = $this->marque->getLibelle().' '.$this->libelle;

        return $this;
    }
}
