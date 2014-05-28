<?php

namespace Reports\ReportsBundle\Controller\ING;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LeadsController extends Controller {

    public function getReport() {
        $result = array(
            'header' => 'Leady',
            'table_head' => $this->getTableHead(),
            'table_body' => $this->getTableBody()
        );
        
        return $result;
    }
    
    public function getTableHead() {
        $table_head = '<tr><th>record_id</th><th>client_number_life</th><th>client_number_pte</th><th>client_number_ps</th><th>client_pesel</th>
			<th>client_surname</th><th>client_first_name</th><th>client_middle_name_1</th><th>client_middle_name_2</th><th>client_birth_date</th><th>client_gender</th>
			<th>phone1</th><th>phone2</th><th>street</th><th>street_number</th><th>apartament_no</th><th>city</th><th>district</th><th>postal_code</th>
			<th>street2</th><th>street_number2</th><th>apartament_no2</th><th>city2</th><th>district2</th><th>postal_code2</th><th>salary_range</th>
			<th>kind_of_locality</th><th>campaign_id</th><th>segment_id</th><th>branch_id</th><th>honor_calendar_capacity</th><th>meeting_date</th>
			<th>meeting_place</th><th>agent_number</th><th>product_category</th><th>marketing_flag</th><th>priority</th><th>send_to_cc</th><th>group_id</th>
			<th>notes</th><th>company_name</th><th>company_nip</th><th>comp_empl_no</th><th>record_date</th><th>lat</th><th>lng</th><th>id_nav_record</th></tr></thead>';
        
        return $table_head;
    }
    
    public function getTableBody() {
        $login = 'cc_sparrow';
	$password = 'sparrow';
	$leads = filter_input(INPUT_GET, 'leads');
        
        $params = array( 'login' => $login, 'password' => $password );
	$client = new \SoapClient("http://invnv-9.pegacloud.com/prweb/PRSOAPServlet/SOAP/INGFWE2EWorkCallCentre/Services?WSDL", $params);
        
        $results = $client->__soapCall('GetLeads', array($leads));
        
        $filename = strtotime(date('Y-m-d H:i:s'));
        $fp = fopen('ing_leads/' . $filename . '.csv', 'w');
        
        $file_array = array();
        $table_body = '';
		
        foreach ( $results->LeadSummary as $result ) {
            $table_body .= '<tr><td>' . $result->RecordID . '</td><td>' . $result->NumberLife . '</td><td>' . $result->NumberPTE . '</td><td>' . $result->NumberPS . '</td>
                <td>' . $result->PESEL . '</td><td>' . $result->LastName . '</td><td>' . $result->FirstName . '</td><td>' . $result->MiddleName1 . '</td>
                <td>' . $result->MiddleName2 . '</td><td>' . $result->BirthDate . '</td><td>' . $result->Gender . '</td><td>' . $result->Phone1 . '</td>
                <td>' . $result->Phone2 . '</td><td>' . $result->Street . '</td><td>' . $result->StreetNumber . '</td><td>' . $result->ApertmentNumber . '</td>
                <td>' . $result->City . '</td><td>' . $result->District . '</td><td>' . $result->ZipCode . '</td><td>' . $result->Street1 . '</td>
                <td>' . $result->StreetNumber1 . '</td><td>' . $result->ApartmentNumber1 . '</td><td>' . $result->City1 . '</td><td>' . $result->District1 . '</td>
                <td>' . $result->ZipCode1 . '</td><td>' . $result->SalaryRange . '</td><td>' . $result->KindOfLocality . '</td><td>' . $result->CampaignId . '</td>
                <td>' . $result->SegmentId . '</td><td>' . $result->BranchId . '</td><td>' . $result->HonorCalendarCapacity . '</td><td>' . $result->MeetingDate . '</td>
                <td>' . $result->MeetingPlace . '</td><td>' . $result->AgentNumber . '</td><td>' . $result->ProductCategory . '</td><td>' . $result->MarketingFlag . '</td>
                <td>' . $result->Priority . '</td><td>' . $result->SendToCC . '</td><td>' . $result->GroupID . '</td><td>' . $result->Notes . '</td>
                <td>' . $result->CompanyName . '</td><td></td><td>' . $result->CompanyEmployeeNr . '</td><td></td><td></td><td></td><td></td></tr>';
			
            foreach ( $result as $cell ) {
                $file_array[]= $cell;
            }
        }

        fputcsv($fp, $file_array);
        fclose($fp);
        
        return $table_body;
    }
}


