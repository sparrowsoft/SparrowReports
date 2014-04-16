<?php

namespace Reports\ReportsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class KonsultantController extends Controller {
    public $em;
    public $campaign; 
    public $campaign_name;
    public $campaign_id;
    
    public function getReport() {
        $this->campaign = filter_input(INPUT_GET, 'campaign');
        $this->em = $this->getDoctrine()->getManager();
        $this->campaign_name = $this->get('reports_service')->getFullCamapignName($this->campaign);
        $this->campaign_id = $this->get('reports_service')->getCampaignID($this->campaign);
        
        $this->getWorkTime();
    }
    
    public function getWorkTime() {
        $work_time_query = "SELECT temp.calllog_agentid AS AGENT_ID, 
            (SELECT user_name || ' ' || user_surname FROM users WHERE user_id = temp.calllog_agentid) AS AGENT_NAME,
            temp.AGENT_MIN_TIME AS AGENT_START, temp.AGENT_MAX_TIME AS AGENT_END, temp.AGENT_WORK_TIME, 
            CAST((EXTRACT(hour FROM temp.AGENT_WORK_TIME) * 3600 + EXTRACT(minute FROM temp.AGENT_WORK_TIME) * 60 + EXTRACT(second FROM temp.AGENT_WORK_TIME)) / 3600 as FLOAT) AS AGENT_WORK_HOURS --CAST(temp.AGENT_WORK_TIME/ 10800 AS FLOAT) AS AGENT_WORK_TIME
            FROM
            (
                SELECT cc_calllog.calllog_agentid, MIN(cc_calllog.calllog_makecall_result_time) AS AGENT_MIN_TIME, 
                    MAX(cc_calllog.calllog_makecall_result_time) as AGENT_MAX_TIME, 
                    (MAX(cc_calllog.calllog_makecall_result_time) - MIN(cc_calllog.calllog_makecall_result_time )) AS AGENT_WORK_TIME
                FROM cc_calllog
                WHERE calllog_campaign = " . $this->campaign_id[0]['campaign_id'] . " AND cc_calllog.calllog_makecall_result_time BETWEEN '2014-04-15 00:00:00' AND '2014-04-15 23:59:59'
                    AND cc_calllog.calllog_agentid IS NOT NULL
                GROUP BY cc_calllog.calllog_agentid
            ) temp
            ORDER BY temp.calllog_agentid";
        
        $work_time = $this->em->getConnection()->prepare($work_time_query);
        $work_time->execute();
        
        var_dump($work_time->fetchAll());
    }
}


