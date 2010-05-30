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
$pagetitle = Profile;
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
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
}

mysql_select_db($database_contacts, $contacts);
$query_profile = "SELECT * FROM users";
$profile = mysql_query($query_profile, $contacts) or die(mysql_error());
$row_profile = mysql_fetch_assoc($profile);
$totalRows_profile = mysql_num_rows($profile);

//UPDATE PROFILE
if ($_POST['email']) {

$password = $row_profile['user_password'];

if ($_POST['password']) {
$password = $_POST['password'];
}

mysql_query("UPDATE users SET 
	user_email = '".trim($_POST['email'])."', 
	user_password = '".trim($password)."', 
	user_home = '".trim($_POST['home'])."'");
set_msg('Profile Updated');
$_SESSION['user'] = $_POST['email'];
header('Location: profile.php'); die;
}
//
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Update Profile</title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2>Profile</h2>
    <span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
    </span>
    <form id="form1" name="form1" method="post" action="">
      <p>Email
        <br />
        <input name="email" type="text" id="email" value="<?php echo $row_profile['user_email']; ?>" class="required validate-email" size="35" />
      </p>
      <p><br />
        Password (leave blank to keep current password) <br />
        <input name="password" type="password" id="password" />
          <br />
      </p>
      <p>Home Page<br />
        <input name="home" type="radio" value="index.php" <?php if ($row_profile['user_home']=="index.php") { ?>checked="checked"<?php } ?> />
          Dashboard
      </p>
      <p>
        <input name="home" type="radio" value="contacts.php" <?php if ($row_profile['user_home']=="contacts.php") { ?>checked="checked"<?php } ?> />
      Contacts</p>
      <p>
        <input name="Submit2" type="submit" id="Submit2" value="Update" /> 
        </p>
    </form>
    
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
