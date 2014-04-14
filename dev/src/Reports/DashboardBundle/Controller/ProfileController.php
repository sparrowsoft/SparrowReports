<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller {

    public function createProfileForm($user_data, $request) {
        $user_id = $user_data->id;
        $error = 0;
        
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
        
        if ( $user_data->role == 'a' || isset($_GET['action'])) {
            $form->add('role', 'choice', array(
                'label' => 'Uprawnienia',
                'choices' => array('u' => 'Użytkownik', 'a' => 'Administrator'),
                'data' => $user_data->role,
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'col-lg-3 control-label')
            ));
        }
        
        $form->handleRequest($request);

        if ( $form->isValid() ) {
            $data = $form->getData();
            $error = $this->get('user_service')->setUserData($data, $user_id, $request);
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
        
        $breadcrumbs = '<li class="active">Profil</li>';
        
        return $args = array('form' => $form->createView(), 'app_error' => $profile_errors[$error], 'breadcrumbs' => $breadcrumbs);
    }
}
