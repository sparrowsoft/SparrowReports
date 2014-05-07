<?php

namespace Reports\ReportsBundle\Controller\Raabe;

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
    
    public function DialerStatuses() {
        $statuses_query = "SELECT CASE WHEN calllog_makecall_phone_status > 1 THEN calllog_makecall_phone_status
            WHEN calllog_makecall_phone_status = 1 AND calllog_agent_status > 1 THEN calllog_agent_status
            WHEN calllog_makecall_phone_status = 1 AND calllog_agent_status = 1 AND calllog_callcode_id is not null THEN 998
            WHEN calllog_makecall_phone_status = 1 AND calllog_agent_status = 1 AND calllog_contact_time is not null THEN 999
            WHEN calllog_makecall_phone_status = 1 AND (calllog_agent_status = 1 OR calllog_agent_status IS NULL)  AND calllog_contact_time is null AND calllog_callcode_id is null THEN 1000
            ELSE 2000 END AS status FROM cc_calllog WHERE calllog_campaign = '" . $this->campaign_id[0]['campaign_id'] . "'
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
        
        $count_values = array_count_values($all_statuses);
        
        $dialer_statuses = array();
        
        foreach ( $this->DialerStatuses() as $status ) {
            $dialer_statuses[] = $status['status'];
        }
        
        $dialer_values = array_count_values($dialer_statuses);
        
        $table_body = '<tr><td>Abonent czasowo niedostępny</td><td>' . ((!isset($dialer_values[6]) ? 0 : $dialer_values[6]) + 
                (!isset($dialer_values[7]) ? 0 : $dialer_values[7]) + (!isset($dialer_values[9]) ? 0 : $dialer_values[9])) . '</td></tr>';
        
        $table_body .= '<tr><td>Abonent czasowo niedostępny</td><td>' . (!isset($count_values[27]) ? 0 : $count_values[27]) . '</td></tr>';
        
        $table_body .= '<tr><td>Automatyczna sekretarka</td><td>' . ((!isset($dialer_values[5]) ? 0 : $dialer_values[5]) +
                (!isset($dialer_values[8]) ? 0 : $dialer_values[8])) . '</td></tr>';
        
        $table_body .= '<tr><td>Automatyczna sekretarka</td><td>' . ((!isset($count_values[23]) ? 0 : $count_values[23]) + 
                (!isset($count_values[26]) ? 0 : $count_values[26]) + (!isset($count_values[29]) ? 0 : $count_values[29])) . '</td></tr>';
        
        $table_body .= '<tr><td>Błędne dane</td><td>' . (!isset($count_values[325]) ? 0 : $count_values[325]) . '</td></tr>';
        
        $table_body .= '<tr><td>Błędny numer telefonu</td><td>' . ((!isset($count_values[24]) ? 0 : $count_values[24]) +
                (!isset($count_values[25]) ? 0 : $count_values[25]) + (!isset($count_values[28]) ? 0 : $count_values[28])) . '</td></tr>';
        
        $table_body .= '<tr><td>Brak osoby docelowej</td><td>' . (!isset($count_values[317]) ? 0 : $count_values[317]) . '</td></tr>';
        
        $table_body .= '<tr><td>Brak potencjału</td><td>' . ((!isset($count_values[251]) ? 0 : $count_values[251]) +
                (!isset($count_values[252]) ? 0 : $count_values[252])) . '</td></tr>';
        
        $table_body .= '<tr><td>BRP</td><td>' . (!isset($count_values[251]) ? 0 : $count_values[251]) . '</td></tr>';
        
        $table_body .= '<tr><td>BRP TG</td><td>' . (!isset($count_values[252]) ? 0 : $count_values[252]) . '</td></tr>';
        
        $table_body .= '<tr><td>Dublet</td><td>' . (!isset($count_values[326]) ? 0 : $count_values[326]) . '</td></tr>';
        
        $table_body .= '<tr><td>Zamówienie - dyrektor</td><td>' . (!isset($count_values[257]) ? 0 : $count_values[257]) . '</td></tr>';
        
        $table_body .= '<tr><td>Zamówienie</td><td>' . ((!isset($count_values[257]) ? 0 : $count_values[257]) +
                (!isset($count_values[258]) ? 0 : $count_values[258]) + (!isset($count_values[259]) ? 0 : $count_values[259]) +
                (!isset($count_values[260]) ? 0 : $count_values[260]) + (!isset($count_values[261]) ? 0 : $count_values[261])) . '</td></tr>';
        
        $table_body .= '<tr><td>Brak zainteresowania</td><td>' . ((!isset($count_values[262]) ? 0 : $count_values[262]) +
                (!isset($count_values[263]) ? 0 : $count_values[263]) + (!isset($count_values[264]) ? 0 : $count_values[264]) +
                (!isset($count_values[265]) ? 0 : $count_values[265]) + (!isset($count_values[266]) ? 0 : $count_values[266])) . '</td></tr>';
        
        $table_body .= '<tr><td>Brak zainteresowania - właściciel placówki</td><td>' . (!isset($count_values[431]) ? 0 : $count_values[431]) . '</td></tr>';
        
        $table_body .= '<tr><td>Prezentacja</td><td>' . ((!isset($count_values[303]) ? 0 : $count_values[303]) +
                (!isset($count_values[304]) ? 0 : $count_values[304]) + (!isset($count_values[305]) ? 0 : $count_values[305])) . '</td></tr>';
        
        $table_body .= '<tr><td>Cena</td><td>' . ((!isset($count_values[254]) ? 0 : $count_values[254]) +
                (!isset($count_values[255]) ? 0 : $count_values[255]) + (!isset($count_values[256]) ? 0 : $count_values[256])) . '</td></tr>';
        
        $table_body .= '<tr><td>Nie chce aktualizacji</td><td>' . ((!isset($count_values[288]) ? 0 : $count_values[288]) +
                (!isset($count_values[289]) ? 0 : $count_values[289]) + (!isset($count_values[290]) ? 0 : $count_values[290])) . '</td></tr>';
        
        $table_body .= '<tr><td>Konkurencja - inne publikacje</td><td>' . ((!isset($count_values[278]) ? 0 : $count_values[278]) +
                (!isset($count_values[279]) ? 0 : $count_values[279]) + (!isset($count_values[283]) ? 0 : $count_values[283]) +
                (!isset($count_values[284]) ? 0 : $count_values[284])) . '</td></tr>';
        
        $table_body .= '<tr><td>Konkurencja - Internet</td><td>' . ((!isset($count_values[280]) ? 0 : $count_values[280]) + 
            (!isset($count_values[281]) ? 0 : $count_values[281]) + (!isset($count_values[282]) ? 0 : $count_values[282])) . '</td></tr>';
        
        $table_body .= '<tr><td>Złe doświadczenie z wydawnictwami</td><td>' . ((!isset($count_values[314]) ? 0 : $count_values[314]) +
                (!isset($count_values[315]) ? 0 : $count_values[315]) + (!isset($count_values[316]) ? 0 : $count_values[316])) . '</td></tr>';
        
        $table_body .= '<tr><td>Nie kupuje przez telefon</td><td>' . ((!isset($count_values[291]) ? 0 : $count_values[291]) +
                (!isset($count_values[292]) ? 0 : $count_values[292]) + (!isset($count_values[293]) ? 0 : $count_values[293])) . '</td></tr>';
        
        $table_body .= '<tr><td>Problem z odesłaniem</td><td>' . ((!isset($count_values[306]) ? 0 : $count_values[306]) +
                (!isset($count_values[307]) ? 0 : $count_values[307]) + (!isset($count_values[308]) ? 0 : $count_values[308])) . '</td></tr>';
        
        $table_body .= '<tr><td>Mam doświadczenie</td><td>' . ((!isset($count_values[285]) ? 0 : $count_values[285]) + 
                (!isset($count_values[286]) ? 0 : $count_values[286]) + (!isset($count_values[287]) ? 0 : $count_values[287])) . '</td></tr>';
        
        $table_body .= '<tr><td>Doświadczona kadra</td><td>' . (!isset($count_values[271]) ? 0 : $count_values[271]) . '</td></tr>';
        
        $table_body .= '<tr><td>PORE</td><td>' . (!isset($count_values[302]) ? 0 : $count_values[302]) . '</td></tr>';
        
        $grupa_dn = ((!isset($count_values[262]) ? 0 : $count_values[262]) +
                (!isset($count_values[263]) ? 0 : $count_values[263]) + (!isset($count_values[264]) ? 0 : $count_values[264]) +
                (!isset($count_values[265]) ? 0 : $count_values[265]) + (!isset($count_values[266]) ? 0 : $count_values[266]) + 
                (!isset($count_values[303]) ? 0 : $count_values[303]) + (!isset($count_values[304]) ? 0 : $count_values[304]) + 
                (!isset($count_values[305]) ? 0 : $count_values[305]) + (!isset($count_values[254]) ? 0 : $count_values[254]) +
                (!isset($count_values[255]) ? 0 : $count_values[255]) + (!isset($count_values[256]) ? 0 : $count_values[256]) +
                (!isset($count_values[288]) ? 0 : $count_values[288]) + (!isset($count_values[289]) ? 0 : $count_values[289]) + 
                (!isset($count_values[290]) ? 0 : $count_values[290]) + (!isset($count_values[278]) ? 0 : $count_values[278]) +
                (!isset($count_values[279]) ? 0 : $count_values[279]) + (!isset($count_values[283]) ? 0 : $count_values[283]) +
                (!isset($count_values[284]) ? 0 : $count_values[284]) + (!isset($count_values[280]) ? 0 : $count_values[280]) + 
                (!isset($count_values[281]) ? 0 : $count_values[281]) + (!isset($count_values[282]) ? 0 : $count_values[282]) +
                (!isset($count_values[314]) ? 0 : $count_values[314]) + (!isset($count_values[315]) ? 0 : $count_values[315]) + 
                (!isset($count_values[316]) ? 0 : $count_values[316]) + (!isset($count_values[291]) ? 0 : $count_values[291]) +
                (!isset($count_values[292]) ? 0 : $count_values[292]) + (!isset($count_values[293]) ? 0 : $count_values[293]) +
                (!isset($count_values[306]) ? 0 : $count_values[306]) + (!isset($count_values[307]) ? 0 : $count_values[307]) + 
                (!isset($count_values[308]) ? 0 : $count_values[308]) + (!isset($count_values[285]) ? 0 : $count_values[285]) + 
                (!isset($count_values[286]) ? 0 : $count_values[286]) + (!isset($count_values[287]) ? 0 : $count_values[287]) +
                (!isset($count_values[271]) ? 0 : $count_values[271]));
        
        $table_body .= '<tr><td>Grupa efektów DN</td><td>' . $grupa_dn . '</td></tr>';
        
        $table_body .= '<tr><td>Brak zgody na nagrywanie</td><td>' . ((!isset($count_values[267]) ? 0 : $count_values[267]) + 
                (!isset($count_values[268]) ? 0 : $count_values[268]) + (!isset($count_values[269]) ? 0 : $count_values[269]) +
                (!isset($count_values[270]) ? 0 : $count_values[270])) . '</td></tr>';
        
        $table_body .= '<tr><td>Brak środków finansowych</td><td>' . (!isset($count_values[272]) ? 0 : $count_values[272]) . '</td></tr>';
        
        $table_body .= '<tr><td>Fax</td><td>' . (!isset($dialer_values[4]) ? 0 : $dialer_values[4]) . '</td></tr>';
        
        $table_body .= '<tr><td>Fax</td><td>' . (!isset($count_values[22]) ? 0 : $count_values[22]) . '</td></tr>';
        
        $table_body .= '<tr><td>Likwidacja placówki</td><td>' . (!isset($count_values[327]) ? 0 : $count_values[327]) . '</td></tr>';
        
        $table_body .= '<tr><td>Inny powód odmowy - sekretarka</td><td>' . (!isset($count_values[276]) ? 0 : $count_values[276]) . '</td></tr>';
        
        $table_body .= '<tr><td>Inny powód odmowy</td><td>' . ((!isset($count_values[273]) ? 0 : $count_values[273]) +
                (!isset($count_values[274]) ? 0 : $count_values[274]) + (!isset($count_values[275]) ? 0 : $count_values[275]) +
                (!isset($count_values[277]) ? 0 : $count_values[277])) . '</td></tr>';
        
        $table_body .= '<tr><td>Mieszkanie prywatne</td><td>' . (!isset($count_values[146]) ? 0 : $count_values[146]) . '</td></tr>';
        
        $table_body .= '<tr><td>Nikt nie odbiera</td><td>' . ((!isset($count_values[21]) ? 0 : $count_values[21]) +
                (!isset($count_values[30]) ? 0 : $count_values[30])) . '</td></tr>';
        
        $table_body .= '<tr><td>Nie ma takiego numeru</td><td>' . (!isset($count_values[22]) ? 0 : $count_values[22]) . '</td></tr>';
        
        $table_body .= '<tr><td>Oddzwonienie - sekretarka</td><td>' . (!isset($dialer_values[321]) ? 0 : $dialer_values[321]) . '</td></tr>';
        
        $table_body .= '<tr><td>Oddzwonienie</td><td>' . (!isset($dialer_values[999]) ? 0 : $dialer_values[999]) . '</td></tr>';
        
        $table_body .= '<tr><td>Oddzwonienie</td><td>' . ((!isset($count_values[318]) ? 0 : $count_values[318]) 
                + (!isset($count_values[319]) ? 0 : $count_values[319]) + (!isset($count_values[320]) ? 0 : $count_values[320]) +
                (!isset($count_values[322]) ? 0 : $count_values[322])) . '</td></tr>';
        
        $table_body .= '<tr><td>Odmowa połączenia z osobą docelową</td><td>' . ((!isset($count_values[297]) ? 0 : $count_values[297]) +
                (!isset($count_values[298]) ? 0 : $count_values[298]) + (!isset($count_values[294]) ? 0 : $count_values[294]) +
                (!isset($count_values[295]) ? 0 : $count_values[295]) + (!isset($count_values[296]) ? 0 : $count_values[296])) . '</td></tr>';
        
        $table_body .= '<tr><td>Prośba o ofertę faksem</td><td>' . (!isset($count_values[299]) ? 0 : $count_values[299]) . '</td></tr>';
        
        $table_body .= '<tr><td>Prośba o ofertę e-mailem</td><td>' . (!isset($count_values[300]) ? 0 : $count_values[300]) . '</td></tr>';
        
        $table_body .= '<tr><td>Prośba o ofertę pocztą</td><td>' . (!isset($count_values[301]) ? 0 : $count_values[301]) . '</td></tr>';
        
        $table_body .= '<tr><td>Odmowa rozmowy</td><td>' . (!isset($count_values[247]) ? 0 : $count_values[247]) . '</td></tr>';
        
        $table_body .= '<tr><td>Pomyłka</td><td>' . (!isset($count_values[329]) ? 0 : $count_values[329]) . '</td></tr>';
        
        $table_body .= '<tr><td>Zmiana adresu</td><td>' . (!isset($count_values[323]) ? 0 : $count_values[323]) . '</td></tr>';
        
        $table_body .= '<tr><td>Zbyt częsty kontakt</td><td>' . ((!isset($count_values[309]) ? 0 : $count_values[309]) +
                (!isset($count_values[310]) ? 0 : $count_values[310]) + (!isset($count_values[311]) ? 0 : $count_values[311]) +
                (!isset($count_values[312]) ? 0 : $count_values[312]) + (!isset($count_values[313]) ? 0 : $count_values[313])) . '</td></tr>';
        
        $table_body .= '<tr><td>Zajęty</td><td>' . ((!isset($dialer_values[2]) ? 0 : $dialer_values[2]) + (!isset($dialer_values[3]) ? 0 : $dialer_values[3])) . '</td></tr>';
                
        $table_body .= '<tr><td>Zajęty</td><td>' . (!isset($count_values[20]) ? 0 : $count_values[20]) . '</td></tr>';
        
        $table_body .= '<tr><td>Zmiana numeru</td><td>' . (!isset($count_values[324]) ? 0 : $count_values[324]) . '</td></tr>';
        
        return $table_body;
    }
}


