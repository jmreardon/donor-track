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
$pagetitle = "Contact";

$update = 0;
if (isset($_GET['id'])) {
$update = 1;
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
  mysql_query("INSERT INTO contacts (contact_first, contact_last, contact_title, contact_image, contact_profile, contact_company, contact_street, contact_city, contact_state, contact_zip, contact_phone, contact_cell, contact_email, contact_web, contact_updated) VALUES 

	(
		".GetSQLValueString(trim($_POST['contact_first'])).",
		".GetSQLValueString(trim($_POST['contact_last'])).",
		".GetSQLValueString(trim($_POST['contact_title'])).",
		".GetSQLValueString($picture).",
		".GetSQLValueString(trim($_POST['contact_profile'])).",
		".GetSQLValueString(trim($_POST['contact_company'])).",
		".GetSQLValueString(trim($_POST['contact_street'])).",
		".GetSQLValueString(trim($_POST['contact_city'])).",
		".GetSQLValueString(trim($_POST['contact_state'])).",
		".GetSQLValueString(trim($_POST['contact_zip'])).",
		".GetSQLValueString(trim($_POST['contact_phone'])).",
		".GetSQLValueString(trim($_POST['contact_cell'])).",
		".GetSQLValueString(trim($_POST['contact_email'])).",
		".GetSQLValueString(trim($_POST['contact_web'])).",
		'".time()."'
	)

	") or die(mysql_error());

$cid = mysql_insert_id();

	set_msg('Contact Added');
	$redirect = "contact-details.php?id=$cid";
	header('Location: '.$redirect); die;
}
}

if ($update==1) {
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE contacts SET contact_first=%s, contact_last=%s, contact_title=%s, contact_image=%s, contact_profile=%s, contact_company=%s, contact_street=%s, contact_city=%s, contact_state=%s, contact_zip=%s, contact_phone=%s, contact_cell=%s, contact_email=%s, contact_web=%s, contact_updated=%s WHERE contact_id=%s",

                       GetSQLValueString(trim($_POST['contact_first']), "text"),
                       GetSQLValueString(trim($_POST['contact_last']), "text"),
                       GetSQLValueString(trim($_POST['contact_title']), "text"),
                       GetSQLValueString($picture, "text"),
                       GetSQLValueString(trim($_POST['contact_profile']), "text"),
                       GetSQLValueString(trim($_POST['contact_company']), "text"),
                       GetSQLValueString(trim($_POST['contact_street']), "text"),
                       GetSQLValueString(trim($_POST['contact_city']), "text"),
                       GetSQLValueString(trim($_POST['contact_state']), "text"),
                       GetSQLValueString(trim($_POST['contact_zip']), "text"),
                       GetSQLValueString(trim($_POST['contact_phone']), "text"),
                       GetSQLValueString(trim($_POST['contact_cell']), "text"),
                       GetSQLValueString(trim($_POST['contact_email']), "text"),
                       GetSQLValueString(trim($_POST['contact_web']), "text"),
                       GetSQLValueString(trim($_POST['contact_updated']), "int"),
                       GetSQLValueString(trim($_POST['contact_id']), "int"));

  mysql_select_db($database_contacts, $contacts);
  $Result1 = mysql_query($updateSQL, $contacts) or die(mysql_error());
	$pid = $_GET['id'];

	set_msg('Contact Updated');
	$cid = $_GET['id'];
	$redirect = "contact-details.php?id=$cid";
	header('Location: '.$redirect); die;
}
}

