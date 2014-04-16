<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportsController extends Controller {
    public $em;
    public $client;
   
    public function show() {
        $this->em = $this->getDoctrine()->getManager();
        $this->client = filter_input(INPUT_GET, 'client');
                
        $breadcrumbs = '<li class="active">Raporty</li>';
        $campaigns = false;
        $clients = $this->getCampaignsName();
        
        if ( isset($this->client) ) {
            $campaigns = $this->getCampaigns($this->client);
        }
        
        
        return $args = array('breadcrumbs' => $breadcrumbs, 'clients' => $clients, 'campaigns' => $campaigns);
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
    
    public function getCampaigns($client) {
        $client = explode(' ', $client);
        
        $campaigns = $this->em->getConnection()->prepare(
                "SELECT campaign_name FROM cc_campaigns WHERE campaign_name LIKE '%" . $client[0] . "%' AND campaign_status = 'Aktywna'"
            );
        
        $campaigns->execute();
        $campaigns_rows = $campaigns->fetchAll();
        
        $sort = array();
        
        foreach ( $campaigns_rows as $row ) {
            $row = explode('_', $row['campaign_name']);
            $sort[] = $row[0];
        }
        
        asort($sort);
        return $sort;
    }
}

