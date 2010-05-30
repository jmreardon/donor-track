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

ob_start();

include('includes/sc-includes.php');
$pagetitle = "Contact";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

?>
<?php if ($_GET['csv']=="import") { ?>
<?php
$row = 1;
$handle = fopen ($_FILES['csv']['tmp_name'],"r");

while ($data = fgetcsv ($handle, 1000, ",")) {


$checkc = mysql_num_rows(mysql_query("SELECT * FROM contacts WHERE contact_id = ".$data[0].""));

	if ($checkc > 0) {

//UPDATE CURRENT RECORDS
  $updateSQL = sprintf("UPDATE contacts SET `contact_first`=%s, contact_last=%s, contact_title=%s, contact_company=%s, contact_street=%s, contact_city=%s, contact_state=%s, contact_zip=%s, contact_email=%s, contact_phone=%s, contact_fax=%s, contact_web=%s, contact_profile=%s WHERE contact_id=".$data['0']."",
                       GetSQLValueString($data['1'], "text"),
                       GetSQLValueString($data['2'], "text"),
                       GetSQLValueString($data['3'], "text"),
                       GetSQLValueString($data['4'], "text"),
                       GetSQLValueString($data['5'], "text"),
                       GetSQLValueString($data['6'], "text"),
                       GetSQLValueString($data['7'], "text"),
                       GetSQLValueString($data['8'], "text"),
                       GetSQLValueString($data['9'], "text"),
                       GetSQLValueString($data['10'], "text"),
                       GetSQLValueString($data['11'], "text"),
                       GetSQLValueString($data['12'], "text"),
                       GetSQLValueString($data['13'], "text"));

  mysql_select_db($database_contacts);
  $Result1 = mysql_query($updateSQL) or die(mysql_error());

}
//


else { 

if ($row > 1) {

//INSERT NEW RECORDS
  $insertSQL = sprintf("INSERT INTO contacts (`contact_first`, contact_last, contact_title, contact_company, contact_street, contact_city, contact_state, contact_zip, contact_email, contact_phone, contact_fax, contact_web, contact_profile) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data['1'], "text"),
                       GetSQLValueString($data['2'], "text"),
                       GetSQLValueString($data['3'], "text"),
                       GetSQLValueString($data['4'], "text"),
                       GetSQLValueString($data['5'], "text"),
                       GetSQLValueString($data['6'], "text"),
                       GetSQLValueString($data['7'], "text"),
                       GetSQLValueString($data['8'], "text"),
                       GetSQLValueString($data['9'], "text"),
                       GetSQLValueString($data['10'], "text"),
                       GetSQLValueString($data['11'], "text"),
                       GetSQLValueString($data['12'], "text"),
                       GetSQLValueString($data['13'], "text"));
    


  mysql_select_db($database_contacts);
  $Result1 = mysql_query($insertSQL) or die(mysql_error());
	$cid = mysql_insert_id();


mysql_query("INSERT INTO history (history_contact, history_date, history_status) VALUES
(
	".$cid.",
	".time().",
	1
)
");


//
}
$row++;
}

}

header('Location: contacts.php');
}

?>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Batch Import </h2>
    <table width="540" border="0" cellpadding="0" cellspacing="0">
      
      <tr>
        <td colspan="2">Click on &quot;Export Contacts&quot; below to see how to set up your CSV file for importing.</td>
      </tr>
      <tr>
        <td colspan="2"><form name="form1" id="form1" enctype="multipart/form-data" method="post" action="?csv=import">
            <input name="csv" type="file" id="csv" size="40" />
            <br />
            <input name="submit" type="submit" value="Import File" />
            <a href="csv.php"></a> 
        </form></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><a href="csv.php"><strong>+ Export Contacts</strong></a></td>
      </tr>
    </table>    
    <p>&nbsp; </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
