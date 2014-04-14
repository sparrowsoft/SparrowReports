<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard/{page}")
     * @Template()
     */
    public function indexAction(Request $request, $page)
    {
        $session = $request->getSession();
        
        if ( $session->get('user') != NULL ) {
            $args = $this->getArguments($session->get('user'), $page, $request);
            
            return $this->render(
                'ReportsDashboardBundle:Default:' . $page . '.html.twig',
                $args
            ); 
        } 
        
        else { 
            return $this->redirect($this->generateUrl('login'));
        }
    }
    
    public function getArguments($user_id, $page, $request) {
        if ( $user_id ) {
            $user_data = $this->get('user_service')->getUserData($user_id);
        }
        
        $session = $request->getSession();
        
        switch ($page) {
            case 'profile':
                $args = $this->get('profile_service')->createProfileForm($user_data, $request);
                return $args;
            
            case 'users':
                if ( $session->get('user_role') == 'a' ) {
                    $args = $this->get('personel_service')->showTable();
                    return $args;
                }
                
                else {
                    return $this->redirect($this->generateUrl('dashboard', array('page' => 'index')));
                }
                
            default: return array('breadcrumbs' => '<li class="active">Strona główna</li>');
        }
    }
}
