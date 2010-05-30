<?php
$query_history = "SELECT * FROM history WHERE history_status = 1 ORDER BY history_date DESC LIMIT 0, 4";
$history = mysql_query($query_history) or die(mysql_error());
$row_history = mysql_fetch_assoc($history);
$totalRows_history = mysql_num_rows($history);

?>
<link href="simplecustomer.css" rel="stylesheet" type="text/css" />
<div class="headercontainer"> 
  <div class="header">
    <h1>Simple Customer</h1>
  <a href="index.php" class="menubuttons <?php if ($pagetitle==Dashboard) { echo menubuttonsactive; } ?>">Dashboard</a><a href="contacts.php" class="menubuttons <?php if ($pagetitle==Contact || $pagetitle==ContactDetails) { echo menubuttonsactive; } ?>">Contacts</a><span class="headerright">Logged in as <?php echo $row_userinfo['user_email']; ?> | <a href="logout.php">Log Out</a> | <a href="profile.php">Update Profile</a> </span><br clear="all" />
  </div>
  </div>

<?php if ($totalRows_history > 0) { ?>
<div class="historycontainer">Recent: 
    <?php $ih = 1; do { 
//GET CONTACT INFO FROM HISTORY
mysql_select_db($database_contacts);
$query_histcont = "SELECT * FROM contacts WHERE contact_id = ".$row_history['history_contact']."";
$histcont = mysql_query($query_histcont) or die(mysql_error());
$row_histcont = mysql_fetch_assoc($histcont);
//
?>
    <a href="contact-details.php?id=<?php echo $row_histcont['contact_id']; ?>"><?php echo $row_histcont['contact_first']; ?> <?php echo $row_histcont['contact_last']; ?></a> <?php if ($totalRows_history!=$ih) {?> &middot; <?php } ?>
      <?php $ih++; } while ($row_history = mysql_fetch_assoc($history)); ?></div>
<?php } ?>