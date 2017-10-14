<?php

namespace Gosyl\MyCarbuConsoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gosyl\CommonBundle\Entity\ParamUsers;

class MccModelesUsersRepository extends EntityRepository {
    public function getAllModelesByUser(ParamUsers $oUser) {
        $aChamps = [
            'mmu.id as idModeleUser',
            'mmu.nomVehicule',
            'mmu.anneeMiseEnCirculation',
            'mmu.kilometrageInit',
            'marque.libelle as libelleMarque',
            'marque.id as idMarque',
            'nomCommercial.libelle as libelleNomCommercial',
            'nomCommercial.id as idNomCommercial',
            'modele.libelle as libelleModele',
            'modele.typeMine',
            'modele.anneeModele',
            'energie.libelle as libelleEnergie',
            'energie.id as idEnergie',
            'boite.id as idBoite',
            'carrosserie.id as idCarrosserie',
            'puissance.id as idPuissance'
        ];

        $oQb = $this->createQueryBuilder('mmu');

        $oQb->select($aChamps)
            ->leftJoin('mmu.modele', 'modele')
            ->leftJoin('modele.nomCommercial', 'nomCommercial')
            ->leftJoin('nomCommercial.marque', 'marque')
            ->leftJoin('modele.energie', 'energie')
            ->leftJoin('modele.boiteVitesse', 'boite')
            ->leftJoin('modele.carrosserie', 'carrosserie')
            ->leftJoin('modele.puissanceFiscale', 'puissance')
            ->where($oQb->expr()->eq('mmu.user', ':user'))
            ->andWhere($oQb->expr()->isNull('mmu.dateSuppression'))
            ->setParameter(':user', $oUser)
        ;

        return $oQb->getQuery()->getArrayResult();
    }
}
