<?php require_once('includes/config.php'); 
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
  `contact_tags` text,
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
) TYPE=MyISAM");


mysql_query("CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_level` int(11) default NULL,
  `user_email` varchar(255) default NULL,
  `user_password` varchar(255) default NULL,
  `user_date` int(10) default NULL,
  `user_home` varchar(255) default NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM");

mysql_query("INSERT INTO `users` (`user_id`, `user_level`, `user_email`, `user_password`, `user_date`, `user_home`) VALUES (1, 1, '".mysql_real_escape_string(trim($_POST['email']))."', '".mysql_real_escape_string(trim($_POST['password']))."', NULL, 'index.php')");

mysql_query("CREATE TABLE `history` (
  `history_id` int(11) NOT NULL auto_increment,
  `history_type` int(11) default NULL,
  `history_contact` int(11) default NULL,
  `history_date` int(10) default NULL,
  `history_status` int(11) default NULL,
  `history_user` int(11) default NULL,
  PRIMARY KEY  (`history_id`)
) TYPE=MyISAM");



mysql_query("CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL auto_increment,
  `note_contact` int(11) default NULL,
  `note_text` text,
  `note_date` varchar(10) default NULL,
  `note_status` int(11) default NULL,
  `note_user` int(11) default NULL,
  PRIMARY KEY  (`note_id`)
) TYPE=MyISAM");


mysql_query("CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL auto_increment,
  `tag_description` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`tag_id`)
) TYPE=MyISAM");


mysql_query("CREATE TABLE `tags_assoc` (
  `itag_id` int(11) NOT NULL auto_increment,
  `itag_contact` int(11) default NULL,
  `itag_tag` int(11) default NULL,
  PRIMARY KEY  (`itag_id`)
) TYPE=MyISAM");


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



