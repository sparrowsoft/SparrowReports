<?php

namespace Reports\ReportsBundle\Controller\AIG;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatusesController extends Controller {
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
                calllog_campaign = " . $this->campaign_id[0]['campaign_id']  ." AND calllog_makecall_result_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function phoneStatuses() {
        $query = "SELECT calllog_makecall_phone_status FROM cc_calllog WHERE calllog_makecall_phone_status IS NOT NULL AND 
                calllog_campaign = " . $this->campaign_id[0]['campaign_id']  ." AND calllog_makecall_result_time
                BETWEEN '" . $this->start . " 00:00:00' AND '" . $this->end . " 23:59:00'";
        
        $result = $this->em->getConnection()->prepare($query);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function getTableBody() {
        $all_statuses = array();
        $all_phone_statuses = array();
        
        foreach ( $this->Statuses() as $status ) {
            $all_statuses[] = $status['calllog_callcode_id'];
        }
        
        foreach ( $this->phoneStatuses() as $status ) {
            $all_phone_statuses[] = $status['calllog_makecall_phone_status'];
        }

        $values = array_count_values($all_statuses);
        $values_phone = array_count_values($all_phone_statuses);
        
        $table_body = '<tr><td><strong>Sprzedaż</strong></td><td>' . (isset($values[505]) ? $values[505] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie zainteresowany</strong></td><td>' . (isset($values[506]) ? $values[506] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie dzwonić tu więcej</strong></td><td>' . (isset($values[507]) ? $values[507] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie dzwonić i nie wysyłać maili</strong></td><td>' . (isset($values[508]) ? $values[508] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie wysyłać maili</strong></td><td>' . (isset($values[509]) ? $values[509] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Potrzebuje informacji</strong></td><td>' . (isset($values[510]) ? $values[510] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Telefon firmowy</strong></td><td>' . (isset($values[511]) ? $values[511] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Inny język</td><td>' . (isset($values[512]) ? $values[512] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Posiada już taki produkt</td><td>' . (isset($values[513]) ? $values[513] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>W nieodpowiednim wieku</td><td>' . (isset($values[514]) ? $values[514] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Sprzedaż wycofana po weryfikacji</td><td>' . (isset($values[515]) ? $values[515] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>QA Sprzedaż anulowana</td><td>' . (isset($values[516]) ? $values[516] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Brak kluczowych informacji - sprzedaż wycofana</td><td>' . (isset($values[517]) ? $values[517] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie żyje</td><td>' . (isset($values[518]) ? $values[518] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Wyczerpanie prób przy 7 kontakcie</td><td>' . (isset($values[519]) ? $values[519] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient nie używa karty</td><td>' . (isset($values[520]) ? $values[520] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie kontaktować się w sprawie tego produktu</td><td>' . (isset($values[521]) ? $values[521] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zbyt drogo</td><td>' . (isset($values[522]) ? $values[522] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Wymagane dokumenty medyczne</td><td>' . (isset($values[523]) ? $values[523] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmowa podania informacji o koncie</td><td>' . (isset($values[524]) ? $values[524] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie ma karty kredytowej - potrzebuje informacji</td><td>' . (isset($values[525]) ? $values[525] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Powyżej limitu ubezpieczeniowego</td><td>' . (isset($values[526]) ? $values[526] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie dzwonić z ofertami ubezpieczeniowymi</td><td>' . (isset($values[527]) ? $values[527] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>TSR Sprzedaż</td><td>' . (isset($values[528]) ? $values[528] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Błąd - złe dane osobowe</td><td>' . (isset($values[529]) ? $values[529] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie chce teraz decydować</td><td>' . (isset($values[530]) ? $values[530] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie pasuje metoda płatności</td><td>' . (isset($values[531]) ? $values[531] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie kupuje przez telefon</td><td>' . (isset($values[532]) ? $values[532] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie ma konta bankowego</td><td>' . (isset($values[533]) ? $values[533] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Sprzedaż po weryfikacji</td><td>' . (isset($values[534]) ? $values[534] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Anulowana sprzedaż przez kupującego</td><td>' . (isset($values[535]) ? $values[535] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odbiór dokumentów</td><td>' . (isset($values[536]) ? $values[536] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Anulowane podczas dzwonienia</td><td>' . (isset($values[537]) ? $values[537] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Anulowane na piśmie</td><td>' . (isset($values[538]) ? $values[538] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie posiada konta bankowego TM2</td><td>' . (isset($values[539]) ? $values[539] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zakończenie kampanii</td><td>' . (isset($values[540]) ? $values[540] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Ten sam/podobny produkt różny dostawca</td><td>' . (isset($values[541]) ? $values[541] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient posiada informacje i chce przystąpić</td><td>' . (isset($values[542]) ? $values[542] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Potrzebuje dalszych informacji z lokalnego biura</td><td>' . (isset($values[543]) ? $values[543] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Niezadowolony z dostawcy</td><td>' . (isset($values[544]) ? $values[544] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient nie spełnia wymagań</td><td>' . (isset($values[545]) ? $values[545] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient odłożył słuchawkę przed wysłuchaniem skryptu BTM</td><td>' . (isset($values[546]) ? $values[546] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie spełnia warunków zdrowotnych</td><td>' . (isset($values[547]) ? $values[547] : 0) . '</td></tr>';        
        $table_body .= '<tr><td><strong>Nie spełnia warunków - przychód po weryfikacji</td><td>' . (isset($values[548]) ? $values[548] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Nie spełnia warunków mieszkalnych</td><td>' . (isset($values[549]) ? $values[549] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Już kontaktowano się z innym ubezpieczeniem</td><td>' . (isset($values[550]) ? $values[550] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmawia zgody na przekazywanie danych prywatnych do AIG</td><td>' . (isset($values[551]) ? $values[551] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Małżonek już kontaktował się w tej akcji</td><td>' . (isset($values[552]) ? $values[552] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>TPA Awaria Systemu (zamknięty)</td><td>' . (isset($values[553]) ? $values[553] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Brak zgody na nagrywanie</td><td>' . (isset($values[554]) ? $values[554] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient nie zainteresowany po wysłuchaniu oferty</td><td>' . (isset($values[555]) ? $values[555] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Odmowa rozmowy - brak zgody na nagrywanie</td><td>' . (isset($values[556]) ? $values[556] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient rozgląda się za czymś innym</td><td>' . (isset($values[557]) ? $values[557] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient nie używa karty</td><td>' . (isset($values[562]) ? $values[562] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Klient nie warunków - przychód </td><td>' . (isset($values[563]) ? $values[563] : 0) . '</td></tr>';
        $table_body .= '<tr><td></td><td></td></tr><tr><td style="font-size: 16px"><strong>SYSTEMOWE</strong></td><td></td></tr>';
        $table_body .= '<tr><td><strong>Numer nie odpowiada</strong></td><td>' . (isset($values_phone[3]) ? $values_phone[3] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Numer zajęty</strong></td><td>' . (isset($values_phone[2]) ? $values_phone[2] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Fax</strong></td><td>' . (isset($values_phone[4]) ? $values_phone[4] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Automatyczna sekretarka</strong></td><td>' . (isset($values_phone[5]) ? $values_phone[5] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Poczta głosowa</strong></td><td>' . (isset($values_phone[8]) ? $values_phone[8] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Numer nieosiągalny</strong></td><td>' . (isset($values_phone[9]) ? $values_phone[9] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Błędny numer</strong></td><td>' . (isset($values_phone[6]) ? $values_phone[6] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Błąd sieci</strong></td><td>' . (isset($values_phone[7]) ? $values_phone[7] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (maksymalna ilość prób dzwonienia)</strong></td><td>' . (isset($values_phone[30]) ? $values_phone[30] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (przekroczona ilość prób dzwonienia)</strong></td><td>' . (isset($values_phone[31]) ? $values_phone[31] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (nie odpowiada)</strong></td><td>' . (isset($values_phone[21]) ? $values_phone[21] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (zajęty)</strong></td><td>' . (isset($values_phone[20]) ? $values_phone[20] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (poczta głosowa)</strong></td><td>' . (isset($values_phone[26]) ? $values_phone[26] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (IVR / automatyczna sekretarka)</strong></td><td>' . (isset($values_phone[23]) ? $values_phone[23] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (fax)</strong></td><td>' . (isset($values_phone[22]) ? $values_phone[22] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (nieosiągalny)</strong></td><td>' . (isset($values_phone[27]) ? $values_phone[27] : 0) . '</td></tr>';
        $table_body .= '<tr><td><strong>Zamknięty (błąd sieci)</strong></td><td>' . (isset($values_phone[25]) ? $values_phone[25] : 0) . '</td></tr>';
        
        return $table_body;
    }
}


