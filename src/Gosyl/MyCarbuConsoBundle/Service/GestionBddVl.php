<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 07/07/17
 * Time: 14:23
 */

namespace Gosyl\MyCarbuConsoBundle\Service;


use Doctrine\ORM\EntityManager;
use Gosyl\CommonBundle\Entity\ParamUsers;
use Gosyl\MyCarbuConsoBundle\Constantes;
use Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefBoiteVitesse;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefCarrosserie;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefEnergies;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefMarques;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefModeles;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefNomCommercial;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefPuissanceFiscale;
use Gosyl\MyCarbuConsoBundle\Repository\MccRefMarquesRepository;
use Gosyl\MyCarbuConsoBundle\Repository\MccRefModelesRepository;
use Goutte\Client;

class GestionBddVl {
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var null | Client
     */
    protected $oClient = null;

    /**
     * @var array
     */
    protected $aRef = [];

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * Retourne le nombre de marque et de véhicule présent en base
     *
     * @return array
     */
    public function getNbElementsInBase() {
        $aReturn = [];

        /**
         * @var MccRefMarquesRepository
         */
        $oRepRefMarques = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefMarques');
        $aReturn['nbMarques'] = $oRepRefMarques->getNbMarques()[1];

        /**
         * @var MccRefModelesRepository
         */
        $oRepRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        $aReturn['nbModeles'] = $oRepRefModeles->getNbModeles()[1];
        return $aReturn;
    }

    /**
     * Mise à jour du référentiel des véhicules (Modèles, Marques, Nom commerciaux, Puissance fiscale, Énergie, Boite de vitesse...)
     */
    public function refresh() {
        if(is_null($this->oClient)) {
            $this->oClient = new Client();
        }

        $this->getMarquesFromSource();
    }

