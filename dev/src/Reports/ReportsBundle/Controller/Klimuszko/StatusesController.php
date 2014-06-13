<?php

namespace Reports\ReportsBundle\Controller\Klimuszko;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatusesController extends Controller {
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
            'header' => 'Grupa statusów',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function getTableHead() {
        $table_head = '';
        
        return $table_head;
    }
    
    public function Statuses() {
        $query = "SELECT calllog_callcode_id FROM cc_calllog WHERE calllog_callcode_id IS NOT NULL AND 
                calllog_agent_status = 1 AND calllog_campaign = " . $this->campaign_id[0]['campaign_id']  ." AND calllog_callend_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $all_statuses = array();
        $count_statuses = 0;
        
        foreach ( $this->Statuses() as $status ) {
            $all_statuses[] = $status['calllog_callcode_id'];
        }

        $values = array_count_values($all_statuses);
        
        foreach ( $values as $value ) {
            $count_statuses += $value;
        }
        
        $table_body = '<tr><td><strong>Sprzedaż</strong></td><td>' . (isset($values[3]) ? $values[3] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmowa połączenia</strong></td><td>' . (isset($values[30]) ? $values[30] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmowa rozmowy</strong></td><td>' . (isset($values[28]) ? $values[28] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Przekierowanie na infolinię</strong></td><td>' . (isset($values[466]) ? $values[466] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Rozmowa informacyjna</strong></td><td>' . (isset($values[465]) ? $values[465] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>SUMA</strong></td><td><strong>' . $count_statuses . '</strong></td></tr>';
        
        return $table_body;
    }
}