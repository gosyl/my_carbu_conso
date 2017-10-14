<?php

namespace Gosyl\MyCarbuConsoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MccRefNomCommercial
 *
 * @ORM\Table(name="mcc.REF_NOM_COMMERCIAL", indexes={@ORM\Index(name="IDX_CC29A903F7270350", columns={"ID_MARQUE"})})
 * @ORM\Entity(repositoryClass="Gosyl\MyCarbuConsoBundle\Repository\MccRefNomCommercialRepository")
 */
class MccRefNomCommercial
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mcc.REF_NOM_COMMERCIAL_SEQ", allocationSize=1, initialValue=1)
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
     * @var \Gosyl\MyCarbuConsoBundle\Entity\MccRefMarques
     *
     * @ORM\ManyToOne(targetEntity="Gosyl\MyCarbuConsoBundle\Entity\MccRefMarques")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MARQUE", referencedColumnName="ID")
     * })
     */
    private $marque;



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
     * @return MccRefNomCommercial
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
     * @return MccRefNomCommercial
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
     * @return MccRefMarques
     */
    public function getMarque() {
        return $this->marque;
    }

    /**
     * @param MccRefMarques $marque
     * @return MccRefNomCommercial
     */
    public function setMarque($marque) {
        $this->marque = $marque;
        return $this;
    }
}