    /**
     * @param ParamUsers $oUser
     * @param bool $bAccueil
     * @return array
     */
    public function getVehiculeByUser(ParamUsers $oUser, $bAccueil = false) {
        $aReturn = [];

        $oMdlMccModelesUser = $this->em->getRepository('GosylMyCarbuConsoBundle:MccModelesUsers');
        $aResults = $oMdlMccModelesUser->getAllModelesByUser($oUser);

        $aCols = [
            'id' => [
                'title' => 'id',
                'data' => 'idModeleUser',
                'visible' => false
            ],
            'idMarque' => [
                'title' => 'idMarque',
                'data' => 'idMarque',
                'visible' => false
            ],
            'nomVehicule' => [
                'title' => 'Nom du véhicule',
                'data' => 'nomVehicule'
            ],
            'libelleMarque' => [
                'title' => 'Marque',
                'data' => 'libelleMarque'
            ],
            'libelleNomCommercial' => [
                'title' => 'Nom commercial',
                'data' => 'libelleNomCommercial'
            ],
            'libelleModele' => [
                'title' => 'Modèle',
                'data' => 'libelleModele',
                'width' => '150px'
            ],
            'typeMine' => [
                'title' => 'Type mine',
                'data' => 'typeMine'
            ],
            'libelleEnergie' => [
                'title' => 'Carburant',
                'data' => 'libelleEnergie'
            ]
        ];

        if($bAccueil) {
            $aCols['action'] = [
                'title' => 'action',
                'data' => 'null',
                'orderable' => false,
                'render' => [
                    'render' => 'function(oObject, type, row, meta) {
                        var contenu = Gosyl.MyCarbuConso.Default.Index.addBtnActionsVehicule(oObject);
                        return contenu;
                    }'
                ]
            ];
        } else {
            $aCols['action'] = [
                'title' => 'action',
                'data' => 'null',
                'orderable' => false,
                'render' => [
                    'render' => 'function(oObject, type, row, meta) {
                        var contenu = Gosyl.MyCarbuConso.Utilisateurs.Profil.addBtnActionsVehicule(oObject);
                        return contenu;
                    }'
                ]
            ];
        }

        $aOptions = [
            'responsive' => true,
            'paging' => false,
            'autoWidth' => false,
            'stateSave' => true,
            'retrieve' => true,
            'searching' => false,
            'info' => false,
            'sort' => false,
            'lengthChange' => false,
            //'pageLength' => 10,
            //'pagingType' => 'full_numbers',
            'dom' => '<"H"RCTlf>t<"F"rpi>',
            'createdRow' => [
                'function' => 'function(row, data, dataIndex) {
                $(row).data(\'vehicule\', data);
                $(row).attr(\'id\', \'data_\' + data.idModeleUser);
            }']
        ];

        $aReturn['options'] = array_merge($aOptions, \Gosyl\CommonBundle\Constantes::$aDataTableLanguage);
        $aReturn['cols'] = $aCols;
        $aReturn['results'] = ['data' => $aResults];

        return $aReturn;
    }

    /**
     * Récupération des différentes marques et insertion en base des nouveautés
     */
    protected function getMarquesFromSource() {
        set_time_limit(0);
        gc_disable();
        //$aAllMarques = [];

        // obtention des marques
        $this->oClient->setServerParameters([
            'timeout' => 20,
            'keepalive' => false,
            'useragent' => Constantes::USER_AGENT,
            'HTTP_USER_AGENT' => Constantes::USER_AGENT
        ]);

        $this->oClient->request('get', Constantes::URL);

        $oJsonMarques = json_decode($this->oClient->getResponse()->getContent());//dump($oJsonMarques);

        if(!$oJsonMarques->Success) {
            throw new \Exception('Erreur lors de la récupération des marques');
        }

        // Récupération des modèles existants
        $aAllMarques = $this->getMarques();
        $aAllMarques = $this->getNomsCommerciaux($aAllMarques);
        $aAllModeles = $this->getModeles($aAllMarques);

        foreach($oJsonMarques->Datas->Marques as $oJsonMarque) {
            $libelleMarque = $oJsonMarque->Name;
            $idMarque = $oJsonMarque->Id;

            $aMarquesKeys = array_keys($aAllModeles);

            if(!in_array($idMarque, $aMarquesKeys)) {
                // La marque n'existe pas en base la crée
                $this->em->beginTransaction();

                try {
                    $oRefMarques = new MccRefMarques();
                    $oRefMarques->setLibelle($libelleMarque);
                    $this->em->persist($oRefMarques);
                    $this->em->flush();
                    $this->em->commit();

                    $aAllModeles = array_merge($aAllModeles, [$libelleMarque => ['id' => $oRefMarques->getId(), 'libelle' => $libelleMarque, 'nomCommerciaux' => []]]);

                    // Récupération des noms commerciaux de la source
                    $this->oClient->request('get', Constantes::URL . '?marque=' . $idMarque);
                    $oJsonNomsCommerciaux = json_decode($this->oClient->getResponse()->getContent());

                    foreach($oJsonNomsCommerciaux->Datas->NomsCommerciaux as $oJsonNomCommercial) {
                        $libelleNomCommercial = $oJsonNomCommercial->Name;
                        $idNomCommercial = $oJsonNomCommercial->Id;

                        $this->em->beginTransaction();
                        try {
                            $oRefNomCommercial = new MccRefNomCommercial();
                            $oRefNomCommercial->setLibelle($libelleNomCommercial);
                            $oRefNomCommercial->setMarque($oRefMarques);
                            $this->em->persist($oRefNomCommercial);
                            $this->em->flush();
                            $this->em->commit();

                            $aAllModeles[$libelleMarque]['nomCommerciaux'] = array_merge($aAllModeles[$libelleMarque]['nomCommerciaux'], [
                                $libelleMarque . '_' . $libelleNomCommercial => [
                                    'id' => $idNomCommercial,
                                    'libelle' => $libelleNomCommercial,
                                    'modeles' => []
                                ]
                            ]);

                            // Pour chaque nom Commercial on récupère tous les modeles
                            $this->oClient->request('get', Constantes::URL . '?marque=' . $idMarque . '&nomCommercial=' . $idNomCommercial);
                            $oJsonModeles = json_decode($this->oClient->getResponse()->getContent());

                            // Gestion du référentiel
                            $this->gereReferentiels($oJsonModeles->Datas->Energies, 'Energies');
                            $this->gereReferentiels($oJsonModeles->Datas->BoiteVitesses, 'BoiteVitesse');
                            $this->gereReferentiels($oJsonModeles->Datas->Carrosseries, 'Carrosserie');
                            $this->gereReferentiels($oJsonModeles->Datas->PuissanceFiscales, 'PuissanceFiscale');

                            foreach($oJsonModeles->Datas->Modeles as $oJsonModele) {
                                $this->em->beginTransaction();
                                try {
                                    $oRefModele = new MccRefModeles();
                                    $oRefModele->setLibelle($oJsonModele->Modele)
                                        ->setAnneeModele($oJsonModele->AnneeModele)
                                        ->setBoiteVitesse($this->aRef['BoiteVitesse'][$oJsonModele->IdBoiteVitesse])
                                        ->setCarrosserie($this->aRef['Carrosserie'][$oJsonModele->IdCarrosserie])
                                        ->setEnergie($this->aRef['Energies'][$oJsonModele->IdEnergie])
                                        ->setNomCommercial($oRefNomCommercial)
                                        ->setPuissanceFiscale($this->aRef['PuissanceFiscale'][$oJsonModele->IdPuissanceFiscale])
                                        ->setTypeMine($oJsonModele->TypeMine);

                                    $this->em->persist($oRefModele);
                                    $this->em->flush();
                                    $this->em->commit();

                                    $aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['modeles'] = array_merge(
                                        $aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['modeles'],
                                        [
                                            $oJsonModele->Modele . '_' . $oJsonModele->TypeMine => [
                                                'id' => $oRefModele->getId(),
                                                'libelle' => $oRefModele->getLibelle(),
                                                'typeMine' => $oRefModele->getTypeMine()
                                            ]
                                        ]
                                        );
                                } catch(\Exception $e) {
                                    $this->em->rollback();
                                    throw $e;
                                }
                            }
                        } catch(\Exception $e) {
                            $this->em->rollback();
                            throw $e;
                        }
                    }
                } catch(\Exception $e) {
                    $this->em->rollback();
                    throw $e;
                }
            } else {
                // la marque existe

                // Récupération des noms commerciaux
                $this->oClient->request('get', Constantes::URL . '?marque=' . $idMarque);
                $oJsonNomsCommerciaux = json_decode($this->oClient->getResponse()->getContent());

                $oRefMarques = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefMarques')->findOneBy(['id' => $aAllModeles[$libelleMarque]['id']]);

                foreach($oJsonNomsCommerciaux->Datas->NomsCommerciaux as $oJsonNomCommercial) {
                    $libelleNomCommercial = $oJsonNomCommercial->Name;
                    $idNomCommercial = $oJsonNomCommercial->Id;

                    $aNomCommercialKeys = array_keys($aAllModeles[$libelleMarque]['nomCommerciaux']);

                    //if(!array_key_exists($libelleMarque . '_' . $libelleNomCommercial, $aAllModeles[$libelleMarque]['nomCommerciaux'])) {
                    if(!in_array($libelleMarque . '_' . $libelleNomCommercial, $aNomCommercialKeys)) {
                        // Le nom commercial n'existe pas, le crée
                        $this->em->beginTransaction();
                        try {
                            $oRefNomCommercial = new MccRefNomCommercial();
                            $oRefNomCommercial->setLibelle($libelleNomCommercial);
                            $oRefNomCommercial->setMarque($oRefMarques);
                            $this->em->persist($oRefNomCommercial);
                            $this->em->flush();
                            $this->em->commit();

                            $aAllModeles[$libelleMarque]['nomCommerciaux'] = array_merge($aAllModeles[$libelleMarque]['nomCommerciaux'], [
                                $libelleMarque . '_' . $libelleNomCommercial => [
                                    'id' => $idNomCommercial,
                                    'libelle' => $libelleNomCommercial,
                                    'modeles' => []
                                ]
                            ]);

                            // Pour chaque nom Commercial on récupère tous les modeles
                            $this->oClient->request('get', Constantes::URL . '?marque=' . $idMarque . '&nomCommercial=' . $idNomCommercial);
                            $oJsonModeles = json_decode($this->oClient->getResponse()->getContent());

                            // Gestion du référentiel
                            $this->gereReferentiels($oJsonModeles->Datas->Energies, 'Energies');
                            $this->gereReferentiels($oJsonModeles->Datas->BoiteVitesses, 'BoiteVitesse');
                            $this->gereReferentiels($oJsonModeles->Datas->Carrosseries, 'Carrosserie');
                            $this->gereReferentiels($oJsonModeles->Datas->PuissanceFiscales, 'PuissanceFiscale');

                            foreach($oJsonModeles->Datas->Modeles as $oJsonModele) {
                                $this->em->beginTransaction();
                                try {
                                    $oRefModele = new MccRefModeles();
                                    $oRefModele->setLibelle($oJsonModele->Modele)
                                        ->setAnneeModele($oJsonModele->AnneeModele)
                                        ->setBoiteVitesse($this->aRef['BoiteVitesse'][$oJsonModele->IdBoiteVitesse])
                                        ->setCarrosserie($this->aRef['Carrosserie'][$oJsonModele->IdCarrosserie])
                                        ->setEnergie($this->aRef['Energies'][$oJsonModele->IdEnergie])
                                        ->setNomCommercial($oRefNomCommercial)
                                        ->setPuissanceFiscale($this->aRef['PuissanceFiscale'][$oJsonModele->IdPuissanceFiscale])
                                        ->setTypeMine($oJsonModele->TypeMine);

                                    $this->em->persist($oRefModele);
                                    $this->em->flush();
                                    $this->em->commit();

                                    $aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['modeles'] = array_merge(
                                        $aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['modeles'],
                                        [
                                            $oJsonModele->Modele . '_' . $oJsonModele->TypeMine => [
                                                'id' => $oRefModele->getId(),
                                                'libelle' => $oRefModele->getLibelle(),
                                                'typeMine' => $oRefModele->getTypeMine()
                                            ]
                                        ]
                                    );
                                } catch(\Exception $e) {
                                    $this->em->rollback();
                                    throw $e;
                                }
                            }
                        } catch(\Exception $e) {
                            $this->em->rollback();
                            throw $e;
                        }
                    } else {
                        // Le nom commercial existe
                        // Pour chaque nom Commercial on récupère tous les modeles
                        $this->oClient->request('get', Constantes::URL . '?marque=' . $idMarque . '&nomCommercial=' . $idNomCommercial);
                        $oJsonModeles = json_decode($this->oClient->getResponse()->getContent());

                        // Récupération de l'entité MccRefNomCommercial
                        $oRefNomCommercial = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefNomCommercial')->findOneBy(['id' => $aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['id']]);

                        // Gestion du référentiel
                        $this->gereReferentiels($oJsonModeles->Datas->Energies, 'Energies');
                        $this->gereReferentiels($oJsonModeles->Datas->BoiteVitesses, 'BoiteVitesse');
                        $this->gereReferentiels($oJsonModeles->Datas->Carrosseries, 'Carrosserie');
                        $this->gereReferentiels($oJsonModeles->Datas->PuissanceFiscales, 'PuissanceFiscale');

                        foreach($oJsonModeles->Datas->Modeles as $oJsonModele) {
                            $aModelesKeys = array_keys($aAllModeles[$libelleMarque]['nomCommerciaux'][$libelleMarque . '_' . $libelleNomCommercial]['modeles']);

                            if(!in_array($oJsonModele->Modele . '_' . $oJsonModele->TypeMine, $aModelesKeys)) {
                                // Le modele n'existe pas en base
                                $this->em->beginTransaction();
                                try {
                                        $oRefModele = new MccRefModeles();
                                        $oRefModele->setLibelle($oJsonModele->Modele)
                                            ->setAnneeModele($oJsonModele->AnneeModele)
                                            ->setBoiteVitesse($this->aRef['BoiteVitesse'][$oJsonModele->IdBoiteVitesse])
                                            ->setCarrosserie($this->aRef['Carrosserie'][$oJsonModele->IdCarrosserie])
                                            ->setEnergie($this->aRef['Energies'][$oJsonModele->IdEnergie])
                                            ->setNomCommercial($oRefNomCommercial)
                                            ->setPuissanceFiscale($this->aRef['PuissanceFiscale'][$oJsonModele->IdPuissanceFiscale])
                                            ->setTypeMine($oJsonModele->TypeMine);

                                        $this->em->persist($oRefModele);
                                        $this->em->flush();
                                        $this->em->commit();
                                } catch(\Exception $e) {
                                    $this->em->rollback();
                                    throw $e;
                                }
                            }
                        }
                    }
                }
            }
            gc_collect_cycles();
        }
    }

    protected function gereReferentiels($oRef, $type) {
        if(!array_key_exists($type, $this->aRef)) {
            $this->aRef[$type] = [];
        }

        foreach($oRef as $oValue) {
            if(!array_key_exists($oValue->Id, $this->aRef[$type])) {
                    // Il n'exite pas on l'ajoute
                    $this->aRef[$type][$oValue->Id] = $this->getORef($type, $oValue);
            }
        }
    }

    protected function getORef($type, $oValue) {
        $oRef = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRef' . $type)->findOneBy(array('libelle' => $oValue->Name, 'libelleCourt' => $oValue->Id));

        if(is_null($oRef)) {
            $this->em->beginTransaction();
            try {
                // L'élément n'est pas trouvé dans en base on le crée
                $entity = '\Gosyl\MyCarbuConsoBundle\Entity\MccRef' . $type;
                /**
                 * @var MccRefEnergies | MccRefBoiteVitesse | MccRefPuissanceFiscale | MccRefCarrosserie $oRef
                 */
                $oRef = new $entity();
                $oRef->setLibelle($oValue->Name);
                $oRef->setLibelleCourt($oValue->Id);
                $this->em->persist($oRef);
                $this->em->flush();
                $this->em->commit();
            } catch(\Exception $exception) {
                $this->em->rollback();
                throw $exception;
            }
        }
        return $oRef;
    }

    /**** Recherche ****/

    public function getModelesByMultiCritere($aCriteres) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');

        $aResults = $oMdlMccRefModeles->getModelesByMultiCriteres($aCriteres['anneeModele'], $aCriteres['nomCommercial'], $aCriteres['boiteVitesse'], $aCriteres['carrosserie'], $aCriteres['energie'], $aCriteres['puissanceFiscale']);
        return $aResults;
    }

    public function getModelesByTypeMine($sTypeMine) {
        $oMdlMccRefModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles');
        return $oMdlMccRefModeles->getModelesByTypeMines($sTypeMine);
    }

    public function insertModeleUser(MccModelesUsers $modelesUsers) {
        try {
            $this->em->persist($modelesUsers);
            $this->em->flush();
        } catch(\Exception $e) {
            throw $e;
        }

    }


    protected function getMarques() {
        $aMarques = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefMarques')->getAllMarques();

        $aSortedMarques = array();

        foreach($aMarques as $aMarque) {
            $aSortedMarques[$aMarque['libelle']] = array(
                'libelle' => $aMarque['libelle'],
                'id' => $aMarque['id'],
                'nomCommerciaux' => array()
            );
        }

        return $aSortedMarques;
    }

    protected function getNomsCommerciaux($aAllMarques) {
        $aNomsCommerciaux = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefNomCommercial')->getAllNomsCommerciaux();
        foreach($aNomsCommerciaux as $aNomsCommercial) {
            $aAllMarques[$aNomsCommercial['libelleMarque']]['nomCommerciaux'][$aNomsCommercial['libelleMarque'] . '_' . $aNomsCommercial['libelle']] = array(
                'id' => $aNomsCommercial['id'],
                'libelle' => $aNomsCommercial['libelle'],
                'modeles' => []
            );
        }

        return $aAllMarques;
    }

    protected function getModeles($aAllMarques) {
        $aModeles = $this->em->getRepository('GosylMyCarbuConsoBundle:MccRefModeles')->getAllModeles();//dump($aModeles);die;

        foreach($aModeles as $modele) {
            $aAllMarques[$modele['libelleMarque']]['nomCommerciaux'][$modele['libelleMarque'] . '_' . $modele['libelleNomCommercial']]['modeles'][$modele['libelle'] . '_' . $modele['typeMine']] = array(
                'id' => $modele['id'],
                'libelle' => $modele['libelle'],
                'typeMine' => $modele['typeMine']
            );
        }

        return $aAllMarques;
    }
//    protected function _outputData($data) {
//        // 1024 padding is required for Safari, while 256 padding is required
//        // for Internet Explorer. The <br /> is required so Safari actually
//        // executes the <script />
//        echo str_pad($data . '<br />', 1024, ' ', STR_PAD_RIGHT) . "\n";
//
//        flush();
//        ob_flush();
//    }
}