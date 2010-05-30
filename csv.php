<?php require_once('includes/config.php');

//   Copyright 2008 johnboyproductions.com
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.

include('includes/sc-includes.php');

mysql_select_db($database_contacts, $contacts);
$query_contacts = "SELECT * FROM contacts";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$row_contacts = mysql_fetch_assoc($contacts);
$totalRows_contacts = mysql_num_rows($contacts);


$csv_output = '"id","First Name","Last Name","Title","Company","Street","City","State","Zip","Email","Phone","Fax","Website","Profile"';
$csv_output .= "\n"; 

$result = mysql_query("$query_contacts");

while($row_contacts = mysql_fetch_array($result)) {
    $csv_output .= '"'.$row_contacts['contact_id'].'","'.$row_contacts['contact_first'].'","'.$row_contacts['contact_last'].'","'.$row_contacts['contact_title'].'","'.$row_contacts['contact_company'].'","'.$row_contacts['contact_street'].'","'.$row_contacts['contact_city'].'","'.$row_contacts['contact_state'].'","'.$row_contacts['contact_zip'].'","'.$row_contacts['contact_email'].'","'.$row_contacts['contact_phone'].'","'.$row_contacts['contact_fax'].'","'.$row_contacts['contact_web'].'","'.$row_contacts['contact_profile'].'"';
$csv_output .= "\n"; 

  }

  //You cannot have the breaks in the same feed as the content. 
  header("Content-type: application/vnd.ms-excel");
  header("Content-disposition: csv; filename=simplecustomer.csv");
  print $csv_output;
  exit;
?>

