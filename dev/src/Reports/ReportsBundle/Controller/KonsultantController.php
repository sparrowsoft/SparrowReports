<?php

namespace Reports\ReportsBundle\Controller;

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
            'header' => 'Konsultanci',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function getWorkTime() {
        $work_time_query = "SELECT temp.calllog_agentid AS AGENT_ID, 
            (SELECT user_name || ' ' || user_surname FROM users WHERE user_id = temp.calllog_agentid) AS AGENT_NAME, 
            CAST((EXTRACT(hour FROM AGENT_WORK_TIME)*3600 + EXTRACT(minute FROM AGENT_WORK_TIME)*60 + EXTRACT(second FROM AGENT_WORK_TIME))/3600 AS FLOAT) AS AGENT_WORK_TIME
            FROM
            (
                SELECT cc_calllog.calllog_agentid, MIN(cc_calllog.calllog_makecall_result_time) AS AGENT_MIN_TIME, 
                    MAX(cc_calllog.calllog_makecall_result_time) as AGENT_MAX_TIME, 
                    (MAX(cc_calllog.calllog_makecall_result_time) - MIN(cc_calllog.calllog_makecall_result_time )) AS AGENT_WORK_TIME
                FROM cc_calllog
                WHERE calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND cc_calllog.calllog_makecall_result_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'
                    AND cc_calllog.calllog_agentid IS NOT NULL
                GROUP BY cc_calllog.calllog_agentid
            ) temp
            ORDER BY temp.calllog_agentid";
        
        $work_time = $this->em->getConnection()->prepare($work_time_query);
        $work_time->execute();
        
        return $work_time->fetchAll();
    }
    
    public function getClosedRecords() {
        $closed_query = "SELECT summary.calllog_agentid AS AGENT_ID, SUM(summary.agent_target) AS AGENT_TARGET, 
            SUM(summary.agent_sales) AS AGENT_SALES
            FROM 
            (
                SELECT temp.calllog_agentid, count(*) AS AGENT_TARGET, CASE WHEN temp.tocall_s_callcode BETWEEN 257 AND 261 THEN count(*) ELSE 0 END AS AGENT_SALES
                FROM
                (
                    SELECT cc_tocall.calllog_agentid, cc_tocall.tocall_s_callcode FROM cc_tocall
                    WHERE tocall_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND tocall_s_callcode BETWEEN 247 AND 316 
                        AND cc_tocall.tocall_dialer_status_time BETWEEN '" . $this->start . " 00:00:00' and '" . $this->end . " 23:59:59'
                        GROUP BY calllog_agentid, tocall_s_callcode, tocall_dialer_status_time 
                ) AS temp
                GROUP BY temp.calllog_agentid, temp.tocall_s_callcode
            ) AS summary
            GROUP BY summary.calllog_agentid ORDER BY summary.calllog_agentid";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function getAllClosedRecords() {
        $closed_query = "SELECT summary.calllog_agentid AS AGENT_ID, SUM(summary.agent_total_close) AS AGENT_TOTAL_CLOSE
            FROM
            (
                SELECT pod.calllog_agentid, count(*) AS AGENT_TOTAL_CLOSE
                FROM
                (
                    SELECT cc_tocall.calllog_agentid, cc_tocall.tocall_s_callcode FROM cc_tocall
                    WHERE tocall_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND tocall_s_callcode IS NOT NULL
                        AND cc_tocall.tocall_dialer_status_time between '" . $this->start . " 00:00:00' and '" . $this->end . " 23:59:59'
                    GROUP BY calllog_agentid, tocall_s_callcode, tocall_dialer_status_time 
            ) AS pod
                GROUP BY pod.calllog_agentid, pod.tocall_s_callcode
            ) AS summary
                GROUP BY summary.calllog_agentid ORDER BY summary.calllog_agentid";
        
        $closed = $this->em->getConnection()->prepare($closed_query);
        $closed->execute();
        
        return $closed->fetchAll();
    }
    
    public function getTableHead() {
        $table_head = '<tr><th>#</th><th>Agent</th><th>Osób docelowych</th>
                <th>Zamówień</th><th>Skuteczność do osoby docelowej</th><th>Liczba godzin pracy</th>
                <th>Zamknięć</th><th>Efektywność</th></tr>';
        
        return $table_head;
    }
    
    public function getTableBody() {
        $combine = array(
            'work_time' => $this->getWorkTime(), 
            'closed' => $this->getClosedRecords(),
            'all_closed' => $this->getAllClosedRecords()
        );
        
        $count_rows = 1;
        $table_body = '';
               
        foreach ( $combine['work_time'] as $row ) {
            $target = 0;
            $sale = 0;
            $closed = 0;
            
            foreach ( $combine['closed'] as $closed_row ) {
                if ( $closed_row['agent_id'] == $row['agent_id']) {
                    $target = $closed_row['agent_target'];
                    $sale = $closed_row['agent_sales'];
                }
            }
            
            foreach ( $combine['all_closed'] as $closed_row ) {
                if ( $closed_row['agent_id'] == $row['agent_id']) {
                    $closed = $closed_row['agent_total_close'];
                }
            }
            
            $target_efficacy = ($target > 0 and $sale > 0 ) ? ($sale / $target) * 100 : 0.00;
            $efficacy = ($closed > 0 and $sale > 0 ) ? ($sale / $closed) * 100 : 0.00;
             
            $table_body .= '<tr><td>' . $count_rows . '</td><td>' . $row['agent_name'] . '</td><td>' . $target . '</td>'
                    . '<td>' . $sale .'</td><td>' . round($target_efficacy, 2) . '%</td><td>' . round($row['agent_work_time'], 2) . '</td>'
                    . '<td>' . $closed . '</td><td>' . round($efficacy, 2) . '%</td></tr>';
            
            $count_rows++;
        }
        
        return $table_body;
    }
    
}


