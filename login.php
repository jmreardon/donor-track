<?php require_once('includes/config.php'); 
session_start();
if (isset($_SESSION['user'])) {
header('Location: index.php');
}
mysql_select_db($database_contacts, $contacts);
$pagetitle = Login;

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


if ($_POST['email']  && $_POST['password']) {
mysql_select_db($database_contacts, $contacts);
$query_logincheck = "SELECT * FROM users WHERE user_email = '".addslashes($_POST['email'])."' AND user_password = '".addslashes($_POST['password'])."'";
$logincheck = mysql_query($query_logincheck, $contacts) or die(mysql_error());
$row_logincheck = mysql_fetch_assoc($logincheck);
$totalRows_logincheck = mysql_num_rows($logincheck);

if ($totalRows_logincheck==1) { 
	$_SESSION['user'] = addslashes($_POST['email']);
	$redirect = $row_logincheck['user_home'];
	header(sprintf('Location: %s', $redirect)); die;	
}

if ($totalRows_logincheck < 1) { 
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
</head>

<body>
<div class="logincontainer">
  <h1>Simple Customer </h1>
  <form id="form1" name="form1" method="post" action="">
<span class="notices" style="display:<?php echo $dis; ?>">
    <?php display_msg(); ?>
    </span></span>Email Address <br />
    <input name="email" type="text" size="35" class="required validate-email" title="You must enter your email address." />
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