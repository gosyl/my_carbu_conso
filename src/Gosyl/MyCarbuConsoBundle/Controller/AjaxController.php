<?php

namespace Gosyl\MyCarbuConsoBundle\Controller;

use Gosyl\MyCarbuConsoBundle\Service\Consommations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxController extends Controller {
    public function modelesAction(Request $request) {
        $oRequest = $request->request;
        $aReturn = [];
        $oSrvGestionBddVl = $this->get('gosyl.my_carbu_conso.services.gestion_bdd_vl');

        if($oRequest->has('typeMine')) {
            // Recherche par type mine
            $typeMine = $request->get('typeMine');
            $aReturn = $oSrvGestionBddVl->getModelesByTypeMine($typeMine);

        } elseif($oRequest->has('anneeMiseEnCirculation') && $oRequest->has('boiteVitesse') && $oRequest->has('carrosserie') && $oRequest->has('energie') && $oRequest->has('marque') && $oRequest->has('nomCommercial') && $oRequest->has('puissanceFiscale')) {
            // Recherche multi-critères
            $aCriteres = [
                'anneeModele' => $oRequest->get('anneeMiseEnCirculation'),
                'boiteVitesse' => $oRequest->get('boiteVitesse'),
                'carrosserie' => $oRequest->get('carrosserie'),
                'energie' => $oRequest->get('energie'),
                'nomCommercial' => $oRequest->get('nomCommercial'),
                'puissanceFiscale' => $oRequest->get('puissanceFiscale')
            ];

            $aReturn = $oSrvGestionBddVl->getModelesByMultiCritere($aCriteres);
        } else {
            return $this->sendJson(array('error' => true, 'reason' => 'Aucun paramètre'));
        }

        return $this->sendJson($aReturn);
    }

    public function marqueAction(Request $request) {//dump($request->get('q')) ;die;
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $query = $request->get('q');
        $aReturn = $oSrvAutocomplete->getMarque($query);

        return $this->sendJson($aReturn);
    }

    public function nomsCommerciauxAction(Request $request) {
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $query = $request->get('q');
        $aReturn = $oSrvAutocomplete->getNomsCommerciaux($query);

        return $this->sendJson($aReturn);
    }

    public function energieAction(Request $request) {
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $query = $request->get('idNomCommercial');
        $aReturn = $oSrvAutocomplete->getEnergie($query);

        return $this->sendJson($aReturn);
    }

    public function boiteVitesseAction(Request $request) {
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $nomCommercial = $request->get('idNomCommercial');
        $energie = $request->get('idEnergie');
        $aReturn = $oSrvAutocomplete->getBoiteVitesse($nomCommercial, $energie);

        return $this->sendJson($aReturn);
    }

    public function carrosserieAction(Request $request) {
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $nomCommercial = $request->get('idNomCommercial');
        $energie = $request->get('idEnergie');
        $boiteVitesse = $request->get('idBoiteVitesse');
        $aReturn = $oSrvAutocomplete->getCarrosserie($nomCommercial, $energie, $boiteVitesse);

        return $this->sendJson($aReturn);
    }

    public function puissanceFiscaleAction(Request $request) {
        $oSrvAutocomplete = $this->get('gosyl.mcc.services.autocomplete');
        $nomCommercial = $request->get('idNomCommercial');
        $energie = $request->get('idEnergie');
        $boiteVitesse = $request->get('idBoiteVitesse');
        $carrosserie = $request->get('idCarrosserie');
        $aReturn = $oSrvAutocomplete->getPuissanceFiscale($nomCommercial, $energie, $boiteVitesse, $carrosserie);

        return $this->sendJson($aReturn);
    }

    public function deleteconsoAction($id) {
        /**
         * @var Consommations $oSrvConso
         */
        $oSrvConso = $this->get('gosyl.mcc.service.consommations');
        if(!$oSrvConso->deleteConso($id)) {
            return $this->sendJson(array('error' => true, 'reason' => 'Echec de la suppression'));
        }

        return $this->sendJson(array('error' => false));
    }

    protected function sendJson($data) {
        $response = new Response(json_encode($data));
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }
}
