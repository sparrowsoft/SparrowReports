<?php

namespace Reports\ReportsBundle\Controller\WSFinance;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConsultantController extends Controller {
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
            'header' => 'Praca konsultantów',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function getTableHead() {
        $table_head = '<tr><th>#</th><th>Agent</th><th>Liczba godzin pracy</th><th>Zamknięć</th><th>Liczba wysłanych e-mail</th></tr>';
        
        return $table_head;
    }
    
    public function WorkTime($id) {
        $closed_query = "SELECT calllog_callcode_id, calllog_odbior, calllog_koniec FROM cc_calllog WHERE
                calllog_agentid = " . $id . " AND calllog_koniec IS NOT NULL AND calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . "
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        $work_time = 0;
        
        foreach ( $closed->fetchAll() as $date ) {
            $start_time = date('H:i:s', strtotime($date['calllog_odbior']));
            $end_time = date('H:i:s', strtotime($date['calllog_koniec']));
            $time = strtotime($end_time) - strtotime($start_time);

            $work_time += $time;
        }
       
        return array('work_time' => round(($work_time/3600), 2));
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
    
    public function Closed($id) {
        $closed_query = "SELECT COUNT(calllog_agentid) AS CLOSED FROM cc_calllog WHERE calllog_agentid = '" . $id . "' 
                AND calllog_callcode_id IS NOT NULL AND calllog_callcode_id != 442 AND calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
                AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function Emails($id) {
        $query = "SELECT count(*) AS emails FROM cc_tocall WHERE tocall_agentid = '" . $id . "' AND tocall_out1 IS NOT NULL
            AND tocall_campaign = " . $this->campaign_id[0]['campaign_id']  . " AND 
            tocall_dialer_last_try_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $table_body = '';
        $count_rows = 1;
        $count_closed = 0;
        $count_time = 0;
        $count_emails = 0;
        
        foreach ( $this->Agents() as $record ) {
            $agent = $this->AgentName($record['agent']);
            $work_time = $this->WorkTime($record['agent']);
            $closed = $this->Closed($record['agent']);
            $emails = $this->Emails($record['agent']);
            
            $table_body .= '<tr><td>' . $count_rows . '</td><td>' . $agent[0]['user_name'] . ' ' . $agent[0]['user_surname'] . '</td>'
                    . '<td>' . $work_time['work_time'] . '</td><td>' . $closed[0]['closed'] . '</td><td>' . $emails[0]['emails'] . '</td></tr>';
            
            $count_rows++;
            $count_closed += $closed[0]['closed'];
            $count_time += $work_time['work_time'];
            $count_emails += $emails[0]['emails'];
        }
        
        $table_body .= '<tr style="font-weight: bold"><td>#</td><td>SUMA</td><td>' . $count_time . '</td>'
                . '<td>' . $count_closed . '</td><td>' . $count_emails . '</td></tr>';
        
        return $table_body;
    }
    
}


