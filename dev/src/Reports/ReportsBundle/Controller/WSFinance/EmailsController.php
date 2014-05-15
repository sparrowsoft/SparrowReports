<?php

namespace Reports\ReportsBundle\Controller\WSFinance;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EmailsController extends Controller {
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
                calllog_campaign = " . $this->campaign_id[0]['campaign_id']  . " AND calllog_makecall_result_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function Emails() {
        $query = "SELECT tocall_s_callcode FROM cc_tocall WHERE tocall_out1 IS NOT NULL
            AND tocall_campaign = " . $this->campaign_id[0]['campaign_id']  . " AND 
            tocall_dialer_last_try_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $emails = 0;
        
        foreach ( $this->Emails() as $email ) {
            $emails++;
        }
        
        $table_body = '<tr><td>Wysłane wiadomości e-mail</td><td>' . $emails . '</td></tr>';
        $table_body .= '<tr><td>Ilość zamknięć</td><td>' . count($this->Statuses()) . '</td></tr>';

        return $table_body;
    }
    
}


