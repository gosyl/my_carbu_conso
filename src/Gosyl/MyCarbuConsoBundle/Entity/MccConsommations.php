<?php

namespace Gosyl\MyCarbuConsoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MccConsommations
 *
 * @ORM\Table(name="mcc.CONSOMMATIONS", indexes={@ORM\Index(name="IDX_21AFC16427CBD4C1", columns={"ID_MODELE_USER"})})
 * @ORM\Entity(repositoryClass="Gosyl\MyCarbuConsoBundle\Repository\MccConsommationsRepository")
 */
class MccConsommations {
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mcc.CONSOMMATIONS_SEQ", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRESSE", type="text", nullable=true)
     */
    private $adresse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_PLEIN", type="date", nullable=false)
     */
    private $datePlein;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_SUPPRESSION", type="date", nullable=true)
     */
    private $dateSuppression;

    /**
     * @var float
     *
     * @ORM\Column(name="DISTANCE", type="float", precision=10, scale=2, nullable=false)
     */
    private $distance;

    /**
     * @var float
     *
     * @ORM\Column(name="PRIX", type="float", precision=10, scale=2, nullable=false)
     */
    private $prix;

    /**
     * @var float
     *
     * @ORM\Column(name="QUANTITE", type="float", precision=10, scale=2, nullable=false)
     */
    private $quantite;

    /**
     * @var float
     *
     * @ORM\Column(name="KILOMETRAGE_COMPTEUR", type="float", precision=10, scale=2, nullable=false)
     */
    private $kilometrageCompteur;

    /**
     * @var MccModelesUsers
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MODELE_USER", referencedColumnName="ID")
     * })
     */
    private $modeleUser;

    /**
     * @var float
     * @ORM\Column(name="GPS_LAT", type="float", nullable=true)
     */
    private $gpsLat;

    /**
     * @var float
     *
     * @ORM\Column(name="GPS_LONG", type="float", nullable=true)
     */
    private $gpsLong;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return MccConsommations
     */
    public function setAdresse($adresse) {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse() {
        return $this->adresse;
    }

    /**
     * Set datePlein
     *
     * @param \DateTime $datePlein
     *
     * @return MccConsommations
     */
    public function setDatePlein($datePlein) {
        $this->datePlein = $datePlein;

        return $this;
    }

    /**
     * Get datePlein
     *
     * @return \DateTime
     */
    public function getDatePlein() {
        return $this->datePlein;
    }

    /**
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     *
     * @return MccConsommations
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
     * Set distance
     *
     * @param string $distance
     *
     * @return MccConsommations
     */
    public function setDistance($distance) {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance() {
        return $this->distance;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return MccConsommations
     */
    public function setPrix($prix) {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix() {
        return $this->prix;
    }

    /**
     * Set quantite
     *
     * @param string $quantite
     *
     * @return MccConsommations
     */
    public function setQuantite($quantite) {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return string
     */
    public function getQuantite() {
        return $this->quantite;
    }

    /**
     * Set modeleUser
     *
     * @param MccModelesUsers $modeleUser
     *
     * @return MccConsommations
     */
    public function setModeleUser(MccModelesUsers $modeleUser = null) {
        $this->modeleUser = $modeleUser;

        return $this;
    }

    /**
     * Get modeleUser
     *
     * @return MccModelesUsers
     */
    public function getModeleUser() {
        return $this->modeleUser;
    }

    /**
     * @return float
     */
    public function getKilometrageCompteur() {
        return $this->kilometrageCompteur;
    }

    /**
     * @param float $kilometrageCompteur
     * @return MccConsommations
     */
    public function setKilometrageCompteur($kilometrageCompteur) {
        $this->kilometrageCompteur = $kilometrageCompteur;
        return $this;
    }

    /**
     * @return float
     */
    public function getGpsLat() {
        return $this->gpsLat;
    }

    /**
     * @param float $gpsLat
     * @return MccConsommations
     */
    public function setGpsLat($gpsLat) {
        $this->gpsLat = $gpsLat;
        return $this;
    }

    /**
     * @return float
     */
    public function getGpsLong() {
        return $this->gpsLong;
    }

    /**
     * @param float $gpsLong
     * @return MccConsommations
     */
    public function setGpsLong($gpsLong) {
        $this->gpsLong = $gpsLong;
        return $this;
    }
}
