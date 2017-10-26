<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 06/07/17
 * Time: 15:05
 */

namespace Gosyl\MyCarbuConsoBundle\Twig\Menu;

use Gosyl\CommonBundle\Twig\Menu\AbstractMenu;

class GestionBaseVehicule extends AbstractMenu {
    protected $sUrl = 'gosyl_gestion_base_vehicule_index';
    protected $sTitle = 'Gestion de la base des véhicules';
    protected $bVerifRole = true;
    protected $bForAnonymousOnly = false;
    protected $aRoles = ['ROLE_ADMIN'];
    protected $aSubMenu = null;
}