<?php

namespace Gosyl\MyCarbuConsoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gosyl\CommonBundle\Entity\ParamUsers;

/**
 * MccModelesUsers
 *
 * @ORM\Table(name="mcc.MODELES_USERS", indexes={@ORM\Index(name="IDX_182E389BF8371B55", columns={"ID_USER"}), @ORM\Index(name="IDX_182E389B760603C3", columns={"ID_MODELE"})})
 * @ORM\Entity(repositoryClass="Gosyl\MyCarbuConsoBundle\Repository\MccModelesUsersRepository")
 */
class MccModelesUsers {
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mcc.MODELES_USERS_SEQ", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_SUPPRESSION", type="date", nullable=true)
     */
    private $dateSuppression;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM_VEHICULE", type="string", length=20, nullable=false)
     */
    private $nomVehicule;

    /**
     * @var ParamUsers
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\CommonBundle\Entity\ParamUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USER", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * @var MccRefModeles
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MODELE", referencedColumnName="ID")
     * })
     */
    private $modele;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_AJOUT", type="datetime", nullable=false)
     */
    private $dateAjout;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_MODIFICATION", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @var integer
     *
     * @ORM\Column(name="ANNEE_MISE_EN_CIRCULATION", type="integer", nullable=false)
     */
    private $anneeMiseEnCirculation;

    /**
     * @var integer
     *
     * @ORM\Column(name="KILOMETRAGE_INIT", type="integer", nullable=true)
     */
    private $kilometrageInit;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     *
     * @return MccModelesUsers
     */
    public function setDateSuppression($dateSuppression) {
        $this->dateSuppression = $dateSuppression;

        return $this;
    }

    /**
     * Get dateSuppression
     *
     * @return \DateTime
     */
    public function getDateSuppression() {
        return $this->dateSuppression;
    }

    /**
     * Set nomVehicule
     *
     * @param string $nomVehicule
     *
     * @return MccModelesUsers
     */
    public function setNomVehicule($nomVehicule) {
        $this->nomVehicule = $nomVehicule;

        return $this;
    }

    /**
     * Get nomVehicule
     *
     * @return string
     */
    public function getNomVehicule() {
        return $this->nomVehicule;
    }

    /**
     * @return ParamUsers
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param ParamUsers $user
     * @return MccModelesUsers
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return MccRefModeles
     */
    public function getModele() {
        return $this->modele;
    }

    /**
     * @param MccRefModeles $modele
     * @return MccModelesUsers
     */
    public function setModele(MccRefModeles $modele) {
        $this->modele = $modele;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAjout() {
        return $this->dateAjout;
    }

    /**
     * @param \DateTime $dateAjout
     * @return MccModelesUsers
     */
    public function setDateAjout($dateAjout) {
        $this->dateAjout = $dateAjout;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateModification() {
        return $this->dateModification;
    }

    /**
     * @param \DateTime $dateModification
     * @return MccModelesUsers
     */
    public function setDateModification($dateModification) {
        $this->dateModification = $dateModification;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnneeMiseEnCirculation() {
        return $this->anneeMiseEnCirculation;
    }

    /**
     * @param int $anneeMiseEnCirculation
     * @return MccModelesUsers
     */
    public function setAnneeMiseEnCirculation($anneeMiseEnCirculation) {
        $this->anneeMiseEnCirculation = $anneeMiseEnCirculation;
        return $this;
    }

    /**
     * @return int
     */
    public function getKilometrageInit() {
        return $this->kilometrageInit;
    }

    /**
     * @param int $kilometrageInit
     * @return MccModelesUsers
     */
    public function setKilometrageInit($kilometrageInit) {
        $this->kilometrageInit = $kilometrageInit;
        return $this;
    }
}
