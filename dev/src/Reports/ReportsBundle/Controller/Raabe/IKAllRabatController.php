<?php

namespace Reports\ReportsBundle\Controller\Raabe;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IKAllRabatController extends Controller {
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
            'header' => 'Raport ilościowo-kosztowy (wszystkie kampanie rabatowe)',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function Split() {
        $query = "SELECT tocall_c36 AS email_confirm, tocall_c40 AS split_name, tocall_s_callcode AS callcode FROM cc_tocall WHERE
                tocall_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND tocall_dialer_last_try_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function Connect() {
        $query = "SELECT calllog_callcode_id, calllog_calling, calllog_koniec, calllog_odbior, calllog_rezygnacja FROM cc_calllog WHERE calllog_odbior IS NOT NULL
                AND calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND
                calllog_makecall_time between '" . $this->start . " 00:00:00' and '" . $this->end . " 23:59:59'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function Calls() {
        $query = "SELECT calllog_makecall_phone_status, calllog_agent_status, calllog_callcode_id, calllog_contact_time FROM cc_calllog WHERE
                calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND
                calllog_makecall_time between '" . $this->start . " 00:00:00' and '" . $this->end . " 23:59:59'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableHead() {
        $table_head = '';
        
        return $table_head;
    }
    
    public function getTableBody() {
        $table_body = '';
        
        $splites = $this->Split();
        $connects = $this->Connect();
        
        $orders = 0;
        $private = 0;
        $email_confirm= 0;
        
        foreach ( $splites as $split ) {
            if ( $split['callcode'] === 257 OR $split['callcode'] === 258 OR $split['callcode'] === 259 OR $split['callcode'] === 260 OR $split['callcode'] === 261) {
                ($split['email_confirm'] != '' ? $email_confirm++ : $email_confirm);
                $orders++;
            }
            
            ($split['callcode'] === 146 ? $private++ : $private);
        }
        
        $phone = 0;
        $mobile_phone = 0;
        $seconds_phone = 0;
        $seconds_mobile = 0;
        $calls = 0;
        $orders_secretary = 0;
        
        $mobile_phones = array(50, 51, 53, 57, 60, 66, 69, 72, 73, 78, 79, 88);
        
        
        foreach ( $connects as $connect ) {
            $number = substr($connect['calllog_calling'], 0, 2);
            (in_array($number, $mobile_phones) == true ? $mobile_phone++ : $phone++);
            
            $start_time = date('H:i:s', strtotime($connect['calllog_odbior']));
            $end_time = date('H:i:s', strtotime($connect['calllog_koniec']));
            $no_time = date('H:i:s', strtotime($connect['calllog_rezygnacja']));
            
            if ( in_array($number, $mobile_phones) ) {
                $connect['calllog_koniec'] != '' ? $seconds_mobile += strtotime($end_time) - strtotime($start_time) : $seconds_mobile += strtotime($no_time) - strtotime($start_time);
            } else {
                $connect['calllog_koniec'] != '' ? $seconds_phone += strtotime($end_time) - strtotime($start_time) : $seconds_phone += strtotime($no_time) - strtotime($start_time);
            }
            
            if ( $connect['calllog_callcode_id'] === 257 OR $connect['calllog_callcode_id'] === 258 
                    OR $connect['calllog_callcode_id'] === 259 OR $connect['calllog_callcode_id'] === 261) { 
                $orders_secretary++;
            }
        }
        
        $calls = $this->Calls();
        $calls_count = 0;
        
        foreach ( $calls as $call ) {
            if ( $call['calllog_makecall_phone_status'] === 1 AND $call['calllog_agent_status'] > 1) $calls_count++;
        }
         
        var_dump($calls_count);
        
        $phone_pln = 0.09;
        $mobile_phone_pln = 0.19;
        
        $phone_count_pln = ($seconds_phone > 0 ) ? round(($seconds_phone/60) * $phone_pln, 2)  : 0;
        $mobile_phone_count_pln = ($seconds_mobile > 0) ? round(($seconds_mobile/60) * $mobile_phone_pln, 2) : 0;
        
        $table_body = '<tr><td style="font-weight: bold">' . $splites[0]['split_name'] . '</td><td>' . $orders . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Suma nr prywatnych</td><td>' . $private . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Wysłanych potwierdzeń e-mail</td><td>' . $email_confirm . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Liczba połączeń - połączenia stacjonarne</td><td>' . $phone . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Liczba połączeń - połączenia komórkowe</td><td>' . $mobile_phone . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Liczba połączeń - suma</td><td>' . count($connects) . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Koszt połączeń - połączenia stacjonarne</td><td>' . $phone_count_pln . ' zł</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Koszt połączeń - połączenia komórkowe</td><td>' . $mobile_phone_count_pln . ' zł</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Koszt połączeń - suma</td><td>' . ($mobile_phone_count_pln + $phone_count_pln) . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Minuty - połączenia stacjonarne</td><td>' . round(($seconds_phone/60), 2) . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Minuty - połączenia komórkowe</td><td>' . round(($seconds_mobile/60), 2) . '</td></tr>';
        $table_body .= '<tr><td style="font-weight: bold">Minuty - suma</td><td>' . (round(($seconds_phone/60), 2) + round(($seconds_mobile/60), 2)) . '</td></tr>';
        //$table_body .= '<tr><td style="font-weight: bold">Liczba rozmów sprzedażowych (bez sekretarek)</td><td>' . $orders_secretary . '</td></tr>';
        return $table_body;
    }
}


