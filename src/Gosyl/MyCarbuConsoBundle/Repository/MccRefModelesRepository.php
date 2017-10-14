<?php

namespace Gosyl\MyCarbuConsoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MccRefModelesRepository extends EntityRepository {
    public function getNbModeles() {
        $oQb = $this->createQueryBuilder('m');
        $oQb->select('count(m)');

        return $oQb->getQuery()->getOneOrNullResult();
    }

    public function getAllModeles() {
        $aChamps = array(
            'm.id',
            'm.typeMine',
            'com.id as idNomCommercial',
            'com.libelle as libelleNomCommercial',
            'marque.id as idMarque',
            'marque.libelle as libelleMarque',
            'm.libelle'
        );

        $oQb = $this->createQueryBuilder('m');
        $oQb->select($aChamps)
            ->leftJoin('m.nomCommercial', 'com')
            ->leftJoin('com.marque', 'marque')
            ->where($oQb->expr()->isNull('m.dateSuppression'));

        return $oQb->getQuery()->getArrayResult();
    }

    public function getAllModelesForUpdate() {
        $aChamps = [
            'mod.id as idModele',
            'mod.libelle as libelleModele',
            'mod.typeMine',
            'nomCommercial.id as idNomCommercial',
            'nomCommercial.libelle as libelleNomCommercial',
            'marque.id as idMarque',
            'marque.libelle as libelleMarque'
        ];

        $oQb = $this->createQueryBuilder('mod');

        $oQb->select($aChamps)
            ->leftJoin('mod.nomCommercial', 'nomCommercial')
            ->leftJoin('nomCommercial.marque', 'marque')
        ;

        return $oQb->getQuery()->getArrayResult();
    }

    public function getEnergiesByNomCommercial($idNomCommercial) {
        /*
        select distinct e.id, e.libelle
        from MCC_REF_MODELES modeles
        left join MCC_REF_NOM_COMMERCIAL nom on nom.id = modeles.ID_NOM_COMMERCIAL
        left join MCC_REF_ENERGIES e on e.id = modeles.ID_ENERGIE
        where nom.ID = 1091
        order by e.libelle asc;*/

        $aChamp = [
            'e.id',
            'e.libelle'
        ];

        $oQb = $this->createQueryBuilder('modele');

        $oQb->select($aChamp)
            ->leftJoin('modele.nomCommercial', 'nom')
            ->leftJoin('modele.energie', 'e')
            ->where($oQb->expr()->eq('nom.id', ':idNom'))
            ->distinct()
            ->orderBy('e.libelle')
            ->setParameter(':idNom', $idNomCommercial);

        return $oQb->getQuery()->getArrayResult();
    }

    public function getBoiteVitesseByNomCommercialAndEnergie($nomCommercial, $energie) {
        $aChamp = [
            'b.id',
            'b.libelle'
        ];

        $oQb = $this->createQueryBuilder('modele');

        $oQb->select($aChamp)
            ->leftJoin('modele.nomCommercial', 'nom')
            ->leftJoin('modele.energie', 'e')
            ->leftJoin('modele.boiteVitesse', 'b')
            ->where($oQb->expr()->eq('nom.id', ':idNom'))
            ->andWhere($oQb->expr()->eq('e.id', ':idEnergie'))
            ->distinct()
            ->orderBy('b.libelle')
            ->setParameter(':idNom', $nomCommercial)
            ->setParameter(':idEnergie', $energie);

        return $oQb->getQuery()->getArrayResult();
    }

    public function getCarrosserie($nomCommercial, $energie, $boiteVitesse) {
        $aChamp = [
            'c.id',
            'c.libelle'
        ];

        $oQb = $this->createQueryBuilder('modele');

        $oQb->select($aChamp)
            ->leftJoin('modele.nomCommercial', 'nom')
            ->leftJoin('modele.energie', 'e')
            ->leftJoin('modele.boiteVitesse', 'b')
            ->leftJoin('modele.carrosserie', 'c')
            ->where($oQb->expr()->eq('nom.id', ':idNom'))
            ->andWhere($oQb->expr()->eq('e.id', ':idEnergie'))
            ->andWhere($oQb->expr()->eq('b.id', ':idBoite'))
            ->distinct()
            ->orderBy('c.libelle')
            ->setParameter(':idNom', $nomCommercial)
            ->setParameter(':idEnergie', $energie)
            ->setParameter(':idBoite', $boiteVitesse);
        return $oQb->getQuery()->getArrayResult();
    }

