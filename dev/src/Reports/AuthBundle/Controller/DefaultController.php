<?php

namespace Reports\AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $session = new Session();
        $session->start();
        
        if ( $session->get('user') != NULL ) {
            return $this->redirect($this->generateUrl('dashboard', array('page' => 'index')));
        } else {
            return $this->redirect($this->generateUrl('login'));
        }
    }
}
