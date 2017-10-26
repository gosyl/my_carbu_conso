<?php

namespace Gosyl\MyCarbuConsoBundle\Controller;

use Gosyl\MyCarbuConsoBundle\Service\GestionBddVl;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GestionBaseVehiculeController extends Controller {
    public function indexAction(Request $request) {
        /**
         * @var GestionBddVl $oSrvGestionBddVl
         */
        $oSrvGestionBddVl = $this->get('gosyl.my_carbu_conso.services.gestion_bdd_vl');
        $aNbElements = $oSrvGestionBddVl->getNbElementsInBase();

        return $this->render('GosylMyCarbuConsoBundle:GestionBaseVehicule:index.html.twig', array('nbElements' => $aNbElements));
    }

    public function refreshAction() {
        $aReturn = [];

        $oSrvGestionBddVl = $this->get('gosyl.my_carbu_conso.services.gestion_bdd_vl');
        $oSrvGestionBddVl->refresh();

        return $this->redirectToRoute('gosyl_gestion_base_vehicule_index');
    }
}
