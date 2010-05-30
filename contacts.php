<?php require_once('includes/config.php');
include('includes/sc-includes.php');
$pagetitle = Contact;

//SORTING
$name = "name_up";
if (isset($_GET['name_up'])) {
$sorder = "ORDER BY contact_last ASC";
$name = "name_down";
} elseif (isset($_GET['name_down'])) {
$sorder = "ORDER BY contact_last DESC";
}

$email = "email_up";
if (isset($_GET['email_up'])) {
$sorder = "ORDER BY contact_email ASC";
$email = "email_down";
} elseif (isset($_GET['email_down'])) {
$sorder = "ORDER BY contact_email DESC";
}

$phone = "phone_up";
if (isset($_GET['phone_up'])) {
$sorder = "ORDER BY contact_phone ASC";
$phone = "phone_down";
} elseif (isset($_GET['email_phone'])) {
$sorder = "ORDER BY contact_phone DESC";
}
//END SORTING

mysql_select_db($database_contacts, $contacts);
$query_contacts = "SELECT * FROM contacts $sorder";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$row_contacts = mysql_fetch_assoc($contacts);
$totalRows_contacts = mysql_num_rows($contacts);

if ($totalRows_contacts < 1) { 
header('Location: contact.php');
}

//delete multiple contacts
if (isset($_POST['d'])) {
	foreach($_POST['d'] as $key => $value) {
		if ($value) {
			mysql_query("DELETE FROM contacts WHERE contact_id = ".$value."");
		}
		
	}
set_msg('Contacts Deleted');
header('Location: contacts.php'); die;
}
//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $pagetitle; ?>s</title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>

<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Contacts</h2>
<?php if ($totalRows_contacts > 0) { ?>
    <form id="form1" name="form1" method="post" action="">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="4" align="right"><a href="csv.php"><strong>Export</strong></a><strong> | </strong><a href="batch.php"><strong>Import</strong></a> </td>
        </tr>
        <tr>
          <td colspan="4"><span class="notices" id="notice" style="display:<?php echo $dis; ?>">
            <?php display_msg(); ?>
          </span></td>
        </tr>
        <tr>
          <th width="26%"  style="padding-left:5px"><a href="?<?php echo $name; ?>">Name</a></th>
          <th width="27%"><a href="?<?php echo $phone; ?>">Phone</a></th>
          <th width="40%"><a href="?<?php echo $email; ?>">Email</a></th>
          <th width="7%">Delete</th>
        </tr>

  <?php do { $row_count++; ?>
        <tr <?php if ($row_count%2) { ?>bgcolor="#F4F4F4"<?php } ?>>
          <td style="padding-left:5px"><a href="contact-details.php?id=<?php echo $row_contacts['contact_id']; ?>"><?php echo $row_contacts['contact_first']; ?> <?php echo $row_contacts['contact_last']; ?></a></td>
          <td><?php echo $row_contacts[contact_phone] ? $row_contacts['contact_phone'] : $na; ?></td>
          <td><a href="mailto:<?php echo $row_contacts['contact_email']; ?>"><?php echo $row_contacts['contact_email']; ?></a></td>
          <td>
            <input name="d[<?php echo $row_contacts['contact_id']; ?>]" type="checkbox" id="d[<?php echo $row_contacts['contact_id']; ?>]" value="<?php echo $row_contacts['contact_id']; ?>" />
          </td>
        </tr>
        <?php } while ($row_contacts = mysql_fetch_assoc($contacts)); ?>

      
        <tr>
          <td style="padding-left:5px">&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan="2" align="right"><input type="submit" name="Submit" value="Submit" /></td>
        </tr>

      </table>
    </form>
    <?php } ?>



  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
