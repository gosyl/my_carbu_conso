<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 19/07/17
 * Time: 15:13
 */

namespace Gosyl\MyCarbuConsoBundle\Service;

use Doctrine\ORM\EntityManager;
use Gosyl\CommonBundle\Constantes as ConstantesCommon;
use Gosyl\MyCarbuConsoBundle\Constantes;
use Gosyl\MyCarbuConsoBundle\Entity\MccConsommations;
use Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers;
use Gosyl\MyCarbuConsoBundle\Repository\MccConsommationsRepository;
use Goutte\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Consommations {
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var null | Client
     */
    protected $oClient = null;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(EntityManager $em, TokenStorage $token) {
        $this->em = $em;
        $this->tokenStorage = $token;
    }

    /**
     * Enregistre une consommation en base
     *
     * @param MccConsommations $oConso
     * @return MccConsommations
     * @throws \Exception
     */
    public function addConso(MccConsommations $oConso) {
        $this->em->beginTransaction();

        try {

            if(!is_null($oConso->getAdresse())) {
                // tentative de récupération des coordonnées GPS
                $adresse = trim($oConso->getAdresse());
                $adresse = urlencode(preg_replace("[\n|\r\n]", " ", $adresse));

                $aCoordonnees = $this->GetCoordonneesGPS($adresse);

                foreach($aCoordonnees as $colonne => $value) {
                    $champ = 'set' . $colonne;
                    $oConso->$champ($value);
                }
            }

            $this->em->persist($oConso);
            $this->em->flush();

            $this->em->commit();
        } catch(\Exception $e) {
            $this->em->rollback();
            throw $e;
        }

        return $oConso;
    }

    public function deleteConso($idConso) {
        $this->em->beginTransaction();

        try {
            /**
             * @var MccConsommations $oMccConso
             */
            $oMccConso = $this->em->getRepository('GosylMyCarbuConsoBundle:MccConsommations')->findOneBy(array('id' => $idConso));
            $oMccConso->setDateSuppression(new \DateTime());

            $this->em->persist($oMccConso);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            return false;
        }

        return true;
    }

    /**
     * Récupération des consommations d'un véhicule donné
     *
     * @param MccModelesUsers $vehicule
     * @param null $aParams
     * @return array
     * @internal param array|null $aParam
     */
    public function getAllConsommationByVehicule(MccModelesUsers $vehicule, $aParams = null) {
        /**
         * @var MccConsommationsRepository $oMdlMccConso
         */
        $oMdlMccConso = $this->em->getRepository('GosylMyCarbuConsoBundle:MccConsommations');
        $aResults = $oMdlMccConso->getAllConsommationsByModeleUser($vehicule, !is_null($aParams) ? $aParams : null);

        foreach($aResults['results'] as $key => $result) {
            /**
             * @var \DateTime $datePlein
             */
            $datePlein = $result['datePlein'];

            $aResults['results'][$key]['datePlein'] = $datePlein->format('d/m/Y');
        }

        $aCols = [
            'idConso' => [
                'title' => 'Identifiant',
                'visible' => false,
                'data' => 'idConso'
            ],
            'datePlein' => [
                'title' => 'Date du plein',
                'data' => 'datePlein'
            ],
            'quantite' => [
                'title' => 'Volume (l)',
                'data' => 'quantite'
            ],
            'prix' => [
                'title' => 'Prix du plein (€)',
                'data' => 'prix'
            ],
            'distance' => [
                'title' => 'Distance parcouru (km)',
                'data' => 'distance'
            ],
            'action' => [
                'title' => '',
                'data' => 'null',
                'orderable' => false,
                'render' => [
                    'render' => 'function(data, type, row, meta) {
                        var contenu = Gosyl.MyCarbuConso.Default.Stat.setBtnActionForConso(data);
                        return contenu;
                    }'
                ]
            ]
        ];

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
            'dom' => '<"H"RCTlf>t<"F"rpi>',
            'createdRow' => [
                'function' => 'function(row, data, dataIndex) {
                    $(row).attr(\'data-idconso\', data.idConso);
                    $(row).data("conso", data);
            }']
        ];

        $aReturn = [
            'options' => array_merge($aOptions, ConstantesCommon::$aDataTableLanguage),
            'cols' => $aCols,
            'results' => ['data' => $aResults['results']],
            'dateDebut' => $aResults['dateDebut'],
            'dateFin' => $aResults['dateFin']
        ];

        return $aReturn;
    }

    /**
     * Récupération des coordonnées GPS d'une adresse
     *
     * @param $adresse
     * @return array
     */
    protected function getCoordonneesGPS($adresse) {
        $lat = '';
        $long = '';

        if(is_null($this->oClient)) {
            $this->oClient = new Client();
        }

        $this->oClient->setServerParameters([
            'timeout' => 20,
            'keepalive' => false,
            'useragent' => Constantes::USER_AGENT,
            'HTTP_USER_AGENT' => Constantes::USER_AGENT
        ]);

        $this->oClient->request('get', 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $adresse . '&key=' . Constantes::GOOGLE_API_KEY . '&language=fr');

        $oJsonDataGoogle = json_decode($this->oClient->getResponse()->getContent());

        if($oJsonDataGoogle->status == 'OK') {
            $lat = $oJsonDataGoogle->results[0]->geometry->location->lat;
            $long = $oJsonDataGoogle->results[0]->geometry->location->lng;
        }

        return ['GpsLat' => $lat, 'GpsLong' => $long];
    }
}