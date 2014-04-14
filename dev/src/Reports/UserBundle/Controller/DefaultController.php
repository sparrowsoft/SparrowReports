<?php

namespace Reports\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Reports\UserBundle\Entity\Users;

class DefaultController extends Controller
{
    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        
        if ( $session->get('user') != NULL ) {
            return $this->redirect($this->generateUrl('dashboard', array('page' => 'index')));
        } 
        
        else {
            $error = 0; 
            
            $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('login'))->setMethod('post')
                ->add('username', 'email', array(
                    'label' => 'Adres e-mail',
                    'attr' => array('placeholder' => 'Adres e-mail', 'class' => 'form-control input-login')
                ))->add('password', 'password', array(
                    'label' => 'Hasło',
                    'attr' => array('placeholder' => 'Hasło', 'class' => 'form-control  input-login')
                ))->add('login', 'submit', array(
                    'label' => 'Zaloguj się',
                    'attr' => array('class' => 'btn btn-default btn-login')
                ))->getForm();

            $form->handleRequest($request);

            if ( $form->isValid() ) {
                $data = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $user_info = $em->getRepository('ReportsUserBundle:Users')->findOneBy( array('email' => $data['username']) );
                
                $error = $this->validateLogin($data, $user_info, $session);
                
                if ( $error === 0 ) {
                    return $this->redirect($this->generateUrl('dashboard', array('page' => 'index')));
                }
            }
            
            $login_errors = array(
                '0' => '',
                '1' => '<div class="alert alert-dismissable alert-danger">Użytkownik o podanym adresie e-mail nie istnieje!</div>',
                '2' => '<div class="alert alert-dismissable alert-danger">Podane hasło jest niepoprawne!</div>'
            );
            
            return array('form' => $form->createView(), 'error' => $login_errors[$error]);
        }
    }
    
    /**
     * @Route("/logout")
     * @Template()
     */
    public function logoutAction(Request $request) {
        $session = $request->getSession();
        $session->invalidate();
        
        return $this->redirect($this->generateUrl('login'));
    }

    public function validateLogin($data, $user_info, $session) {
        if ( count($user_info) > 0 ) {
            $user = new Users();
            $validate = $user->validatePassword($data, $user_info);
            
            if ( $validate == 1 ) {
                $session->set('user', $user_info);
                return 0;
            } 
            
            else {
                return 2;
            }
        }
        
        else {
            return 1;
        }
    }

}
