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
include('includes/donation.inc.php');
$pagetitle = "Dashboard";

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

$default_campaign = get_default_campaign();
if($default_campaign) {
  $stats = donation_stats($default_campaign['campaign_id']);
}

if ($totalRows_contacts < 1 && !isset($_GET['s'])) { header('Location: contact.php'); }
?>
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
        <a href="contact-details.php?id=<?php echo $row_contacts['contact_id']; ?>">
        <?php echo display_name($row_contacts); ?></a><?php if ($totalRows_contacts!=$i) { ?>,<?php } ?> 
      <?php $i++; } while ($row_contacts = mysql_fetch_assoc($contacts)); ?>
<hr />
<?php } ?>

<?php if ($totalRows_notes > 0) { ?>
      <h2> Notes  </h2>

    <?php $i = 1; do { ?>
<div <?php if ($row_notes['note_date'] > time()-1) { ?>id="newnote"<?php } ?>>
        <span class="datedisplay"><a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>&note=<?php echo $row_notes['note_id']; ?>"><?php echo date('F d, Y g:mA', $row_notes['note_date']); ?></a></span> for <a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>"><?php echo $row_notes['contact_first']; ?> <?php echo $row_notes['contact_last']; ?></a><br />
          <?php echo $row_notes['note_text']; ?>
</div>
          <?php if ($totalRows_notes!=$i) { ?><hr /><?php } ?>
              <?php $i++;  } while ($row_notes = mysql_fetch_assoc($notes)); ?>
    <hr />
<?php } ?>
<?php if($stats) { ?>
    <h2>Donations - <?php echo $default_campaign['campaign_name'] ?></h2>
    <div class="unitx2">
      <div class="unitx1 column first">Expected</div>
      <div class="unitx1 column align-right">$<?php echo $stats->expected ?></div>
      <div class="unitx1 column first">Pledged</div>
      <div class="unitx1 column align-right">$<?php echo $stats->pledged ?></div>
      <div class="unitx1 column first">Received</div>
      <div class="unitx1 column align-right">$<?php echo $stats->received ?></div>
      <div class="unitx1 column first">Total</div>
      <div class="unitx1 column align-right">$<?php echo $stats->total ?></div>
    </div>
<?php } ?>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
