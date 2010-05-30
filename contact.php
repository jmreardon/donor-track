<?php require_once('includes/config.php');

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

include('includes/sc-includes.php');
$pagetitle = Contact;

$update = 0;
if (isset($_GET['id'])) {
$update = 1;
}

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



//
if ($update==1) {
mysql_select_db($database_contacts, $contacts);
$query_contact = "SELECT * FROM contacts WHERE contact_id = ".$_GET['id']."";
$contact = mysql_query($query_contact, $contacts) or die(mysql_error());
$row_contact = mysql_fetch_assoc($contact);
$totalRows_contact = mysql_num_rows($contact);
}
//

//UPLOAD PICTURE
	$picture = $_POST['image_location'];
	$time = substr(time(),0,5);	
   if($_FILES['image'] && $_FILES['image']['size'] > 0){
	$ori_name = $_FILES['image']['name'];
	$ori_name = $time.$ori_name;
	$tmp_name = $_FILES['image']['tmp_name'];
	$src = imagecreatefromjpeg($tmp_name);
	list($width,$height)=getimagesize($tmp_name);
	$newwidth=95;
	$newheight=($height/$width)*95;
	$tmp=imagecreatetruecolor($newwidth,$newheight);
	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	$filename = "images/". $ori_name;
	imagejpeg($tmp,$filename,100);
	$picture = $ori_name;
	imagedestroy($src);
	imagedestroy($tmp);	
}
//END UPLOAD PICTURE

if ($update==0) {
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  mysql_query("INSERT INTO contacts (contact_tags, contact_first, contact_last, contact_title, contact_image, contact_profile, contact_company, contact_street, contact_city, contact_state, contact_zip, contact_phone, contact_cell, contact_email, contact_web, contact_updated) VALUES 

	(
		'".mysql_real_escape_string(trim($_POST['contact_tags']))."',
		'".mysql_real_escape_string(trim($_POST['contact_first']))."',
		'".mysql_real_escape_string(trim($_POST['contact_last']))."',
		'".mysql_real_escape_string(trim($_POST['contact_title']))."',
		'".mysql_real_escape_string($picture)."',
		'".mysql_real_escape_string(trim($_POST['contact_profile']))."',
		'".mysql_real_escape_string(trim($_POST['contact_company']))."',
		'".mysql_real_escape_string(trim($_POST['contact_street']))."',
		'".mysql_real_escape_string(trim($_POST['contact_city']))."',
		'".mysql_real_escape_string(trim($_POST['contact_state']))."',
		'".mysql_real_escape_string(trim($_POST['contact_zip']))."',
		'".mysql_real_escape_string(trim($_POST['contact_phone']))."',
		'".mysql_real_escape_string(trim($_POST['contact_cell']))."',
		'".mysql_real_escape_string(trim($_POST['contact_email']))."',
		'".mysql_real_escape_string(trim($_POST['contact_web']))."',
		'".time()."'
	)

	");

$cid = mysql_insert_id();

//insert tags
$tags = str_replace("","",$_POST['contact_tags']);
$tags = explode(",",$tags);

foreach ($tags as $key => $value) {

$value = trim($value);

	if ($value) {
		mysql_query("DELETE FROM tags WHERE tag_description = '".mysql_real_escape_string($value)."'");
		mysql_query("INSERT INTO tags (tag_description) VALUES
		
		(
			'".mysql_real_escape_string($value)."'
		)
		
		");
	$tid = mysql_insert_id();

	//associate tag with contact
	mysql_query("INSERT INTO tags_assoc (itag_contact, itag_tag) VALUES
	(
		'".$cid."',
		'".$tid."'
	)
	");
	//
}

}

	set_msg('Contact Added');
	$redirect = "contact-details.php?id=$cid";
	header('Location: '.$redirect); die;
}
}

if ($update==1) {
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE contacts SET contact_tags=%s, contact_first=%s, contact_last=%s, contact_title=%s, contact_image=%s, contact_profile=%s, contact_company=%s, contact_street=%s, contact_city=%s, contact_state=%s, contact_zip=%s, contact_phone=%s, contact_cell=%s, contact_email=%s, contact_web=%s, contact_updated=%s WHERE contact_id=%s",

                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_tags'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_first'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_last'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_title'])), "text"),
                       GetSQLValueString(mysql_real_escape_string($picture), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_profile'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_company'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_street'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_city'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_state'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_zip'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_phone'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_cell'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_email'])), "text"),
                       GetSQLValueString(mysql_real_escape_string(trim($_POST['contact_web'])), "text"),
                       GetSQLValueString(trim($_POST['contact_updated']), "int"),
                       GetSQLValueString(trim($_POST['contact_id']), "int"));

  mysql_select_db($database_contacts, $contacts);
  $Result1 = mysql_query($updateSQL, $contacts) or die(mysql_error());
	$pid = $_GET['id'];

//insert tags
$tags = str_replace("","",$_POST['contact_tags']);
$tags = explode(",",$tags);

