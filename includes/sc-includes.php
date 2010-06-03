<?php

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


session_start();
if (!isset($_SESSION['user'])) {
header('Location: login.php');
}
setlocale(LC_ALL, 'en_CA.UTF-8');

//GET USER INFORMATION
mysql_select_db($database_contacts, $contacts);
$query_userinfo = "SELECT * FROM users WHERE user_email = '".$_SESSION['user']."'";
$userinfo = mysql_query($query_userinfo, $contacts) or die(mysql_error());
$row_userinfo = mysql_fetch_assoc($userinfo);
$totalRows_userinfo = mysql_num_rows($userinfo);
//

//GET OPTION INFORMATION
function get_option ($opt)  {
   $query =  mysql_query("SELECT option_value FROM options WHERE option_title = '".$opt."'");
   $result = mysql_fetch_array($query);
   return $result['option_value'];
}
//

//SET SUCCESS NOTICES
function set_msg($msg) 
	{
	$_SESSION['msg'] = $msg;
	}

function display_msg() {
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}

$dis = none;
if (isset($_SESSION['msg'])) {
$dis = block;
}
//

function echo_campaign_options($selection = 0) {
  $query_campaigns = "SELECT campaign_id, campaign_name FROM campaigns ORDER BY campaign_id DESC";
  $campaigns = mysql_query($query_campaigns) or die(mysql_error());
  $row_campaigns = mysql_fetch_assoc($campaigns);
  $totalRows_campaigns = mysql_num_rows($campaigns);
  do { ?>
    <option <?php if($row_campaigns['campaign_id'] == $selection) { echo "selected='selected' "; } ?> value="<?php echo $row_campaigns['campaign_id']; ?>">
      <?php echo $row_campaigns['campaign_name']; ?>
    </option>
  <?php } while($row_campaigns = mysql_fetch_assoc($campaigns));
}

function display_name($contact) {
  if($contact['contact_company'] && $contact['contact_last']) {
    return sprintf("%s (%s %s)", $contact['contact_company'], $contact['contact_first'], $contact['contact_last']); 
  } else if ($contact['contact_company']) {
    return $contact['contact_company']; 
  } else if ($contact['contact_first']) {
    return sprintf("%s %s", $contact['contact_first'], $contact['contact_last']); 
  } else {
    return sprintf("%s %s", $contact['contact_title'], $contact['contact_last']); 
  }
}
function get_default_campaign() {
  $query_campaign = "SELECT * FROM campaigns ORDER BY campaign_id desc LIMIT 1";
  $campaign = mysql_query($query_campaign) or die(mysql_error());
  return mysql_fetch_assoc($config);
}

function GetSQLValueString($theValue, $theType = "text", $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$contactcount = mysql_query("SELECT * FROM contacts") or die(mysql_error());
$contactcount = mysql_num_rows($contactcount);

//not applicable
$na = '<span style="color:#CCCCCC">N/A</span>';
//

?>
