<?php require_once('includes/config.php'); ?><?php require_once('includes/config.php'); 
include('includes/sc-includes.php');
$pagetitle = Dashboard;

if (empty($_GET['s']) && isset($_GET['s'])) {
header('Location: '.$_SERVER['HTTP_REFERER']); die;
}

$cwhere = "WHERE history_status = 1";
if (isset($_GET['s'])) {
$cwhere = "WHERE history_status = 1 AND (contact_tags LIKE '%".$_GET['s']."%' OR contact_first LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR contact_last LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR contact_email LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR contact_company LIKE '%".mysql_real_escape_string($_GET['s'])."%')";
}

$nwhere = "";
if (isset($_GET['s'])) {
$search = 1;
$nwhere = "WHERE note_text LIKE '%".mysql_real_escape_string($_GET['s'])."%' ";
}


mysql_select_db($database_contacts, $contacts);
$query_notes = "SELECT * FROM notes INNER JOIN contacts ON note_contact = contact_id $nwhere ORDER BY note_date DESC LIMIT 0, 20";
$notes = mysql_query($query_notes, $contacts) or die(mysql_error());
$row_notes = mysql_fetch_assoc($notes);
$totalRows_notes = mysql_num_rows($notes);

mysql_select_db($database_contacts, $contacts);
$query_contacts = "SELECT * FROM history INNER JOIN contacts ON contact_id = history_contact $cwhere ORDER BY history_date DESC LIMIT 0, 20";
$contacts = mysql_query($query_contacts, $contacts) or die(mysql_error());
$row_contacts = mysql_fetch_assoc($contacts);
$totalRows_contacts = mysql_num_rows($contacts);

if ($totalRows_contacts < 1 && !isset($_GET['s'])) { header('Location: contact.php'); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $pagetitle; ?></title>
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
<?php if ($search==1) { ?>
Search results for <em><?php echo $_GET['s']; ?></em>.<br />
<br />
<?php } ?>

<?php if ($totalRows_contacts > 0) { ?>
    <h2>Contacts</h2>
    <?php $i = 1; do { ?>
        <a href="contact-details.php?id=<?php echo $row_contacts['contact_id']; ?>"><?php echo $row_contacts['contact_first']; ?> <?php echo $row_contacts['contact_last']; ?></a>          <?php if ($totalRows_contacts!=$i) { ?>,<?php } ?> 
      <?php $i++; } while ($row_contacts = mysql_fetch_assoc($contacts)); ?>
<hr />
<?php } ?>

<?php if ($totalRows_notes > 0) { ?>
      <h2> Notes  </h2>

    <?php $i = 1; do { ?>
<div <?php if ($row_notes['note_date'] > time()-1) { ?>id="newnote"<?php } ?>>
        <span class="datedisplay"><a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>&note=<?php echo $row_notes['note_id']; ?>"><?php echo date('F d, Y', $row_notes['note_date']); ?></a></span> for <a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>"><?php echo $row_notes['contact_first']; ?> <?php echo $row_notes['contact_last']; ?></a><br />
          <?php echo $row_notes['note_text']; ?>
</div>
          <?php if ($totalRows_notes!=$i) { ?><hr /><?php } ?>
              <?php $i++;  } while ($row_notes = mysql_fetch_assoc($notes)); ?>
<?php } ?>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
