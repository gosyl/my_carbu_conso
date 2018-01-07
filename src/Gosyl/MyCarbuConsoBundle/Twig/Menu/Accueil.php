<?php
namespace Gosyl\MyCarbuConsoBundle\Twig\Menu;

use Gosyl\CommonBundle\Twig\Menu\AbstractMenu;

class Accueil extends AbstractMenu {
    protected $sUrl = 'gosyl_mcc_homepage';
    protected $sTitle = 'MyCarbuConso';
    protected $bVerifRole = true;
    protected $bForAnonymousOnly = false;
    protected $aRoles = ['ROLE_USER'];
    protected $aSubMenu = null;
}