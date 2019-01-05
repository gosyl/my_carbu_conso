<?php

namespace Gosyl\MyCarbuConsoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MccRefMarquesRepository extends EntityRepository {

    /**
     * Récupère toutes les marques
     *
     * @return array
     */
    public function getAllMarques() {
        $oQb = $this->createQueryBuilder('m');

        $oQb->select()
            ->where($oQb->expr()->isNull('m.dateSuppression'));

        return $oQb->getQuery()->getArrayResult();
    }

    /**
     * Retourne le nombre de marque
     *
     * @return mixed
     */
    public function getNbMarques() {
        $oQb = $this->createQueryBuilder('m');

        $oQb->select('count(m)');

        return $oQb->getQuery()->getOneOrNullResult();
    }

    /**
     * Pour Autocomplete Retourne une liste de marque avec en paramètre un libelle partiel
     *
     * @param string $partialLibelle
     *
     * @return array
     */
    public function getMarquesByPartialLibelle($partialLibelle) {
        $aChamps = [
            'm.id',
            'm.libelle'
        ];

        $oQb = $this->createQueryBuilder('m');
        $oQb->select($aChamps)
            ->where($oQb->expr()->like('m.libelle', ':query'))
            ->setParameter(':query', $partialLibelle . '%')
        ;

        return $oQb->getQuery()->getArrayResult();
    }
}