foreach ($tags as $key => $value) {

$value = trim($value);

	if ($value) {
		mysql_query("DELETE FROM tags WHERE tag_description = '".mysql_real_escape_string($value)."'");
		mysql_query("INSERT INTO tags (tag_description) VALUES
		
		(
			'".mysql_real_escape_string($value)."'
		)
		
		");
	$tid = mysql_insert_id();

	//associate tag with contact
	mysql_query("INSERT INTO tags_assoc (itag_contact, itag_tag) VALUES
	(
		'".$pid."',
		'".$tid."'
	)
	");
	//

	}

}

//

	set_msg('Contact Updated');
	$cid = $_GET['id'];
	$redirect = "contact-details.php?id=$cid";
	header('Location: '.$redirect); die;
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php if ($update==0) { echo "Add Contact"; } ?><?php echo $row_contact['contact_first']; ?> <?php echo $row_contact['contact_last']; ?></title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>
<script language="javascript">
function toggleLayer(whichLayer)
{
if (document.getElementById)
{
// this is the way the standards work
var style2 = document.getElementById(whichLayer).style;
style2.display = style2.display? "":"block";
}
else if (document.all)
{
// this is the way old msie versions work
var style2 = document.all[whichLayer].style;
style2.display = style2.display? "":"block";
}
else if (document.layers)
{
// this is the way nn4 works
var style2 = document.layers[whichLayer].style;
style2.display = style2.display? "":"block";
}
}
</script>
<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2><?php if ($update==1) { echo Update; } else { echo Add; } ?> Contact </h2>
    <p>&nbsp;</p>
    <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="form1" id="form1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="28%">First Name<br />
            <input name="contact_first" type="text" class="required" id="contact_first" value="<?php echo $row_contact['contact_first']; ?>" size="25" /></td>
          <td width="72%">Last Name<br />
                <input name="contact_last" type="text" class="required" id="contact_last" value="<?php echo $row_contact['contact_last']; ?>" size="25" />
            </p></td>
        </tr>
        <tr>
          <td>Title<br />            <input name="contact_title" type="text" id="contact_title" value="<?php echo $row_contact['contact_title']; ?>" size="25" />          </td>
          <td>Company<br />
            <input name="contact_company" type="text" id="contact_company" value="<?php echo $row_contact['contact_company']; ?>" size="35" /></td>
        </tr>
        <tr>
          <td colspan="2">Email <br />
            <input name="contact_email" type="text" class="required validate-email" id="contact_email" value="<?php echo $row_contact['contact_email']; ?>" size="35" /></td>
        </tr>
        <tr>
          <td colspan="2"><hr />
         <?php if ($update!=1) { ?>   <p><a href="#" onclick="new Effect.toggle('morecontact', 'slide'); return false;">+Add more contact information </a></p><?php } ?>

<div <?php if ($update!=1) { ?>id="morecontact" style="display:none"<?php } ?>>
            <table  width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>Street<br />
                    <input name="contact_street" type="text" id="contact_street" value="<?php echo $row_contact['contact_street']; ?>" size="35" /></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="39%">City<br />
                          <input name="contact_city" type="text" id="contact_city" value="<?php echo $row_contact['contact_city']; ?>" size="35" /></td>
                      <td width="27%" valign="top">State<br />
                          <select name="contact_state" id="contact_state">
<option value="">Select a state...</option>
                            <option value="AL" <?php if (!(strcmp("AL", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Alabama</option>
                            <option value="AK" <?php if (!(strcmp("AK", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Alaska</option>
                            <option value="AZ" <?php if (!(strcmp("AZ", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Arizona</option>
                            <option value="AR" <?php if (!(strcmp("AR", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Arkansas</option>
                            <option value="CA" <?php if (!(strcmp("CA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>California</option>
                            <option value="CO" <?php if (!(strcmp("CO", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Colorado</option>
                            <option value="CT" <?php if (!(strcmp("CT", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Connecticut</option>
                            <option value="DE" <?php if (!(strcmp("DE", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Delaware</option>
                            <option value="DC" <?php if (!(strcmp("DC", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>District of Columbia</option>
                            <option value="FL" <?php if (!(strcmp("FL", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Florida</option>
                            <option value="GA" <?php if (!(strcmp("GA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Georgia</option>
                            <option value="HI" <?php if (!(strcmp("HI", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Hawaii</option>
                            <option value="ID" <?php if (!(strcmp("ID", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Idaho</option>
                            <option value="IL" <?php if (!(strcmp("IL", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Illinois</option>
                            <option value="IN" <?php if (!(strcmp("IN", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Indiana</option>
                            <option value="IA" <?php if (!(strcmp("IA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Iowa</option>
                            <option value="KS" <?php if (!(strcmp("KS", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Kansas</option>
                            <option value="KY" <?php if (!(strcmp("KY", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Kentucky</option>
                            <option value="LA" <?php if (!(strcmp("LA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Louisiana</option>
                            <option value="ME" <?php if (!(strcmp("ME", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Maine</option>
                            <option value="MD" <?php if (!(strcmp("MD", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Maryland</option>
                            <option value="MA" <?php if (!(strcmp("MA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Massachusetts</option>
                            <option value="MI" <?php if (!(strcmp("MI", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Michigan</option>
                            <option value="MN" <?php if (!(strcmp("MN", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Minnesota</option>
                            <option value="MS" <?php if (!(strcmp("MS", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Mississippi</option>
                            <option value="MO" <?php if (!(strcmp("MO", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Missouri</option>
                            <option value="MT" <?php if (!(strcmp("MT", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Montana</option>
                            <option value="NE" <?php if (!(strcmp("NE", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Nebraska</option>
                            <option value="NV" <?php if (!(strcmp("NV", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Nevada</option>
                            <option value="NH" <?php if (!(strcmp("NH", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>New Hampshire</option>
                            <option value="NJ" <?php if (!(strcmp("NJ", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>New Jersey</option>
                            <option value="NM" <?php if (!(strcmp("NM", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>New Mexico</option>
                            <option value="NY" <?php if (!(strcmp("NY", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>New York</option>
                            <option value="NC" <?php if (!(strcmp("NC", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>North Carolina</option>
                            <option value="ND" <?php if (!(strcmp("ND", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>North Dakota</option>
                            <option value="OH" <?php if (!(strcmp("OH", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Ohio</option>
                            <option value="OK" <?php if (!(strcmp("OK", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Oklahoma</option>
                            <option value="OR" <?php if (!(strcmp("OR", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Oregon</option>
                            <option value="PA" <?php if (!(strcmp("PA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Pennsylvania</option>
                            <option value="RI" <?php if (!(strcmp("RI", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Rhode Island</option>
                            <option value="SC" <?php if (!(strcmp("SC", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>South Carolina</option>
                            <option value="SD" <?php if (!(strcmp("SD", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>South Dakota</option>
                            <option value="TN" <?php if (!(strcmp("TN", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Tennessee</option>
                            <option value="TX" <?php if (!(strcmp("TX", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Texas</option>
                            <option value="UT" <?php if (!(strcmp("UT", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Utah</option>
                            <option value="VT" <?php if (!(strcmp("VT", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Vermont</option>
                            <option value="VA" <?php if (!(strcmp("VA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Virginia</option>
                            <option value="WA" <?php if (!(strcmp("WA", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Washington</option>
                            <option value="WV" <?php if (!(strcmp("WV", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>West Virginia</option>
                            <option value="WI" <?php if (!(strcmp("WI", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Wisconsin</option>
                            <option value="WY" <?php if (!(strcmp("WY", $row_contact['contact_state']))) {echo "selected=\"selected\"";} ?>>Wyoming</option>
                        </select></td>
                      <td width="34%">Zip<br />
                          <input name="contact_zip" type="text" id="contact_zip" value="<?php echo $row_contact['contact_zip']; ?>" size="10" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="39%">Phone<br />
                          <input name="contact_phone" type="text" id="contact_phone" value="<?php echo $row_contact['contact_phone']; ?>" size="35" /></td>
                      <td width="61%">Cell<br />
                          <input name="contact_cell" type="text" id="contact_cell" value="<?php echo $row_contact['contact_cell']; ?>" size="35" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td>Image<br />
                    <input name="image" type="file" id="image" /><?php if ($row_contact['contact_image']) { ?>
                <br />
                <img src="images/<?php echo $row_contact['contact_image']; ?>" width="95" />
<?php } ?></td>
              </tr>
              <tr>
                <td>Website<br />
                    <input name="contact_web" type="text" id="contact_web" value="<?php echo $row_contact['contact_web']; ?>" size="45" /></td>
              </tr>
              <tr>
                <td>Background/Profile<br />
                    <textarea name="contact_profile" cols="60" rows="3" id="contact_profile"><?php echo $row_contact['contact_profile']; ?></textarea></td>
              </tr>
            </table>  
</div>          
          <p>&nbsp;</p></td>
        </tr>

        <tr>
          <td colspan="2">Tags<br />
          <input name="contact_tags" type="text" id="contact_tags" value="<?php echo $row_contact['contact_tags']; ?>" size="45" /></td>
        </tr>
        <tr>
          <td colspan="2"><p>
            <input type="submit" name="Submit2" value="<?php echo $update==1 ? 'Update' : 'Add'; ?> contact" />
            <input type="hidden" name="MM_insert" value="form1" />
            <input name="contact_id" type="hidden" id="contact_id" value="<?php echo $row_contact['contact_id']; ?>" />
            <input name="image_location" type="hidden" id="image_location" value="<?php echo $row_contact['contact_image']; ?>" />
          </p></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <input type="hidden" name="MM_update" value="form1">
    </form>
  </div>
  <?php include('includes/right-column.php'); ?>

  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
