<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 06/07/17
 * Time: 14:59
 */

namespace Gosyl\MyCarbuConsoBundle\Twig\Menu;

use Gosyl\FileserverBundle\Twig\Menu\Administration as AdminMenu;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Administration extends AdminMenu {
    public function __construct(Router $router, AuthorizationChecker $autorization, $namespace) {
        parent::__construct($router, $autorization, $namespace);

        $this->aSubMenu = array_merge($this->aSubMenu, array('GestionBaseVehicule'));
    }
}