if ($update==0) { 
  $title_text = "Add Contact"; 
  $back_track = array('title' => "Contacts", 'url' => "contacts.php");
} else {
  $title_text = display_name($row_contact);
  $back_track = array('title' => $title_text, 'url' => "contact-details.php?id=" . $row_contact['contact_id']);
}
?>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2><?php if ($update==1) { echo 'Update'; } else { echo 'Add'; } ?> Contact </h2>
    <p>&nbsp;</p>
    <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="form1" id="form1">
    <fieldset class="first unitx4">
      <label class="first unitx2">
      First Name
      <input name="contact_first" type="text" class="" id="contact_first" value="<?php echo $row_contact['contact_first']; ?>" />
      </label>
      <label class="unitx2">
      Last Name
      <input name="contact_last" type="text" class="" id="contact_last" value="<?php echo $row_contact['contact_last']; ?>" />
      </label>
      <label class="first unitx2">
      Title
      <input name="contact_title" type="text" id="contact_title" value="<?php echo $row_contact['contact_title']; ?>"  />
      </label>
      <label class="unitx2">
      Organization
      <input name="contact_company" type="text" id="contact_company" value="<?php echo $row_contact['contact_company']; ?>" />
      </label>
      <label class="first unitx4">
      Email
      <input name="contact_email" type="text" class="validate-email" id="contact_email" value="<?php echo $row_contact['contact_email']; ?>" />
      </label>
    </fieldset>
      <hr />
     <?php if ($update!=1) { ?>   <p><a href="#" onclick="new Effect.toggle('morecontact', 'slide'); return false;">+Add more contact information </a></p><?php } ?>

<div <?php if ($update!=1) { ?>id="morecontact" style="display:none"<?php } ?>>
    <fieldset class="first unitx4">
      <label class="first unitx2">
      Street
      <input name="contact_street" type="text" id="contact_street" value="<?php echo $row_contact['contact_street']; ?>" />
      </label>
      <label class="unitx2">
      City
      <input name="contact_city" type="text" id="contact_city" value="<?php echo $row_contact['contact_city']; ?>" />
      </label>
      <label class="first unitx1">
      Province/State
      <input name="contact_state" type="text" id="contact_state" value="<?php echo $row_contact['contact_state']; ?>" />
      </label>
      <label class="unitx1">
      Postal Code
      <input name="contact_zip" type="text" id="contact_zip" value="<?php echo $row_contact['contact_zip']; ?>" />
      </label>
      <label class="unitx2">
      Country
      <input name="contact_country" type="text" id="contact_country" value="<?php echo $row_contact['contact_country']; ?>" />
      </label>
      <label class="first unitx2">
      Phone
      <input name="contact_phone" type="text" id="contact_phone" value="<?php echo $row_contact['contact_phone']; ?>" /></td>
      </label>
      <label class="unitx2">
      Cell
      <input name="contact_cell" type="text" id="contact_cell" value="<?php echo $row_contact['contact_cell']; ?>" />
      </label>
      <label class="first unitx4">
      Image
      <input name="image" type="file" id="image" /><?php if ($row_contact['contact_image']) { ?>
            <br />
            <img src="images/<?php echo $row_contact['contact_image']; ?>" width="95" />
<?php } ?>
      </label>
      <label class="first unitx4">
      Website
      <input name="contact_web" type="text" id="contact_web" value="<?php echo $row_contact['contact_web']; ?>" />
      </label>
      <label class="first unitx4">
      Background/Profile
      <textarea name="contact_profile" cols="60" rows="3" id="contact_profile">
        <?php echo $row_contact['contact_profile']; ?>
      </textarea>
      </label>
    </fieldset>
    </div>
    <fieldset>
      <input type="submit" name="Submit2" value="<?php echo $update==1 ? 'Update' : 'Add'; ?> contact" />
    </fieldset>
      <input type="hidden" name="MM_insert" value="form1" />
      <input name="contact_id" type="hidden" id="contact_id" value="<?php echo $row_contact['contact_id']; ?>" />
      <input name="image_location" type="hidden" id="image_location" value="<?php echo $row_contact['contact_image']; ?>" />
      <input type="hidden" name="MM_update" value="form1">
    </form>
  </div>
  <?php include('includes/right-column.php'); ?>

  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
