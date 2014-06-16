<?php

namespace Reports\ReportsBundle\Controller\Vivi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class KonsultantController extends Controller {
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
            'header' => 'Praca telemarketerów',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function Orders($id) {
        $closed_query = "SELECT COUNT(calllog_agentid) AS ORDERS FROM cc_calllog WHERE calllog_agentid = '" . $id . "' 
                AND calllog_callcode_id = 3 AND calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function Closed($id) {
        $closed_query = "SELECT COUNT(calllog_agentid) AS CLOSED FROM cc_calllog WHERE calllog_agentid = '" . $id . "' 
                AND calllog_callcode_id IS NOT NULL AND calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function WorkTime($id) {
        $closed_query = "SELECT calllog_callcode_id, calllog_odbior, calllog_koniec FROM cc_calllog WHERE
                calllog_agentid = " . $id . " AND calllog_koniec IS NOT NULL AND calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . "
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        $work_time = 0;
        $counter = 0; 
        
        foreach ( $closed->fetchAll() as $date ) {
            $start_time = date('H:i:s', strtotime($date['calllog_odbior']));
            $end_time = date('H:i:s', strtotime($date['calllog_koniec']));
            $time = strtotime($end_time) - strtotime($start_time);

            $work_time += $time;
            
            if ( $date['calllog_callcode_id'] != NULL ) {
                if ( $date['calllog_callcode_id'] === 3 OR $date['calllog_callcode_id'] === 27 
                        OR $date['calllog_callcode_id'] === 28  OR $date['calllog_callcode_id'] === 10
                        OR $date['calllog_callcode_id'] === 25 OR $date['calllog_callcode_id'] === 29 OR
                        $date['calllog_callcode_id'] === 403 OR $date['calllog_callcode_id'] === 567 OR 
                        $date['calllog_callcode_id'] === 9 OR $date['calllog_callcode_id'] === 564) {
                    $counter++;
                }
            }
        }
       
        return array('target' => $counter, 'work_time' => round(($work_time/3600), 2));
    }
    
    public function Agents() {
        $closed_query = "SELECT DISTINCT calllog_agentid AS AGENT FROM cc_calllog WHERE calllog_agentid IS NOT NULL
                AND calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function AgentName($id) {
        $user_query = "SELECT user_name, user_surname FROM users WHERE user_id = '" . $id . "'";
        
        $user = $this->em->getConnection()->prepare($user_query);
        $user->execute();
        
        return $user->fetchAll();
    }
    
    public function getTableHead() {
        $table_head = '<tr><th>#</th><th>Agent</th><th>Osób docelowych</th>
                <th>Zamówień</th><th>Skuteczność do osoby docelowej</th><th>Liczba godzin pracy</th>
                <th>Zamknięć</th><th>Efektywność</th></tr>';
        
        return $table_head;
    }

    public function getTableBody() {
        $table_body = '';
        $count_rows = 1;
        $count_orders = 0;
        $count_target = 0;
        $count_target_efficacy = 0;
        $count_work_time = 0;
        $count_closed = 0;
        
        foreach ( $this->Agents() as $record ) {
            $agent = $this->AgentName($record['agent']);
            
            $orders = $this->Orders($record['agent']);
            $closed = $this->Closed($record['agent']);
            $work_time = $this->WorkTime($record['agent']);
            
            $efficacy = ($closed[0]['closed'] > 0 and $orders[0]['orders'] > 0 ) ? ($orders[0]['orders'] / $closed[0]['closed']) * 100 : 0.00;
            $target_efficacy = ($work_time['target'] > 0 and $orders[0]['orders'] > 0 ) ? ($orders[0]['orders'] / $work_time['target']) * 100 : 0.00;
            
            $table_body .= '<tr><td>' . $count_rows . '</td><td>' . $agent[0]['user_name'] . ' ' . $agent[0]['user_surname'] . '</td>'
                    . '<td>' . $work_time['target'] . '</td><td>' . $orders[0]['orders'] . '</td><td>' . round($target_efficacy, 2) . '%</td><td>' . $work_time['work_time'] . '</td><td>' . $closed[0]['closed'] . '</td><td>' . round($efficacy, 2) . '%</td></tr>';
            
            $count_rows++;
            $count_orders += $orders[0]['orders'];
            $count_target += $work_time['target'];
            $count_work_time += $work_time['work_time'];
            $count_closed += $closed[0]['closed'];
        }
        
        $table_body .= '<tr style="font-weight: bold"><td>#</td><td>SUMA</td><td>' . $count_target . '</td><td>' . $count_orders. '</td><td>-</td>'
                . '<td>' . $count_work_time . '</td><td>' . $count_closed . '</td><td>-</td></tr>';
        
        return $table_body;
    }
    
}


