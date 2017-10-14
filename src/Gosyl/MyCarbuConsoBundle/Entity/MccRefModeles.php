<?php

namespace Gosyl\MyCarbuConsoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MccRefModeles
 *
 * @ORM\Table(name="mcc.REF_MODELES", indexes={@ORM\Index(name="IDX_371EE66E44A81553", columns={"ID_BOITE_VITESSE"}), @ORM\Index(name="IDX_371EE66EDBEF185D", columns={"ID_CARROSSERIE"}), @ORM\Index(name="IDX_371EE66EEF30CB9", columns={"ID_ENERGIE"}), @ORM\Index(name="IDX_371EE66E1E5272B2", columns={"ID_NOM_COMMERCIAL"}), @ORM\Index(name="IDX_371EE66E3FDE569C", columns={"ID_PUISSANCE_FISCALE"})})
 * @ORM\Entity(repositoryClass="Gosyl\MyCarbuConsoBundle\Repository\MccRefModelesRepository")
 */
class MccRefModeles
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mcc.REF_MODELES_SEQ", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ANNEE_MODELE", type="integer", nullable=true)
     */
    private $anneeModele;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_SUPPRESSION", type="date", nullable=true)
     */
    private $dateSuppression;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBELLE", type="string", length=2000, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE_MINE", type="string", length=1000, nullable=true)
     */
    private $typeMine;

    /**
     * @var MccRefBoiteVitesse
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefBoiteVitesse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BOITE_VITESSE", referencedColumnName="ID")
     * })
     */
    private $boiteVitesse;

    /**
     * @var MccRefCarrosserie
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefCarrosserie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CARROSSERIE", referencedColumnName="ID")
     * })
     */
    private $carrosserie;

    /**
     * @var MccRefEnergies
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefEnergies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ENERGIE", referencedColumnName="ID")
     * })
     */
    private $energie;

    /**
     * @var MccRefNomCommercial
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefNomCommercial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_NOM_COMMERCIAL", referencedColumnName="ID")
     * })
     */
    private $nomCommercial;

    /**
     * @var MccRefPuissanceFiscale
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefPuissanceFiscale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PUISSANCE_FISCALE", referencedColumnName="ID")
     * })
     */
    private $puissanceFiscale;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set anneeModele
     *
     * @param integer $anneeModele
     *
     * @return MccRefModeles
     */
    public function setAnneeModele($anneeModele)
    {
        $this->anneeModele = $anneeModele;

        return $this;
    }

    /**
     * Get anneeModele
     *
     * @return integer
     */
    public function getAnneeModele()
    {
        return $this->anneeModele;
    }

    /**
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     *
     * @return MccRefModeles
     */
    public function setDateSuppression($dateSuppression)
    {
        $this->dateSuppression = $dateSuppression;

        return $this;
    }

    /**
     * Get dateSuppression
     *
     * @return \DateTime
     */
    public function getDateSuppression()
    {
        return $this->dateSuppression;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return MccRefModeles
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
     * Set typeMine
     *
     * @param string $typeMine
     *
     * @return MccRefModeles
     */
    public function setTypeMine($typeMine)
    {
        $this->typeMine = $typeMine;

        return $this;
    }

    /**
     * Get typeMime
     *
     * @return string
     */
    public function getTypeMine()
    {
        return $this->typeMine;
    }

    /**
     * @return MccRefBoiteVitesse
     */
    public function getBoiteVitesse() {
        return $this->boiteVitesse;
    }

    /**
     * @param MccRefBoiteVitesse $boiteVitesse
     * @return MccRefModeles
     */
    public function setBoiteVitesse($boiteVitesse) {
        $this->boiteVitesse = $boiteVitesse;
        return $this;
    }

    /**
     * @return MccRefCarrosserie
     */
    public function getCarrosserie() {
        return $this->carrosserie;
    }

    /**
     * @param MccRefCarrosserie $carrosserie
     * @return MccRefModeles
     */
    public function setCarrosserie($carrosserie) {
        $this->carrosserie = $carrosserie;
        return $this;
    }

    /**
     * @return MccRefEnergies
     */
    public function getEnergie() {
        return $this->energie;
    }

    /**
     * @param MccRefEnergies $energie
     * @return MccRefModeles
     */
    public function setEnergie($energie) {
        $this->energie = $energie;
        return $this;
    }

    /**
     * @return MccRefNomCommercial
     */
    public function getNomCommercial() {
        return $this->nomCommercial;
    }

    /**
     * @param MccRefNomCommercial $nomCommercial
     * @return MccRefModeles
     */
    public function setNomCommercial($nomCommercial) {
        $this->nomCommercial = $nomCommercial;
        return $this;
    }

    /**
     * @return MccRefPuissanceFiscale
     */
    public function getPuissanceFiscale() {
        return $this->puissanceFiscale;
    }

    /**
     * @param MccRefPuissanceFiscale $puissanceFiscale
     * @return MccRefModeles
     */
    public function setPuissanceFiscale($puissanceFiscale) {
        $this->puissanceFiscale = $puissanceFiscale;
        return $this;
    }
}
