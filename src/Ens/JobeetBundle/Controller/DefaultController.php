<?php

namespace Ens\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/nothing")
     */
    public function indexAction()
    {
        return $this->render('EnsJobeetBundle:Default:index.html.twig');
    }
}
