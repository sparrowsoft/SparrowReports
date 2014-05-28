<?php

namespace Reports\ReportsBundle\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DailyOperationsController extends Controller {
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
            'header' => 'Operacje',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function getTableHead() {
        $table_head = '<tr><th>Ilość połączeń</th><th>Ilość minut komórkowe</th><th>Ilość minut stacjonarne</th>'
                . '<th>Łączny czas rozmów</th><th>Zamknięte rekordy</th></tr>';
        
        return $table_head;
    }
    
    public function Calls() {
        $query = "SELECT calllog_callcode_id, calllog_calling, calllog_odbior, calllog_koniec, calllog_rezygnacja FROM cc_calllog WHERE calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " 
            AND calllog_makecall_result_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $count_calls = 0;
        $count_gsm_minutes = 0;
        $count_minutes = 0;
        $count_closed = 0;
        
        $mobile_phones = array(50, 51, 53, 57, 60, 66, 69, 72, 73, 78, 79, 88);
        
        foreach ( $this->Calls() as $call ) {
            $number = substr($call['calllog_calling'], 0, 2);
                            
            if ( $call['calllog_odbior'] != '' ) {
                $count_calls++;
            } 
            
            if ( $call['calllog_odbior'] != '' AND $call['calllog_koniec'] != '') {
                $start_time = date('H:i:s', strtotime($call['calllog_odbior']));
                $end_time = date('H:i:s', strtotime($call['calllog_koniec']));
                $time = strtotime($end_time) - strtotime($start_time);
                
                in_array($number, $mobile_phones) ? $count_gsm_minutes += $time : $count_minutes += $time;
            }
            
            if ( $call['calllog_odbior'] != '' AND $call['calllog_rezygnacja'] != '') {
                $start_time = date('H:i:s', strtotime($call['calllog_odbior']));
                $end_time = date('H:i:s', strtotime($call['calllog_rezygnacja']));
                $time = strtotime($end_time) - strtotime($start_time);
                
                in_array($number, $mobile_phones) ? $count_gsm_minutes += $time : $count_minutes += $time;
            }
            
            if ( $call['calllog_callcode_id'] != '' ) {
                $count_closed++;
            }
        }
        
        $table_body = '<tr><td>' . $count_calls . '</td><td>' . round(($count_gsm_minutes/60), 2) . '</td><td>' . round(($count_minutes/60), 2) . '</td>'
                . '<td>' . round(($count_gsm_minutes/60)+($count_minutes/60), 2) . '</td><td>' . $count_closed . '</td></tr>';
        
        return $table_body;
    }
}


