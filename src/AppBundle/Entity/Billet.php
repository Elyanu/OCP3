<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BilletRepository")
 */
class Billet
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
     * @ORM\Column(name="dateVisite", type="date")
     * @Assert\Date()
     */
    private $dateVisite;

    /**
     * @var string
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(name="nomf", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $nomf;

    /**
     * @var string
     * @ORM\Column(name="pays", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $pays;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateNaissance", type="date")
     * @Assert\Date()
     */
    private $dateNaissance;

    /**
     * @var integer
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $age;

    /**
     * @var bool
     * @ORM\Column(name="tarifReduit", type="boolean", nullable=true)
     */
    private $tarifReduit;

    /**
     * @var bool
     * @ORM\Column(name="statutTransaction", type="boolean")
     */
    private $statutTransaction = false;

    /**
     * @return boolean
     */
    public function isStatutTransaction()
    {
        return $this->statutTransaction;
    }

    /**
     * @param boolean $statutTransaction
     */
    public function setStatutTransaction($statutTransaction)
    {
        $this->statutTransaction = $statutTransaction;
    }

    /**
     * @return boolean
     */
    public function isTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * @param boolean $tarifReduit
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="Commande", inversedBy="billets")
     */
    private $commande;

    public function __construct()
    {
        $this->prix = 12;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * Set dateVisite
     *
     * @param \DateTime $dateVisite
     *
     * @return Billet
     */
    public function setDateVisite($dateVisite)
    {
        $this->dateVisite = $dateVisite;

        return $this;
    }

    /**
     * Get dateVisite
     *
     * @return \DateTime
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Billet
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set commande
     *
     */
    public function setCommande(Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return string
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nomf
     *
     * @param string $nomf
     *
     * @return Billet
     */
    public function setNomf($nomf)
    {
        $this->nomf = $nomf;

        return $this;
    }

    /**
     * Get nomf
     *
     * @return string
     */
    public function getNomf()
    {
        return $this->nomf;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }


    /**
     * Get tarifReduit
     *
     * @return boolean
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * Get statutTransaction
     *
     * @return boolean
     */
    public function getStatutTransaction()
    {
        return $this->statutTransaction;
    }
}
