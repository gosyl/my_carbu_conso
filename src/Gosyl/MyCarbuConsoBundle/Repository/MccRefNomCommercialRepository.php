<?php

namespace Gosyl\MyCarbuConsoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MccRefNomCommercialRepository extends EntityRepository {
    public function getNomsCommerciauxByIdMarque($idMarque) {
        $aChamps = [
            'n.id',
            'n.libelle'
        ];

        $oQb = $this->createQueryBuilder('n');

        $oQb->select($aChamps)
            ->leftJoin('n.marque', 'marque')
            ->where($oQb->expr()->eq('marque.id', ':idMarque'))
            ->setParameter(':idMarque', $idMarque)
            ->orderBy('n.libelle')
        ;

        return $oQb->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getAllNomsCommerciaux() {
        $aChamps = array(
            'n.id',
            'n.libelle',
            'marque.id as idMarque',
            'marque.libelle as libelleMarque',
        );

        $oQb = $this->createQueryBuilder('n');

        $oQb->select($aChamps)
            ->leftJoin('n.marque', 'marque')
            ->where($oQb->expr()->isNull('n.dateSuppression'));

        return $oQb->getQuery()->getArrayResult();
    }
}
