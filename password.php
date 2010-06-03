<?php require_once('includes/config.php'); 
require_once('includes/user.inc.php'); 

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

mysql_select_db($database_contacts, $contacts);
$pagetitle = "Password Request";
session_start();
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


if ($_POST['email']) {
mysql_select_db($database_contacts, $contacts);
$query_passwordcheck = "SELECT * FROM users WHERE user_email = '".$_POST['email']."'";
$passwordcheck = mysql_query($query_passwordcheck, $contacts) or die(mysql_error());
$row_passwordcheck = mysql_fetch_assoc($passwordcheck);
$totalRows_passwordcheck = mysql_num_rows($passwordcheck);

$new_password = gen_password(8);

update_profile($_POST['email'], $new_password);

if ($totalRows_passwordcheck==1) { 
	$redirect = "login.php";
	set_msg('A new password has been sent.');

//SEND EMAIL WITH PASSWORD
$password = $row_passwordcheck['user_password'];
$emailfrom = $row_passwordcheck['user_email'];
$name = "Donor Track";
$subject = "Your New Password";
$message = "Your password is $new_password.";
$emailto = $row_passwordcheck['user_email'];

$smail=mail($emailto, $subject, $message, 
	"From: $name <$emailfrom>\n" .
	"MIME-Version: 1.0\n" .
	"Content-type: text/html; charset=iso-8859-1") .
	header(sprintf('Location: %s', $redirect)); die;	
//END SEND EMAIL
}

if ($totalRows_passwordcheck < 1) {
set_msg('Could not send the password.');
header('Location: password.php'); die;
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
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="logincontainer">
  <h1>Donor Track<h1>
 <span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
  </span>  
  <form id="form1" name="form1" method="post" action="">Enter your email address below and your password will be sent to you immediately. <br />
       <br />
       <input name="email" class="required validate-email" type="text" size="35" title="You must enter your email address." />
       <br />
       <input type="submit" name="Submit" value="Send Password" />
       <a href="password.php"></a>  </p>
     <p>&nbsp;</p>
     <p><a href="login.php">Go back    </a></p>
  </form>
					<script type="text/javascript">
						var valid2 = new Validation('form1', {useTitles:true});
					</script>
</div>
</body>
</html>
