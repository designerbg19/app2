<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProduitRepository")
 */
class Produit
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
     * @ORM\Column(name="imei", type="string", length=50)
     */
    private $imei;

    /**
     * @var string
     *
     * @ORM\Column(name="prixachat", type="string", length=60)
     */
    private $prixachat;

    /**
     * @var string
     *
     * @ORM\Column(name="prixveng", type="string", length=60)
     */
    private $prixveng;

    /**
     * @var string
     *
     * @ORM\Column(name="prixvend", type="string", length=60)
     */
    private $prixvend;

    /**
     * @var int
     *
     * @ORM\Column(name="qte", type="integer")
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="magremise", type="string", length=60)
     */
    private $magremise;


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
     * Set imei
     *
     * @param string $imei
     *
     * @return Produit
     */
    public function setImei($imei)
    {
        $this->imei = $imei;

        return $this;
    }

    /**
     * Get imei
     *
     * @return string
     */
    public function getImei()
    {
        return $this->imei;
    }

    /**
     * Set prixachat
     *
     * @param string $prixachat
     *
     * @return Produit
     */
    public function setPrixachat($prixachat)
    {
        $this->prixachat = $prixachat;

        return $this;
    }

    /**
     * Get prixachat
     *
     * @return string
     */
    public function getPrixachat()
    {
        return $this->prixachat;
    }

    /**
     * Set prixveng
     *
     * @param string $prixveng
     *
     * @return Produit
     */
    public function setPrixveng($prixveng)
    {
        $this->prixveng = $prixveng;

        return $this;
    }

    /**
     * Get prixveng
     *
     * @return string
     */
    public function getPrixveng()
    {
        return $this->prixveng;
    }

    /**
     * Set prixvend
     *
     * @param string $prixvend
     *
     * @return Produit
     */
    public function setPrixvend($prixvend)
    {
        $this->prixvend = $prixvend;

        return $this;
    }

    /**
     * Get prixvend
     *
     * @return string
     */
    public function getPrixvend()
    {
        return $this->prixvend;
    }

    /**
     * Set qte
     *
     * @param integer $qte
     *
     * @return Produit
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
     * Set magremise
     *
     * @param string $magremise
     *
     * @return Produit
     */
    public function setMagremise($magremise)
    {
        $this->magremise = $magremise;

        return $this;
    }

    /**
     * Get magremise
     *
     * @return string
     */
    public function getMagremise()
    {
        return $this->magremise;
    }
}

