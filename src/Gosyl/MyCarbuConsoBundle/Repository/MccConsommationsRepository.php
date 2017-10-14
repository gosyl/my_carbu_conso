<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 08/07/17
 * Time: 10:34
 */

namespace Gosyl\MyCarbuConsoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers;

class MccConsommationsRepository extends EntityRepository {
    public function getAllConsommationsByModeleUser(MccModelesUsers $modeleUser, $aParams = null) {
        if(!is_null($aParams)) {
            $oDateDebut = $aParams['dateDebut'];
            $oDateFin = $aParams['dateFin'];
        } else {
            $oDateDebut = new \DateTime();
            $oDateDebut->setDate(date('Y'),1,1);
            $oDateDebut->setTime(0,0,0);
            $oDateFin = new \DateTime();
        }
        $oDateFin->add(new \DateInterval('P1D'));
        $oDateFin->setTime(0, 0, 0);

        $aChamps = [
            'conso.id as idConso',
            'conso.quantite',
            'conso.prix',
            'conso.datePlein',
            'conso.distance',
            'conso.adresse',
            'conso.kilometrageCompteur',
            'conso.gpsLat',
            'conso.gpsLong'
        ];

        $oQb = $this->createQueryBuilder('conso');
        $oQb->select($aChamps)
            ->leftJoin('conso.modeleUser', 'vl')
            ->where($oQb->expr()->eq('conso.modeleUser', ':vl'))
            ->andWhere($oQb->expr()->gte('conso.datePlein', ':dateDebut'))
            ->andWhere($oQb->expr()->lte('conso.datePlein', ':dateActuelle'))
            ->andWhere($oQb->expr()->isNull('conso.dateSuppression'))
            ->setParameter(':vl', $modeleUser)
            ->setParameter(':dateDebut', $oDateDebut)
            ->setParameter(':dateActuelle', $oDateFin)
            ->orderBy('conso.datePlein');

        $oNewDateFin = clone $oDateFin;
        $oNewDateFin->sub(new \DateInterval('P1D'));

        return array('results' => $oQb->getQuery()->getArrayResult(), 'dateDebut' => $oDateDebut->format('d/m/Y'), 'dateFin' => $oNewDateFin->format('d/m/Y'));
    }
}