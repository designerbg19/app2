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
     * @ORM\Column(name="qte", type="integer")
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
     *@ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $Produit;
    
        /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commande")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Commande;
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
     * Get the value of Commande
     */ 
    public function getCommande()
    {
        return $this->Commande;
    }

    /**
     * Set the value of Commande
     *
     * @return  self
     */ 
    public function setCommande($Commande)
    {
        $this->Commande = $Commande;

        return $this;
    }
}

