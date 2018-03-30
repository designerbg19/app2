<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandeRepository")
 */
class Commande
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="string", length=50)
     */
    private $tva;

    /**
     * @var string
     *
     * @ORM\Column(name="ptixTotal", type="string", length=50)
     */
    private $ptixTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="numCmd", type="string", length=50)
     */
    private $numCmd;


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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Commande
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return Commande
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set ptixTotal
     *
     * @param string $ptixTotal
     *
     * @return Commande
     */
    public function setPtixTotal($ptixTotal)
    {
        $this->ptixTotal = $ptixTotal;

        return $this;
    }

    /**
     * Get ptixTotal
     *
     * @return string
     */
    public function getPtixTotal()
    {
        return $this->ptixTotal;
    }

    /**
     * Set numCmd
     *
     * @param string $numCmd
     *
     * @return Commande
     */
    public function setNumCmd($numCmd)
    {
        $this->numCmd = $numCmd;

        return $this;
    }

    /**
     * Get numCmd
     *
     * @return string
     */
    public function getNumCmd()
    {
        return $this->numCmd;
    }
}