    public function getPuissanceFiscale($nomCommercial, $energie, $boiteVitesse, $carrosserie) {
        $aChamp = [
            'p.id',
            'p.libelle'
        ];

        $oQb = $this->createQueryBuilder('modele');

        $oQb->select($aChamp)
            ->leftJoin('modele.nomCommercial', 'nom')
            ->leftJoin('modele.energie', 'e')
            ->leftJoin('modele.boiteVitesse', 'b')
            ->leftJoin('modele.carrosserie', 'c')
            ->leftJoin('modele.puissanceFiscale', 'p')
            ->where($oQb->expr()->eq('nom.id', ':idNom'))
            ->andWhere($oQb->expr()->eq('e.id', ':idEnergie'))
            ->andWhere($oQb->expr()->eq('b.id', ':idBoite'))
            ->andWhere($oQb->expr()->eq('c.id', ':idCarrosserie'))
            ->distinct()
            ->orderBy('c.libelle')
            ->setParameter(':idNom', $nomCommercial)
            ->setParameter(':idEnergie', $energie)
            ->setParameter(':idBoite', $boiteVitesse)
            ->setParameter(':idCarrosserie', $carrosserie);
        return $oQb->getQuery()->getArrayResult();
    }

    public function getModelesByMultiCriteres($anneeModele, $idNomCommercial, $idBoiteVitesse, $idCarrosserie, $idEnergie, $idPuissanceFiscale) {
        $aChamps = [
            'mod.id',
            'mod.libelle',
            'mod.anneeModele',
            'mod.typeMine',
            'com.libelle as libelleNomCommercial'
        ];

        $oQb = $this->createQueryBuilder('mod');
        $oQb->select($aChamps)
            ->leftJoin('mod.nomCommercial', 'com')
            ->leftJoin('mod.boiteVitesse', 'boite')
            ->leftJoin('mod.carrosserie', 'carrosserie')
            ->leftJoin('mod.energie', 'energie')
            ->leftJoin('mod.puissanceFiscale', 'pf')
            ->where($oQb->expr()->eq('com.id', ':idNomCommercial'))
            ->andWhere($oQb->expr()->eq('boite.id', ':idBoite'))
            ->andWhere($oQb->expr()->eq('carrosserie.id', ':idCarrosserie'))
            ->andWhere($oQb->expr()->eq('energie.id', ':idEnergie'))
            ->andWhere($oQb->expr()->eq('pf.id', ':idPuissance'))
            ->andWhere($oQb->expr()->lte('mod.anneeModele',':annee'))
            ->andWhere($oQb->expr()->isNull('mod.dateSuppression'))
            ->orderBy('mod.libelle')
            ->addOrderBy('mod.typeMine')
            ->setParameters([
                ':idNomCommercial' => $idNomCommercial,
                ':idBoite' => $idBoiteVitesse,
                ':idCarrosserie' => $idCarrosserie,
                ':idEnergie' => $idEnergie,
                ':idPuissance' => $idPuissanceFiscale,
                ':annee' => $anneeModele
            ]);

        return $oQb->getQuery()->getArrayResult();
    }

    public function getModelesByTypeMines($typeMine) {
        $aChamps = [
            'mod.id',
            'mod.libelle',
            'mod.anneeModele',
            'mod.typeMine',
            'com.libelle as libelleNomCommercial'
        ];

        $oQb = $this->createQueryBuilder('mod');
        $oQb->select($aChamps)
            ->leftJoin('mod.nomCommercial', 'com')
            ->where($oQb->expr()->like('mod.typeMine', ':typeMine'))
            ->orderBy('mod.libelle')
            ->addOrderBy('mod.typeMine')
            ->setParameters([
                ':typeMine' => $typeMine
            ]);

        return $oQb->getQuery()->getArrayResult();
    }
}
