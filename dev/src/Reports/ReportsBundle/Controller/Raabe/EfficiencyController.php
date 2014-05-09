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
    
    public function getReach() {
        $reach_query = "SELECT tocall_dialer_tries_sum FROM cc_tocall
            WHERE tocall_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND tocall_s_callcode BETWEEN 257 AND 261
                AND cc_tocall.tocall_dialer_status_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
    
        $reach = $this->em->getConnection()->prepare($reach_query);
        $reach->execute();
        
        return $reach->fetchAll();
    }
    
     public function getSales() {
        $sales_query = "SELECT tocall_dialer_tries_sum FROM cc_tocall
            WHERE tocall_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND tocall_s_callcode BETWEEN 247 AND 316 
                AND cc_tocall.tocall_dialer_status_time between '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
    
        $sales = $this->em->getConnection()->prepare($sales_query);
        $sales->execute();
        
        return $sales->fetchAll();
    }
    
    public function getTableHead() {
        $table_head = '<tr></tr><tr><th>#</th><th>Liczba dotarcia</th><th>% dotarcia</th><th>Liczba sprzedaży</th><th>% sprzedaży</th></tr>';
        
        return $table_head;
    }
    
    public function getTableBody() {
        $table_body = '';
        
        $p1 = 0;
        $p2 = 0;
        $p3 = 0;
        $pp1 = 0;
        $pp2 = 0;
        $pp3 = 0;
        
        foreach ($this->getReach() as $reach_row) {
            if ( $reach_row['tocall_dialer_tries_sum'] == 1 ) $p1++;
            else if ( $reach_row['tocall_dialer_tries_sum'] == 2) $p2++;
            else if ( $reach_row['tocall_dialer_tries_sum'] >= 3) $p3++;
        }
        
        foreach ($this->getSales() as $sales_row) {
            if ( $sales_row['tocall_dialer_tries_sum'] == 1 ) $pp1++;
            else if ( $sales_row['tocall_dialer_tries_sum'] == 2) $pp2++;
            else if ( $sales_row['tocall_dialer_tries_sum'] >= 3) $pp3++;
        }
        
        $table_body .= '<tr><td>1</td><td>' . $pp1 . '</td><td>' . ((count($this->getSales()) != 0) ? (str_replace('.', ',', round(($pp1/count($this->getSales()))*100,2))) . '%' : '0,0%') . '</td><td>' . $p1 . '</td><td>' . ((count($this->getreach()) != 0) ? (str_replace('.', ',', round(($p1/count($this->getReach()))*100,2))) . '%' : '0,0%') . '</td></tr>';
        $table_body .= '<tr><td>2</td><td>' . $pp2 . '</td><td>' . ((count($this->getSales()) != 0) ? (str_replace('.', ',', round(($pp2/count($this->getSales()))*100,2))) . '%' : '0,0%') . '</td><td>' . $p2 . '</td><td>' . ((count($this->getReach()) != 0) ? (str_replace('.', ',', round(($p2/count($this->getReach()))*100,2))) . '%' : '0,0%') . '</td></tr>';
        $table_body .= '<tr><td>3</td><td>' . $pp3 . '</td><td>' . ((count($this->getSales()) != 0) ? (str_replace('.', ',', round(($pp3/count($this->getSales()))*100,2))) . '%' : '0,0%') . '</td><td>' . $p3 . '</td><td>' . ((count($this->getReach()) != 0) ? (str_replace('.', ',', round(($p3/count($this->getReach()))*100,2))) . '%' : '0,0%') . '</td></tr>';
        $table_body .= '<tr style="font-weight: bold"><td>#</td><td>' . count($this->getSales()) . '</td><td>-</td><td>' . count($this->getReach()) . '</td><td>-</td></tr>';
        
        return $table_body;
    }
}


