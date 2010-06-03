<?php require_once('includes/config.php'); 
require_once('includes/user.inc.php'); 

//   Copyright 2008 johnboyproductions.com
//   Copyright 2010 Justin Reardon
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
session_start();
$res = mysql_query("SHOW TABLE STATUS LIKE 'users'") or die(mysql_error());
$table_exists = mysql_num_rows($res) == 1;
$success = 0;
$s = 0;
if (isset($_GET['s'])) {
$success = 1;
$s = 1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Simple Customer:  Installation</title>
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head><body><div class="logincontainer" id="installcontainer"><h1><span class="loginheading">Simple Customer Installation</span></h1>
<?php if ($s!=1) { ?><form id="form1" name="form1" method="post" action="">
    <p>Your email address<br />
      <input name="email" type="text" id="email" size="35" />
</p>
    <p></p>
    <p>Choose a password <br />
      <input name="password" type="password" id="password" />
</p>
    <p></p>
    <input type="submit" name="Submit" value="Submit" />
  </form>
<?php } ?>
  <h1>
    <?php if ($_POST['email'] && $success==0) { ?>
<?php 
mysql_query("CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL auto_increment,
  `contact_first` varchar(255) default NULL,
  `contact_last` varchar(255) default NULL,
  `contact_title` varchar(255) default NULL,
  `contact_image` varchar(255) default NULL,
  `contact_profile` text,
  `contact_company` varchar(255) default NULL,
  `contact_street` varchar(255) default NULL,
  `contact_city` varchar(255) default NULL,
  `contact_state` varchar(255) default NULL,
  `contact_zip` varchar(255) default NULL,
  `contact_phone` varchar(255) default NULL,
  `contact_cell` varchar(255) default NULL,
  `contact_fax` varchar(255) default NULL,
  `contact_email` varchar(255) default NULL,
  `contact_web` varchar(255) default NULL,
  `contact_updated` int(11) default NULL,
  `contact_user` int(11) default NULL,
  PRIMARY KEY  (`contact_id`)
)");


mysql_query("CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_level` int(11) default NULL,
  `user_email` varchar(255) default NULL,
  `user_password` varchar(255) default NULL,
  `user_salt` varchar(255) default NULL,
  `user_date` int(10) default NULL,
  `user_home` varchar(255) default NULL,
  PRIMARY KEY  (`user_id`)
)");


$password_salt = time();

mysql_query("INSERT INTO `users` (`user_id`, `user_level`, `user_email`, `user_password`, `user_salt`, `user_date`, `user_home`) VALUES (1, 1, '".mysql_real_escape_string(trim($_POST['email']))."', '".mysql_real_escape_string(generate_password(trim($_POST['password']), $password_salt)). "', '" . mysql_real_escape_string($password_salt) . "', NULL, 'index.php')");

mysql_query("CREATE TABLE `history` (
  `history_id` int(11) NOT NULL auto_increment,
  `history_type` int(11) default NULL,
  `history_contact` int(11) default NULL,
  `history_date` int(10) default NULL,
  `history_status` int(11) default NULL,
  `history_user` int(11) default NULL,
  PRIMARY KEY  (`history_id`)
)");



mysql_query("CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL auto_increment,
  `note_contact` int(11) default NULL,
  `note_text` text,
  `note_date` varchar(10) default NULL,
  `note_status` int(11) default NULL,
  `note_user` int(11) default NULL,
  PRIMARY KEY  (`note_id`)
)");

mysql_query("CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL auto_increment,
  `contact_id` int(11) default NULL,
  `campaign_id` int(11) default NULL,
  `donation_is_cash` BOOL NOT NULL,
  `donation_value` DECIMAL(10,2) NOT NULL,
  `donation_status` ENUM('expected', 'pledged', 'received') default 'received',
  `donation_pledge_date` DATE default NULL,
  `donation_received_date` DATE default NULL,
  `donation_description` text,
  PRIMARY KEY (`donation_id`)
)");

mysql_query("CREATE TABLE `targets` (
  `target_id` int(11) NOT NULL auto_increment,
  `contact_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  PRIMARY KEY (`contact_id`, `campaign_id`),
  UNIQUE KEY (`target_id`)
)");

mysql_query("CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL auto_increment,
  `campaign_name` varchar(255) NOT NULL,
  `campaign_target` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY  (`campaign_id`)
)");

$_SESSION['user'] = $_POST['email'];
header('Location: install.php?s'); die;
?>
<?php } ?>
<?php if ($success==1) { 
$query_usercheck = "SELECT * FROM users ";
$usercheck = mysql_query($query_usercheck, $contacts) or die(mysql_error());
$row_usercheck = mysql_fetch_assoc($usercheck);
$totalRows_usercheck = mysql_num_rows($usercheck);
if ($totalRows_usercheck > 0) { $success = 1; } 

?>
Installation Successful!  Please delete install.php and <a href="index.php" class="links">proceed to login.</a></h1>
<?php } ?>
</div>

</body>
</html>



