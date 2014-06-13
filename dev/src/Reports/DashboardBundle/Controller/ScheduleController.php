<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScheduleController extends Controller {
    public $get;
    public $em;
    
    public function show($user) {
        $this->get = filter_input(INPUT_GET, 'add');
        $this->em = $this->getDoctrine()->getManager();
        
        if ( isset($this->get) ) {
           $this->addSchedule($user);
        }
        
        $breadcrumbs = '<li class="active">Terminarz</li>';
        return $args = array('breadcrumbs' => $breadcrumbs); 
    }
    
    public function addSchedule($user) {
        $select = $this->em->getConnection()->prepare("SELECT id FROM spr_schedule ORDER BY id DESC");
        $select->execute();
        
        $results = $select->fetchAll();
        $next_id = isset($results[0]['id']) ? $results[0]['id'] + 1 : 0;
        
        $time_start = filter_input(INPUT_GET, 'timestart');
        $time_end = filter_input(INPUT_GET, 'timeend');
        $text = filter_input(INPUT_GET, 'text');
        $date = filter_input(INPUT_GET, 'date');
        
        $update = $this->em->getConnection()->prepare(
            "INSERT INTO spr_schedule VALUES (" . $next_id . ", '" . $text . "', " . $user . ", '" . $time_start . "', '" . $time_end . "', '" . $date  . "')"
        );
        
        $update->execute();
        
        return $this->redirect($this->generateUrl('dashboard', array('page' => 'schedule')));
    }
}
