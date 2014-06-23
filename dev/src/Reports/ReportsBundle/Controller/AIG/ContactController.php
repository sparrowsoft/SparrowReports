<?php

namespace Reports\ReportsBundle\Controller\AIG;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller {
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
    
    public function Data() {
        $data = array(0 => array(
                'R20005990101050001137', '13', '0005990101050001137', '001', '0011', '20130909', '6651', 'pp', '6276', 'JK', '520', '0001',
                '0', '09444651', 'T1'
            ), 1 => array(
                'R20005990101050001137', '14', '0005990101050001137', '001', '0011', '20130909', '6651', 'pp', '6276', 'JK', '520', '0001',
                '0', '12444950', 'T1'
            ), 2 => array(
                'R20005990101050001137', '15', '0005990101050001137', '001', '0011', '20130909', '6651', 'pp', '6276', 'JK', '520', '0001',
                '0', '15443650', 'T1'
            ), 3 => array(
                'R20005990101050001137', '16', '0005990101050001137', '001', '0011', '20130909', '6651', 'pp', '6276', 'JK', '520', '0001',
                '0', '19162751', 'T1'
            ), 4 => array(
                'R20005990101050063426', '3 ', '0005990101050063426', '001', '0011', '20130909', '6651', 'pp', '6276', 'JK', '520', '0001',
                '0', '10440350', 'T1'
            ), 5 => array(
                '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'
            )
        );
        
        return $data;
    }

    public function getTableBody() {
        $file = 'aig/001_10_20130909_' . strtotime(date('Y-m-d H:i:s')) . '.txt';
        $data = '';
        
        foreach ( $this->Data() as $row ) {
            $data .= str_pad($row[0], 23) . str_pad($row[1], 2) . str_pad($row[2], 48) . str_pad($row[3], 6) . 
                str_pad($row[4], 10) . str_pad($row[5], 9) . str_pad($row[6], 8) . str_pad($row[7], 3) . 
                str_pad($row[8], 8) . str_pad($row[9], 17) . str_pad($row[10], 7) . str_pad($row[11], 7) . 
                str_pad($row[12], 4) . str_pad($row[13], 12) . str_pad($row[14], 3) . PHP_EOL;
        }
        
        file_put_contents($file, $data);
        
        $table_body = '</table>';
        $table_body .= '<p>Raport generuje plik *.txt. Tutaj nie będą prezentowane żadne dane. Aby pobrać plik, kliknij <a href="/web/' . $file . '" download>tutaj</a>.</p>';
                
        return $table_body;
    }
}


