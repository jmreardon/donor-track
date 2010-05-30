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

if ($update==0) { 
  $title_text = "Add Contact"; 
} else {
  $title_text = $row_contact['contact_first'] . " " . $row_contact['contact_last'];
}
?>
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
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr><td>Street<br />
                    <input name="contact_street" type="text" id="contact_street" value="<?php echo $row_contact['contact_street']; ?>" size="35" /></td>
                    <td width="61%">City<br />
                      <input name="contact_city" type="text" id="contact_city" value="<?php echo $row_contact['contact_city']; ?>" size="35" /></td>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="27%" valign="top">Province/State<br />
			  <input name="contact_state" type="text" id="contact_state" value="<?php echo $row_contact['contact_state']; ?>" size="35" />
                      </td>
                      <td>Postal Code<br />
                          <input name="contact_zip" type="text" id="contact_zip" value="<?php echo $row_contact['contact_zip']; ?>" size="10" /></td>
                      <td>Country<br />
                          <input name="contact_country" type="text" id="contact_country" value="<?php echo $row_contact['contact_country']; ?>" size="10" /></td>
                    </tr>
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
