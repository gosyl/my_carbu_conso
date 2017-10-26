<?php

namespace Gosyl\MyCarbuConsoBundle\Controller;

use Gosyl\MyCarbuConsoBundle\Form\AjoutConsommationType;
use Gosyl\MyCarbuConsoBundle\Form\ChoixDateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
    public function indexAction(Request $request) {
        $aReturn = [];
        $oSrvGestionBddVl = $this->get('gosyl.my_carbu_conso.services.gestion_bdd_vl');
        $oSrvConsommations = $this->get('gosyl.mcc.service.consommations');
        $aReturn['vehicules'] = $oSrvGestionBddVl->getVehiculeByUser($this->getUser(), true);

        if($request->request->has('idConso') && !empty($request->request->get('conso'))) {
            $idConso = $request->request->get('conso');
            $oMccConso = $this->getDoctrine()->getRepository('GosylMyCarbuConsoBundle:MccConsommations')->findOneBy(['id' => $idConso]);
            $oFormAjoutConso = $this->createForm(AjoutConsommationType::class, $oMccConso);
        } else {
            $oFormAjoutConso = $this->createForm(AjoutConsommationType::class);
        }

        $oFormAjoutConso->handleRequest($request);

        if($oFormAjoutConso->isSubmitted()) {
            if($oFormAjoutConso->isValid()) {
                $oMccConso = $oFormAjoutConso->getData();
                /*$oMccConso = */$oSrvConsommations->addConso($oMccConso);

                $this->addFlash(
                    'success',
                    'Le plein du ' .
                        $oMccConso->getDatePlein()->format('d/m/Y') .
                        ' du véhicule ' . $oMccConso->getModeleUser()->getNomVehicule() .
                        ' a bien été enregistré'
                );
            }
        }

        $aReturn['formAjoutConso'] = $oFormAjoutConso->createView();

        return $this->render('GosylMyCarbuConsoBundle:Default:index.html.twig', $aReturn);
    }

    public function statAction(Request $request) {
        $aReturn = [];

        $oFormChoixDate = $this->createForm(ChoixDateType::class);

        $oSrvConsommations = $this->get('gosyl.mcc.service.consommations');
        $aReturn['vehicule'] = $this->getDoctrine()->getRepository('GosylMyCarbuConsoBundle:MccModelesUsers')->findOneBy(['id' => $request->get('id')]);

        $oFormChoixDate->handleRequest($request);
        if($oFormChoixDate->isSubmitted()) {
            if($oFormChoixDate->isValid()) {
                $aConso = $oSrvConsommations->getAllConsommationByVehicule($aReturn['vehicule'], $oFormChoixDate->getData());
            }
        } else {
            $aConso = $oSrvConsommations->getAllConsommationByVehicule($aReturn['vehicule']);
        }





        $aReturn['consoDataTable'] = $aConso;

        $aReturn['conso'] = json_encode($aReturn['consoDataTable']['results']['data']);

        $aReturn['dateDebut'] = $aConso['dateDebut'];

        $aReturn['dateFin'] = $aConso['dateFin'];



        if($request->request->has('idConso') && !empty($request->request->get('idConso'))) {
            $idConso = $request->request->get('idConso');
            $oMccConso = $this->getDoctrine()->getRepository('GosylMyCarbuConsoBundle:MccConsommations')->findOneBy(['id' => $idConso]);
            $oFormAjoutConso = $this->createForm(AjoutConsommationType::class, $oMccConso);
        } else {
            $oFormAjoutConso = $this->createForm(AjoutConsommationType::class);
        }

        $oFormAjoutConso->handleRequest($request);

        if($oFormAjoutConso->isSubmitted()) {
            if($oFormAjoutConso->isValid()) {
                $oMccConso = $oFormAjoutConso->getData();
                /*$oMccConso = */$oSrvConsommations->addConso($oMccConso);

                $this->addFlash(
                    'success',
                    'Le plein du ' .
                        $oMccConso->getDatePlein()->format('d/m/Y') .
                        ' du véhicule ' . $oMccConso->getModeleUser()->getNomVehicule() .
                        ' a bien été enregistré'
                );
                return $this->redirectToRoute('gosyl_mcc_default_stat', ['id' => $aReturn['vehicule']->getId()]);
            }
        }

        $aReturn['formAjoutConso'] = $oFormAjoutConso->createView();
        $aReturn['formChoixDate'] = $oFormChoixDate->createView();

        return $this->render('GosylMyCarbuConsoBundle:Default:stat.html.twig', $aReturn);
    }
}
