<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportsController extends Controller {
    public $em;
    public $client;
    public $campaign;
    
    public function show() {
        $this->em = $this->getDoctrine()->getManager();
        $this->client = filter_input(INPUT_GET, 'client');
        $this->campaign = filter_input(INPUT_GET, 'campaign');
        
        $breadcrumbs = '<li class="active">Raporty</li>';
        $campaigns_active = false;
        $campaigns_inactive = false;
        $clients = $this->getCampaignsName();
        
        if ( isset($this->client) ) {
            $campaigns_active = $this->getCampaigns($this->client, 'Aktywna');
            $campaigns_inactive = $this->getCampaigns($this->client, 'Nieaktywna');
        }
        
        $date['default'] = date('Y-m-d');
        $date['yesterday'] = date('Y-m-d',(strtotime( '-1 day', strtotime(date('Y-m-d')))));
        
        return $args = array('breadcrumbs' => $breadcrumbs, 'clients' => $clients, 'campaigns_active' => $campaigns_active, 'campaigns_inactive' => $campaigns_inactive, 'date' => $date);
    }
    
    public function getCampaignsName() {
        $campaigns_select = $this->em->getConnection()->prepare(
                "SELECT DISTINCT campaign_name FROM cc_campaigns WHERE campaign_status = 'Aktywna' ORDER BY campaign_name ASC"
            );
        
        $campaigns_select->execute();
        $campaigns = $campaigns_select->fetchAll();
        
        $campaigns_names = array();
        
        foreach ( $campaigns as $campaign ) {
            $campaign = explode('_', $campaign['campaign_name']);
            
            if ( count($campaign) > 1 ) {
                $campaign_name = ucfirst($campaign[1]);
                $campaign_name .= $this->isNumeric($campaign[2]);
                
                $campaigns_names[] = $this->inArray($campaign_name, $campaigns_names);
            }
        }
        
        $campaigns_names = array_filter($campaigns_names);
        asort($campaigns_names);
        
        return $campaigns_names;
    }
    
    public function isNumeric($string) {
        if ( !is_numeric($string) ) {
            return ' ' . $string;
        }
    }
    
    public function inArray($string, $array) {
        if ( !in_array($string, $array) ) {
            return preg_replace('/\s+/', ' ', $string);
        }
    }
    
    public function getCampaigns($client, $status) {
        $client = explode(' ', $client);
        
        $campaigns = $this->em->getConnection()->prepare(
                "SELECT campaign_name FROM cc_campaigns WHERE campaign_name LIKE '%" . $client[0] . "%' AND campaign_status = '" . $status . "' ORDER BY campaign_name DESC"
            );
        
        $campaigns->execute();
        $campaigns_rows = $campaigns->fetchAll();

        return $campaigns_rows;
    }
    
    public function getFullCamapignName($campaign) {
        $campaign_name = $this->em->getConnection()->prepare(
                "SELECT campaign_name FROM cc_campaigns WHERE campaign_name LIKE '" . $campaign . "%'"
            );
        
        $campaign_name->execute();
        
        return $campaign_name->fetchAll();
    }
    
    public function getCampaignID($campaign) {
        $campaign_id = $this->em->getConnection()->prepare(
                "SELECT campaign_id FROM cc_campaigns WHERE campaign_name LIKE '" . $campaign . "%'"
            );
        
        $campaign_id->execute();
        
        return $campaign_id->fetchAll();
    }
}

