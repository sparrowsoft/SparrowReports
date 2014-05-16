<?php

namespace Reports\ReportsBundle\Controller\WSFinance;

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
                calllog_campaign = " . $this->campaign_id[0]['campaign_id']  ." AND calllog_makecall_result_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $all_statuses = array();
        $count_statuses = 0;
        
        foreach ( $this->Statuses() as $status ) {
            if ( $status['calllog_callcode_id'] != 442 ) {
                $all_statuses[] = $status['calllog_callcode_id'];
            }
        }

        $values = array_count_values($all_statuses);
        
        foreach ( $values as $value ) {
            $count_statuses += $value;
        }
        
        $table_body = '<tr><td><strong>Decyzja w innym terminie</strong></td><td>' . (isset($values[29]) ? $values[29] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Kontakt niemożliwy w czasie trwania akcji</strong></td><td>' . (isset($values[26]) ? $values[26] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie spełnia kryteriów</strong></td><td>' . (isset($values[33]) ? $values[33] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie zainteresowany</strong></td><td>' . (isset($values[393]) ? $values[393] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmowa rozmowy</strong></td><td>' . (isset($values[28]) ? $values[28] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Pomyłka</strong></td><td>' . (isset($values[329]) ? $values[329] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Sukces</strong></td><td>' . (isset($values[428]) ? $values[428] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>SUMA</strong></td><td><strong>' . $count_statuses . '</strong></td></tr>';
        return $table_body;
    }
    
}


