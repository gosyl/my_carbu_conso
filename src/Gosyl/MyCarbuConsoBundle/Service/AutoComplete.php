<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 12/07/17
 * Time: 21:05
 */

namespace Gosyl\MyCarbuConsoBundle\Service;


use Doctrine\ORM\EntityManager;

class AutoComplete {

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getMarque($query) {
        $oMdlMccRefMarques = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefMarques');
        $aResults = $oMdlMccRefMarques->getMarquesByPartialLibelle($query);

        return $aResults;
    }

    public function getNomsCommerciaux($query) {
        $oMdlMccRefNomCommercial = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefNomCommercial');
        $aResults = $oMdlMccRefNomCommercial->getNomsCommerciauxByIdMarque($query);

        return $aResults;
    }

    public function getEnergie($idNomCommercial) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        return $oMdlMccRefModeles->getEnergiesByNomCommercial($idNomCommercial);
    }

    public function getBoiteVitesse($nomCommercial, $energie) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        return $oMdlMccRefModeles->getBoiteVitesseByNomCommercialAndEnergie($nomCommercial, $energie);
    }

    public function getCarrosserie($nomCommercial, $energie, $boiteVitesse) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        return $oMdlMccRefModeles->getCarrosserie($nomCommercial, $energie, $boiteVitesse);
    }

    public function getPuissanceFiscale($nomCommercial, $energie, $boiteVitesse, $carrosserie) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        return $oMdlMccRefModeles->getPuissanceFiscale($nomCommercial, $energie, $boiteVitesse, $carrosserie);
    }
}