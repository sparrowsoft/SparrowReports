<?php

namespace Reports\ReportsBundle\Controller\WSFinance;

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
                . '<th>Łączny czas rozmów</th><th>Telekomunikacja</th><th>ECP</th><th>Zamknięte rekordy</th>'
                . '<th>Ilość wysłanych e-mail</th><th>Konwersja efektu do rekordów zamkniętych</th><th>Średni czas rozmowy</th>'
                . '<th>Wskaźnik ilości rozmów</th></tr>';
        
        return $table_head;
    }
    
    public function Calls() {
        $query = "SELECT calllog_callcode_id, calllog_calling, calllog_odbior, calllog_koniec, calllog_rezygnacja FROM cc_calllog WHERE calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " 
            AND calllog_makecall_result_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function Emails() {
        $query = "SELECT count(*) AS emails FROM cc_tocall WHERE tocall_out1 IS NOT NULL
            AND tocall_campaign = " . $this->campaign_id[0]['campaign_id']  . " AND 
            tocall_dialer_last_try_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $count_calls = 0;
        $count_gsm_minutes = 0;
        $count_minutes = 0;
        $count_closed = 0;
        $count_orders = 0;
        
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
            
            if ( $call['calllog_callcode_id'] != '') {
                $count_closed++;
            }
            
            if ( $call['calllog_callcode_id'] === 4 ) {
                $count_orders++;
            }
        }
        
        $emails = $this->Emails();
        
        $table_body = '<td>' . $count_calls . '</td><td>' . round(($count_gsm_minutes/60), 2) . '</td><td>' . round(($count_minutes/60), 2) . '</td>'
                . '<td>' . round(($count_gsm_minutes/60)+($count_minutes/60), 2) . '</td><td>' . 
                round((($count_minutes/60) * 0.08)+(($count_gsm_minutes/60)*0.28), 2) . ' zł</td><td>' . round((($count_gsm_minutes/60)+($count_minutes/60))*0.72, 2). ' zł</td>'
                . '<td>' . $count_closed . '</td><td>' . $emails[0]['emails'] . '</td><td>' . round(($count_orders/$count_closed) * 100, 2) . '%</td>'
                . '<td>' . round((($count_gsm_minutes/60)+($count_minutes/60))/$count_calls, 2) . '</td><td>' . round($count_calls/$count_closed, 2) . '</td>';
        
        return $table_body;
    }
    
}


