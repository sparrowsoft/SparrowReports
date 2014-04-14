<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PersonelController extends Controller {

    public function showTable() {
        $users_list = $this->get('user_service')->getAllUsers();
        
        $breadcrumbs = '<li class="active">Personel</li>';
        
        return $args = array('breadcrumbs' => $breadcrumbs, 'users' => $users_list);
    }
}
