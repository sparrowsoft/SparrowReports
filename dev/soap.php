<form method="POST">
	<input type="text" placeholder="Liczba leadów" name="leads" />
	<input type="login" placeholder="Login" name="login" />
	<input type="password" placeholder="Has³o" name="password" />
	<input type="submit" value="Poka¿" name="getLeads" />
</form>
<script type="text/javascript">
var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name, element) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    element.href = uri + base64(format(template, ctx));
  }
})()
</script>
<?php
	if ( filter_input(INPUT_POST, 'getLeads', FILTER_DEFAULT) !== NULL ) {
		$login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
		$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
		$leads = filter_input(INPUT_POST, 'leads', FILTER_DEFAULT);
	
		$params = array( 'login' => $login, 'password' => $password );
		$client = new SoapClient("http://invnv-9.pegacloud.com/prweb/PRSOAPServlet/SOAP/INGFWE2EWorkCallCentre/Services?WSDL", $params);
		
		$results = $client->__soapCall('GetLeads', array($leads));
		
		$table = '<table id="results" style="display: none"><thead><tr><th>record_id</th><th>client_number_life</th><th>client_number_pte</th><th>client_number_ps</th><th>client_pesel</th>
			<th>client_surname</th><th>client_first_name</th><th>client_middle_name_1</th><th>client_middle_name_2</th><th>client_birth_date</th><th>client_gender</th>
			<th>phone1</th><th>phone2</th><th>street</th><th>street_number</th><th>apartament_no</th><th>city</th><th>district</th><th>postal_code</th>
			<th>street2</th><th>street_number2</th><th>apartament_no2</th><th>city2</th><th>district2</th><th>postal_code2</th><th>salary_range</th>
			<th>kind_of_locality</th><th>campaign_id</th><th>segment_id</th><th>branch_id</th><th>honor_calendar_capacity</th><th>meeting_date</th>
			<th>meeting_place</th><th>agent_number</th><th>product_category</th><th>marketing_flag</th><th>priority</th><th>send_to_cc</th><th>group_id</th>
			<th>notes</th><th>company_name</th><th>company_nip</th><th>comp_empl_no</th><th>record_date</th><th>lat</th><th>lng</th><th>id_nav_record</th></tr></thead><tbody>';
		
		$filename = strtotime(date('Y-m-d H:i:s'));
		$fp = fopen($filename . '.csv', 'w');
		
		$file_array = array();
		
		foreach ( $results->LeadSummary as $result ) {
			$table .= '<tr><td>' . $result->RecordID . '</td><td>' . $result->NumberLife . '</td><td>' . $result->NumberPTE . '</td><td>' . $result->NumberPS . '</td>
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

		fputcsv($fp, $file_array, ';', ' ');
		fclose($fp);

		$table .= '</tbody></table>';
		echo $table;
                
        echo '<a href="" download="' . $filename . '.xls" onclick="tableToExcel(\'results\', \'' . $filename . '\', this)">Pobierz xls</a>';
	}
?>