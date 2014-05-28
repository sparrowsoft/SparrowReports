<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller {
    public $action;
    
    public function __construct() {
        $this->action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
    }
    
    public function createProfileForm($user_data, $request) {
        $user_id = $user_data->id;
        $error = 0;
        
        if ( isset($this->action) && $this->action == 'add') {
            $user_id = 0;
            $user_data->first_name = NULL;
            $user_data->last_name = NULL;
            $user_data->email = NULL;
        }
        
        $form = $this->createFormBuilder()
            ->add('first_name', 'text', array(
                'label' => 'Imię',
                'attr' => array('placeholder' => 'Imię', 'class' => 'form-control'),
                'data' => $user_data->first_name,
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('last_name', 'text', array(
                'label' => 'Nazwisko',
                'attr' => array('placeholder' => 'Nazwisko', 'class' => 'form-control'),
                'data' => $user_data->last_name,
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('username', 'email', array(
                'label' => 'Adres e-mail',
                'attr' => array('placeholder' => 'Adres e-mail', 'class' => 'form-control'),
                'data' => $user_data->email,
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('password', 'password', array(
                'label' => 'Nowe hasło',
                'required' => false,
                'attr' => array('placeholder' => 'Nowe hasło', 'class' => 'form-control'),
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('repeat_password', 'password', array(
                'label' => 'Powtórz hasło',
                'required' => false,
                'attr' => array('placeholder' => 'Powtórz hasło', 'class' => 'form-control'),
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('image', 'file', array(
                'label' => 'Zdjęcie',
                'required' => false,
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ))->add('save', 'submit', array(
                'label' => 'Zapisz',
                'attr' => array('class' => 'btn btn-success'),
            ))->getForm();
        
        if ( $user_data->role == 'a' || isset($this->action)) {
            $form->add('role', 'choice', array(
                'label' => 'Uprawnienia',
                'choices' => array('u' => 'Użytkownik', 'a' => 'Administrator', 't' => 'Telemarketer'),
                'data' => $user_data->role,
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ));
        }
        
        $form->handleRequest($request);

        if ( $form->isValid() ) {
            $data = $form->getData();
            if ( isset($this->action) && $this->action == 'add' ) {
                $error = $this->get('user_service')->addNewUser($data);
            } else {
                $error = $this->get('user_service')->setUserData($data, $user_id, $request);
            }
        }
        
        $profile_errors = array(
            '0' => '',
            '1' => '<div class="alert alert-dismissable alert-success">'
                . '<button type="button" class="close" data-dismiss="alert">×</button>'
                . '<p>Zmiany zostały pomyślnie zapisane</p></div>',
            '2' => '<div class="alert alert-dismissable alert-danger">'
                . '<button type="button" class="close" data-dismiss="alert">×</button>'
                . '<p>Podany adres e-mail już istnieje w bazie!</p></div>',
            '3' => '<div class="alert alert-dismissable alert-danger">'
                . '<button type="button" class="close" data-dismiss="alert">×</button>'
                . '<p>Wprowadzone hasła różnią się od siebie!</p></div>',
        );
        
        if ( isset($this->action) && $this->action == 'add' ) {
            $breadcrumbs = '<li><a href="' . $this->generateUrl('dashboard', array('page' => 'profile')) . '">Profil</a></li><li class="active">Dodaj użytkownika</li>';
        } else {
            $breadcrumbs = '<li class="active">Profil</li>';
        }

        return $args = array('form' => $form->createView(), 'app_error' => $profile_errors[$error], 'breadcrumbs' => $breadcrumbs);
    }
}
