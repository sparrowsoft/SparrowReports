<?php

namespace Reports\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportsController extends Controller {
    public $em;
   
    public function show() {
        $this->em = $this->getDoctrine()->getManager();
        $breadcrumbs = '<li class="active">Raporty</li>';
        $campaigns = $this->getCampaignsName();
        
        return $args = array('breadcrumbs' => $breadcrumbs, 'campaigns' => $campaigns);
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
}

