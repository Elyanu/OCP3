<?php

namespace AppBundle\Services;

use AppBundle\Entity\Commande;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Billet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationService extends Controller
{
    protected $em;
    protected $gratuit;
    protected $enfant;
    protected $reduit;
    protected $normal;
    protected $senior;

    public function __construct(EntityManager $em, $gratuit, $enfant, $reduit, $normal, $senior)
    {
        $this->em = $em;
        $this->gratuit = $gratuit;
        $this->enfant = $enfant;
        $this->reduit = $reduit;
        $this->normal = $normal;
        $this->senior = $senior;

    }

    // ÉTAPE 1 : Instancie une nouvelle commande à partir des infos du premier formulaire
    public function createCommande($session, $day, $dureeVisite, $email)
    {
        $today = new \DateTime('today');
        $hour = date('H');
        if ($day == $today && $dureeVisite === 'full' && $hour > 14) {
            return false;
        }
        $commande = new Commande();
        $commande->setSession($session);
        $commande->setDateVisite($day);
        $commande->setDureeVisite($dureeVisite);
        $commande->setEmail($email);
        $this->saveCommande($commande);
        $this->setToken($commande->getId());
        return $commande;
    }

    // Persiste la commande en base de données
    public function saveCommande($commande)
    {
        $this->em->persist($commande);
        $this->em->flush();
    }

    // Attribue un numéro de commande unique (date + id, au format 160912_15)
    public function setToken($id)
    {
        $commande = $this->getCommande($id);
        $date = date('ymd');
        $commande->setToken($date.'_'.$id);
        $this->saveCommande($commande);
    }

    // Récupère une commande par son id
    public function getCommande($id)
    {
        $repository = $this->em->getRepository('AppBundle:Commande');
        return $repository->find($id);
    }

    // ÉTAPE 2 : Instancie un billet pour chaque formulaire rempli
    public function createBillet($id, $prenom, $nomf, $pays, $dateNaissance, $tarifReduit)
    {
        $billet = new Billet();
        $billet->setCommande($this->getCommande($id));
        $billet->setDateVisite($this->getCommande($id)->getDateVisite());
        $billet->setPrenom($prenom);
        $billet->setNomf($nomf);
        $billet->setPays($pays);
        $billet->setDateNaissance($dateNaissance);
        $billet->setTarifReduit($tarifReduit);
        $this->saveBillet($billet);
        $billetId = $billet->getId();
        $this->getAgeVisiteur($billetId);
        $this->getPrix($billetId);
    }

    // Persiste le billet en base de données
    public function saveBillet($billet)
    {
        $this->em->persist($billet);
        $this->em->flush();
    }

    // Récupère un billet par son id
    public function getBillet($billetId)
    {
        $repository = $this->em->getRepository('AppBundle:Billet');
        return $repository->find($billetId);
    }

    // Supprime un billet de la base de données
    public function removeBillet($billetId)
    {
        $billet = $this->getBillet($billetId);
        $this->em->remove($billet);
        $this->em->flush();
    }

    // Calcule la différence entre la date de la visite et la date de naissance saisie
    // Persiste l'âge du visiteur en base de données
    public function getAgeVisiteur($billetId)
    {
        $billet = $this->getBillet($billetId);
        $dateNaissance = $billet->getDateNaissance();
        $visite = $billet->getDateVisite();
        $interval = $visite->diff($dateNaissance);
        $age = $interval->y;
        $billet->setAge($age);
        $this->saveBillet($billet);
    }

    // Récupère l'âge du visiteur et attribue le tarif
    // Persiste le prix du billet en base de données
    public function getPrix($billetId)
    {
        $billet = $this->getBillet($billetId);
        $age = $billet->getAge();
        $id = $billet->getCommande();
        $tarifReduit = $billet->isTarifReduit();
        $commande = $this->getCommande($id);
        $dureeVisite = $commande->getDureeVisite();

        // Tarif gratuit
        if ($age < 4) {
            $billet->setPrix($this->gratuit);
        }
        // Tarif enfant
        elseif ($age < 12) {
            if ($dureeVisite === 'full')
            {
                $billet->setPrix($this->enfant);
            }
            else {
                $billet->setPrix($this->enfant/2);
            }

        }
        // Tarif normal
        elseif ($age < 60) {
            if (!$tarifReduit) {
                if ($dureeVisite === 'full')
                {
                    $billet->setPrix($this->normal);
                }
                else {
                    $billet->setPrix($this->normal/2);
                }
            }
            else if ($dureeVisite === 'full') {
                $billet->setPrix($this->reduit);
            }
            else {
                $billet->setPrix($this->reduit/2);
            }

        }
        // Tarif senior
        else {
            if ($dureeVisite === 'full')
            {
                $billet->setPrix($this->senior);
            }
            else {
                $billet->setPrix($this->senior/2);
            }
        }
        $this->saveBillet($billet);
    }

    // Récupère le tarif de tous les billets de la commande
    // Aditionne et persiste le montant en base de données
    public function getMontant($id)
    {
        $commande = $this->getCommande($id);
        $montant = 0;
        foreach ($commande->getBillets() as $billet) {
            $montant += $billet->getPrix();
        }
        $commande->setMontant($montant);
        $this->saveCommande($commande);
        return $montant;
    }

    // Retourne le nombre de billets réservés pour une date donnée (depuis le datepicker JS)
    public function getNombreBillets($day)
    {
        $str_date = $day;
        $obj_date = \DateTime::createFromFormat('d-m-yy', $str_date);
        $repository = $this->em->getRepository('AppBundle:Billet');
        $billets = $repository->findBy(array('dateVisite' => $obj_date, 'statutTransaction' => true));
        $quantite = 0;
        foreach ($billets as $billet) {
            $quantite++;
        }
        return $quantite;
    }

    // Retourne le nombre de billets réservés pour une date donnée
    public function getNbrBillets($day)
    {
        $repository = $this->em->getRepository('AppBundle:Billet');
        $billets = $repository->findBy(array('dateVisite' => $day, 'statutTransaction' => true));
        $quantite = 0;
        foreach ($billets as $billet) {
            $quantite++;
        }
        return $quantite;
    }

    // Retourne le nombre de billets d'une commande donnée
    public function billetsCommande($id)
    {
        $commande = $this->getCommande($id);
        $quantite = 0;
        foreach ($commande->getBillets() as $billet) {
            $quantite++;
        }
        return $quantite;
    }

    // Marque les billets comme réservés une fois le paiement effectué
    public function changeStatutTransaction($id)
    {
        $commande = $this->getCommande($id);
        foreach ($commande->getBillets() as $billet)
        {
            $billet->setStatutTransaction(true);
            $this->saveBillet($billet);
        }
    }

    public function getSession($id)
    {
        $commande = $this->getCommande($id);
        return $commande->getSession();
    }
}
