<?php

namespace Reports\ReportsBundle\Controller\Raabe;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EfficiencyController extends Controller {
    public $em;
    public $campaign; 
    public $campaign_name;
    public $campaign_id;
    public $start;
    public $end;
    
    public function getReport() {
        $this->campaign = filter_input(INPUT_GET, 'campaign');
        $this->em = $this->getDoctrine()->getManager();
        $this->campaign_name = $this->get('reports_service')->getFullCamapignName($this->campaign);
        $this->campaign_id = $this->get('reports_service')->getCampaignID($this->campaign);
        $this->start = filter_input(INPUT_GET, 'from');
        $this->end = filter_input(INPUT_GET, 'to');
        
        $result = array(
            'header' => 'Efektywność sprzedaży i dotarcia',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
     public function getTableHead() {
        $table_head = '<tr><th>#</th><th>Liczba</th><th>%</th></tr>';
        
        return $table_head;
    }
    
    public function getTableBody() {
        return true;
    }
}


