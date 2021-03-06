<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * lineCmd
 *
 * @ORM\Table(name="line_cmd")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\lineCmdRepository")
 */
class lineCmd
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
     * @var int
     *
     * @ORM\Column(name="qte", type="integer" )
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="prixTotal", type="string", length=60)
     */
    private $prixTotal;


        /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Produit")
     *@ORM\JoinColumn(name="produit", referencedColumnName="id")
     */
    private $Produit;
    /**
     *
     *  @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAdmin")
     */
    private $user;
    /**
     * @var string
     *  @ORM\Column(name="etat", type="string")
     */
    private $etat;
    
 
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
     * Set qte
     *
     * @param integer $qte
     *
     * @return lineCmd
     */
    public function setQte($qte)
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte
     *
     * @return int
     */
    public function getQte()
    {
        return $this->qte;
    }

    /**
     * Set prixTotal
     *
     * @param string $prixTotal
     *
     * @return lineCmd
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    /**
     * Get prixTotal
     *
     * @return string
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }

    /**
     * Get the value of Produit
     */ 
    public function getProduit()
    {
        return $this->Produit;
    }

    /**
     * Set the value of Produit
     *
     * @return  self
     */ 
    public function setProduit($Produit)
    {
        $this->Produit = $Produit;

        return $this;
    }

   

    /**
     * Set user
     *
     * @param \AppBundle\Entity\UserAdmin $user
     *
     * @return lineCmd
     */
    public function setUser(\AppBundle\Entity\UserAdmin $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\UserAdmin
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return lineCmd
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }
}
