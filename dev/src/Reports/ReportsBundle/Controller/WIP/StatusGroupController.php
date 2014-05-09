<?php

namespace Reports\ReportsBundle\Controller\WIP;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatusGroupController extends Controller {
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
    
    public function AllStatuses() {
        $statuses_query = "SELECT calllog_callcode_id FROM cc_calllog WHERE calllog_callcode_id IS NOT NULL
                    AND calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
                    AND calllog_makecall_time BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:59'";
        
        $statuses = $this->em->getConnection()->prepare($statuses_query);
        $statuses->execute();
        
        return $statuses->fetchAll();
    }
    
    public function getTableBody() {
        $all_statuses = array();
        
        foreach ( $this->AllStatuses() as $status ) {
            $all_statuses[] = $status['calllog_callcode_id'];
        }
        
        $values = array_count_values($all_statuses);

        $table_body = '<tr><td>Brak jednoznacznego uzasadnienia</td><td>' . (!isset($values[139]) ? 0 : $values[139]) . '</td></tr>';
        $table_body .= '<tr><td>Firma niesamodzielna/oddział</td><td>' . (!isset($values[150]) ? 0 : $values[150]) . '</td></tr>';
        $table_body .= '<tr><td>Firma w likwidacji</td><td>' . (!isset($values[149]) ? 0 : $values[149]) . '</td></tr>';
        $table_body .= '<tr><td>Inna firma</td><td>' . (!isset($values[148]) ? 0 : $values[148]) . '</td></tr>';
        $table_body .= '<tr><td>Klient zrażony współpracą z WIP</td><td>' . (!isset($values[137]) ? 0 : $values[137]) . '</td></tr>';
        $table_body .= '<tr><td>Mieszkanie prywatne</td><td>' . (!isset($values[146]) ? 0 : $values[146]) . '</td></tr>';
        $table_body .= '<tr><td>Nie spełnia założeń akcji</td><td>' . (!isset($values[145]) ? 0 : $values[145]) . '</td></tr>';
        $table_body .= '<tr><td>Nie zainteresowany tą tematyką</td><td>' . (!isset($values[134]) ? 0 : $values[134]) . '</td></tr>';
        $table_body .= '<tr><td>Obsługa zewnętrzna</td><td>' . (!isset($values[144]) ? 0 : $values[144]) . '</td></tr>';
        $table_body .= '<tr><td>Odmowa rozmowy</td><td>' . (!isset($values[28]) ? 0 : $values[28]) . '</td></tr>';
        $table_body .= '<tr><td>Posiada inne publikacje konkurencji</td><td>' . (!isset($values[135]) ? 0 : $values[135]) . '</td></tr>';
        $table_body .= '<tr><td>Pozyskane zamówienia</td><td>' . (!isset($values[117]) ? 0 : $values[117]) . '</td></tr>';
        $table_body .= '<tr><td>Sekretarka odmawia przełączenia do osoby docelowej</td><td>' . (!isset($values[140]) ? 0 : $values[140]) . '</td></tr>';
        $table_body .= '<tr><td><strong>SUMA KOŃCOWA</strong></td><td><strong>' . count($all_statuses) . '</strong></td></tr>';
        
        return $table_body;
    }
}


