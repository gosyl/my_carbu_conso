<?php

namespace Gosyl\MyCarbuConsoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GosylMyCarbuConsoBundle:Default:index.html.twig');
    }
}
