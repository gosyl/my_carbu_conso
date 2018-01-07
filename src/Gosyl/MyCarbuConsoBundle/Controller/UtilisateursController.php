<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 11/07/17
 * Time: 10:56
 */

namespace Gosyl\MyCarbuConsoBundle\Controller;

use Gosyl\CommonBundle\Constantes;
use Gosyl\CommonBundle\Controller\UtilisateursController as UtilsCtrl;
use Gosyl\CommonBundle\Entity\ParamUsers;
use Gosyl\CommonBundle\Service\Users;
use Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers;
use Gosyl\MyCarbuConsoBundle\Entity\MccRefModeles;
use Gosyl\MyCarbuConsoBundle\Form\AjoutVehiculeType;
use Symfony\Component\HttpFoundation\Request;

class UtilisateursController extends UtilsCtrl {
    public function profilAction(Request $request) {
        //Récupération de l'utilisateur en cours

        /**
         * @var ParamUsers $oUser
         */
        $oUser = $this->getUser();

        // Service Users
        /**
         * @var Users $oSrvUsers
         */
        $oSrvUsers = $this->get('gosyl.common.service.user');
        $aResultOneUser = $oSrvUsers->getOneUserForDataTable($oUser->getId(), $this->generateUrl('gosyl_common_ajax_listerutilisateur'));

        // Récupération des privilèges
        $this->aPrivileges = Constantes::$aPrivileges;

        // Création du formulaire de modification d'un utilisateur
        $oFormModifUser = $this->_createFormModifUser($oUser);

        /**
         * ZONE SURCHARGÉE
         */
        $oSrvGestionBddVl = $this->get('gosyl.my_carbu_conso.services.gestion_bdd_vl');

        if($request->request->has('idModeleUser')) {
            if(!empty($request->request->get('idModeleUser'))) {
                $oModeleUser = $this->getDoctrine()->getRepository('GosylMyCarbuConsoBundle:MccModelesUsers')->findOneBy(array('id' => $request->get('idModeleUser')));

                $oFormAjoutVehicule = $this->createForm(AjoutVehiculeType::class, $oModeleUser);
            } else {
                $oFormAjoutVehicule = $this->createForm(AjoutVehiculeType::class);
            }
        } else {
            $oFormAjoutVehicule = $this->createForm(AjoutVehiculeType::class);
        }

        $oFormAjoutVehicule->handleRequest($request);

        if($oFormAjoutVehicule->isSubmitted()) {
            if($oFormAjoutVehicule->isValid()) {
                /**
                 * @var MccModelesUsers $oModele
                 */
                $oModele = $oFormAjoutVehicule->getData();

                if($request->request->has('idModeleUser') && !empty($request->request->get('idModeleUser'))) {
                    $oModele->setDateModification(new \DateTime());
                } else {
                    $oModele->setUser($this->getUser());
                    $oModele->setDateAjout(new \DateTime());
                }

                $oSrvGestionBddVl->insertModeleUser($oModele);
                return $this->redirectToRoute('gosyl_common_profilutilisateur');
            }
        }


        $aVehicules = $oSrvGestionBddVl->getVehiculeByUser($this->getUser());

        /**
         * FIN ZONE
         */

        return $this->render('GosylMyCarbuConsoBundle:Utilisateurs:profil.html.twig', array(
            'aResultsAllUsers' => $aResultOneUser,
            'oFormModifUser' => $oFormModifUser->createView(),
            'vehicules' => $aVehicules,
            'formAjoutVehicule' => $oFormAjoutVehicule->createView()
        ));
    }

    public function suppressionvlAction(Request $request) {
        $oModeleUser = $this->getDoctrine()->getRepository('GosylMyCarbuConsoBundle:MccModelesUsers')->findOneBy(array('id' => $request->attributes->get('id')));

        try {
            $oModeleUser->setDateSuppression(new \DateTime());

            $this->getDoctrine()->getManager()->persist($oModeleUser);
            $this->getDoctrine()->getManager()->flush();
        } catch(\Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('gosyl_common_profilutilisateur');
    }
}