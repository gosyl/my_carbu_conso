<?php

namespace Gosyl\MyCarbuConsoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MccRefEnergies
 *
 * @ORM\Table(name="mcc.REF_ENERGIES")
 * @ORM\Entity(repositoryClass="Gosyl\MyCarbuConsoBundle\Repository\MccRefEnergiesRepository")
 */
class MccRefEnergies
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mcc.REF_ENERGIES_SEQ", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="LIBELLE", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBELLE_COURT", type="string", length=10, nullable=false)
     */
    private $libelleCourt;



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
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     *
     * @return MccRefEnergies
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
     * @return MccRefEnergies
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
     * Set libelleCourt
     *
     * @param string $libelleCourt
     *
     * @return MccRefEnergies
     */
    public function setLibelleCourt($libelleCourt)
    {
        $this->libelleCourt = $libelleCourt;

        return $this;
    }

    /**
     * Get libelleCourt
     *
     * @return string
     */
    public function getLibelleCourt()
    {
        return $this->libelleCourt;
    }
}
