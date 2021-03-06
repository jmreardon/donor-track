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

session_start();
if (isset($_SESSION['user'])) {
header('Location: index.php');
}
mysql_select_db($database_contacts, $contacts);
$pagetitle = "Login";

//SET SUCCESS NOTICES
function set_msg($msg) 
	{
	$_SESSION['msg'] = $msg;
	}

function display_msg() {
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}

$dis = "none";
if (isset($_SESSION['msg'])) {
$dis = "block";
}
//


if (isset($_POST['email'])  && isset($_POST['password'])) {
mysql_select_db($database_contacts, $contacts);
$query_logincheck = "SELECT * FROM users WHERE user_email = '".mysql_real_escape_string($_POST['email'])."'";
$logincheck = mysql_query($query_logincheck, $contacts) or die(mysql_error());
$row_logincheck = mysql_fetch_assoc($logincheck);
$totalRows_logincheck = mysql_num_rows($logincheck);

if ($totalRows_logincheck==1 && 
    check_password($_POST['password'], $row_logincheck['user_password'], $row_logincheck['user_salt'])) { 
	$_SESSION['user'] = mysql_real_escape_string($_POST['email']);
	$redirect = $row_logincheck['user_home'];
	header(sprintf('Location: %s', $redirect)); die;	
} else {
set_msg('Incorrect Username or Password');
header('Location: login.php'); die;
}

}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $pagetitle; ?></title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  Event.observe(window,'load',function( ) {
    $("email").focus();
  });
</script>
</head>

<body>
<div class="logincontainer">
  <h1>Donor Track</h1>
  <form id="form1" name="form1" method="post" action="">
<span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
    </span></span>Email Address <br />
    <input id="email" name="email" type="text" size="35" class="required validate-email" title="You must enter your email address." />
    <br />
    <br />
    Password<br />
    <input type="password" name="password" class="required" title="Please enter your password." />
    <br />
    <br />
    <input type="submit" name="Submit" value="Login" />
    <a href="password.php">Forget password?</a>
  </form>
					<script type="text/javascript">
						var valid2 = new Validation('form1', {useTitles:true});
					</script>
</div>
</body>
</html>
