<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScheduleController extends Controller {

    public function show() {
        $breadcrumbs = '<li class="active">Terminarz</li>';
        
        return $args = array('breadcrumbs' => $breadcrumbs);
    }
}
